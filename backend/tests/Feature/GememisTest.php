<?php

namespace Tests\Feature;

use App\Models\Allocation;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Exhibition;
use App\Models\Exhibitor;
use App\Models\Invoice;
use App\Models\Space;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use LogicException;
use Tests\TestCase;

/**
 * Test cases are numbered after the requirement they prove (docs/test-plan.md,
 * FRD §25 traceability matrix): TC-010 proves FR-010, and so on.
 */
class GememisTest extends TestCase
{
    use RefreshDatabase;

    private Exhibition $exhibition;

    private array $spaces = [];

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        // These tests cover the manual payment flow; GePG has its own (GepgTest).
        config(['gepg.mode' => 'off', 'gememis.manual_payments' => 'exception']);

        $this->exhibition = Exhibition::create([
            'code' => 'T27', 'name' => 'Test Expo', 'venue' => 'Geita', 'start_date' => now()->addDays(60), 'end_date' => now()->addDays(65),
            'registration_opens_on' => now()->subDay(), 'registration_closes_on' => now()->addDays(30), 'payment_deadline' => now()->addDays(40),
            'required_documents' => ['Business licence'], 'status' => 'published', 'vat_rate' => 18,
        ]);
        $hall = $this->exhibition->halls()->create(['code' => 'A', 'name' => 'Hall A']);
        $category = $this->exhibition->spaceCategories()->create(['code' => 'STD', 'name' => 'Standard booth', 'price_per_sqm' => 10000]);
        foreach (['A01', 'A02', 'A03'] as $i => $number) {
            $this->spaces[$number] = $this->exhibition->spaces()->create([
                'hall_id' => $hall->id, 'space_category_id' => $category->id, 'number' => $number,
                'width_m' => 3, 'length_m' => 3, 'area_sqm' => 9, 'price' => 90000, 'map_col' => $i + 1,
            ])->refresh();
        }
    }

    private function staff(string $role): User
    {
        return User::firstOrCreate(['email' => "$role@test.tz"], ['name' => ucfirst($role), 'password' => 'Secret#12345', 'role' => $role])->refresh();
    }

    /** The applicant details an exhibitor gives with an application, as a registered business. */
    private function business(): array
    {
        return ['entity_type' => 'business', 'organization_name' => 'Acme Mining', 'registration_number' => 'BRELA-1', 'contact_person' => 'Jane',
            'phone' => '+255700000000', 'country' => 'Tanzania'];
    }

    private function exhibitorUser(string $name = 'Acme Mining'): User
    {
        $exhibitor = Exhibitor::create(['organization_name' => $name, 'contact_person' => 'Jane', 'phone' => '+255700000000', 'email' => str($name)->slug().'@test.tz']);

        return User::create(['name' => 'Jane', 'email' => str($name)->slug().'@user.tz', 'password' => 'Secret#12345', 'role' => 'exhibitor', 'exhibitor_id' => $exhibitor->id])->refresh();
    }

    /** Drives an application through the API to "approved". */
    private function approvedApplication(User $exhibitor, int $spaces = 1): Application
    {
        Sanctum::actingAs($exhibitor);
        $id = $this->postJson('/api/v1/applications', ['exhibition_id' => $this->exhibition->id, 'spaces_requested' => $spaces, 'product_category' => 'Drills'] + $this->business())
            ->assertCreated()->json('id');
        $this->post("/api/v1/applications/$id/documents", ['type' => 'Business licence', 'file' => UploadedFile::fake()->create('licence.pdf', 20, 'application/pdf')])->assertCreated();
        $this->postJson("/api/v1/applications/$id/submit")->assertOk();

        Sanctum::actingAs($this->staff('registration_officer'));
        $this->postJson("/api/v1/applications/$id/review", ['decision' => 'approve'])->assertOk();

        return Application::find($id);
    }

    public function test_tc001_exhibition_can_be_created_by_authorised_roles_only(): void
    {
        $payload = ['code' => 'NEW27', 'name' => 'New Expo', 'venue' => 'Geita', 'start_date' => '2027-09-10', 'end_date' => '2027-09-15',
            'registration_opens_on' => '2027-05-01', 'registration_closes_on' => '2027-08-01', 'payment_deadline' => '2027-08-20'];

        Sanctum::actingAs($this->staff('finance_officer'));
        $this->postJson('/api/v1/exhibitions', $payload)->assertForbidden(); // BR-009

        Sanctum::actingAs($this->staff('exhibition_admin'));
        $this->postJson('/api/v1/exhibitions', $payload)->assertCreated()->assertJsonPath('status', 'draft');
        $this->assertDatabaseHas('audit_logs', ['action' => 'exhibition.created', 'reference' => 'NEW27']);
    }

    public function test_tc005_space_is_priced_from_its_category_rate(): void
    {
        Sanctum::actingAs($this->staff('space_officer'));
        $this->postJson("/api/v1/exhibitions/{$this->exhibition->id}/spaces", [
            'hall_id' => $this->spaces['A01']->hall_id, 'space_category_id' => $this->spaces['A01']->space_category_id,
            'number' => 'A09', 'width_m' => 3, 'length_m' => 6,
        ])->assertCreated()->assertJsonPath('area_sqm', 18)->assertJsonPath('price', 180000);
    }

    public function test_tc014_application_cannot_be_submitted_without_mandatory_documents(): void
    {
        Sanctum::actingAs($this->exhibitorUser());
        $id = $this->postJson('/api/v1/applications', ['exhibition_id' => $this->exhibition->id, 'spaces_requested' => 1, 'product_category' => 'Drills'] + $this->business())->assertCreated()->json('id');
        $this->postJson("/api/v1/applications/$id/submit")->assertStatus(422)->assertJsonFragment(['message' => 'Upload the required documents before submitting: Business licence.']);
    }

    public function test_tc010_a_space_cannot_be_allocated_twice(): void
    {
        $first = $this->approvedApplication($this->exhibitorUser('First Co'));
        $second = $this->approvedApplication($this->exhibitorUser('Second Co'));

        Sanctum::actingAs($this->staff('space_officer'));
        $this->postJson('/api/v1/allocations', ['application_id' => $first->id, 'space_id' => $this->spaces['A01']->id])->assertCreated();
        $this->postJson('/api/v1/allocations', ['application_id' => $second->id, 'space_id' => $this->spaces['A01']->id])
            ->assertStatus(422)->assertJsonPath('rule', 'BR-005');

        // Even with the application-level check bypassed, the database refuses.
        $this->expectException(UniqueConstraintViolationException::class);
        Allocation::create(['reference' => 'X', 'application_id' => $second->id, 'exhibition_id' => $this->exhibition->id,
            'exhibitor_id' => $second->exhibitor_id, 'space_id' => $this->spaces['A01']->id, 'active_space_id' => $this->spaces['A01']->id]);
    }

    public function test_tc024_only_approved_applications_are_allocated_and_an_invoice_follows(): void
    {
        $exhibitor = $this->exhibitorUser();
        Sanctum::actingAs($exhibitor);
        $draft = $this->postJson('/api/v1/applications', ['exhibition_id' => $this->exhibition->id, 'spaces_requested' => 1, 'product_category' => 'Drills'] + $this->business())->json('id');

        Sanctum::actingAs($officer = $this->staff('space_officer'));
        $this->postJson('/api/v1/allocations', ['application_id' => $draft, 'space_id' => $this->spaces['A01']->id])->assertStatus(422)->assertJsonPath('rule', 'BR-002');

        Application::find($draft)->update(['status' => 'cancelled']);
        $application = $this->approvedApplication($this->exhibitorUser('Other Co'));
        Sanctum::actingAs($officer);
        $this->postJson('/api/v1/allocations', ['application_id' => $application->id, 'space_id' => $this->spaces['A02']->id])->assertCreated();

        // TC-031: 90,000 + 18% VAT
        $invoice = Invoice::where('application_id', $application->id)->firstOrFail();
        $this->assertSame(90000.0, $invoice->subtotal);
        $this->assertSame(16200.0, $invoice->vat_amount);
        $this->assertSame(106200.0, $invoice->total);
        $this->assertSame('unpaid', $invoice->status);
        $this->assertSame('pending_payment', $application->fresh()->status);
        $this->assertSame('allocated', $this->spaces['A02']->fresh()->status);
    }

    public function test_tc034_verified_payment_settles_invoice_issues_receipt_and_confirms_participation(): void
    {
        $exhibitor = $this->exhibitorUser();
        $application = $this->approvedApplication($exhibitor);
        Sanctum::actingAs($this->staff('space_officer'));
        $this->postJson('/api/v1/allocations', ['application_id' => $application->id, 'space_id' => $this->spaces['A01']->id])->assertCreated();
        $invoice = Invoice::firstOrFail();

        Sanctum::actingAs($exhibitor);
        $pay = fn ($amount, $ref) => $this->post("/api/v1/invoices/{$invoice->id}/payments", ['amount' => $amount, 'paid_on' => now()->toDateString(), 'method' => 'bank',
            'payer_reference' => $ref, 'evidence' => UploadedFile::fake()->create('slip.pdf', 20, 'application/pdf')], ['Accept' => 'application/json']);
        $first = $pay(50000, 'TXN-1')->assertCreated()->json('id');
        $pay(50000, 'TXN-1')->assertStatus(422); // same slip twice
        $this->postJson("/api/v1/payments/$first/verify", ['decision' => 'verify'])->assertForbidden(); // exhibitors cannot verify

        Sanctum::actingAs($finance = $this->staff('finance_officer'));
        $this->postJson("/api/v1/payments/$first/verify", ['decision' => 'verify'])->assertOk()->assertJsonPath('verification_status', 'verified');
        $this->assertSame('partially_paid', $invoice->fresh()->status);
        $this->assertSame('pending_payment', $application->fresh()->status);

        Sanctum::actingAs($exhibitor);
        $second = $pay(56200, 'TXN-2')->assertCreated()->json('id');
        Sanctum::actingAs($finance);
        $receipt = $this->postJson("/api/v1/payments/$second/verify", ['decision' => 'verify'])->assertOk()->json('receipt_number');

        $this->assertNotEmpty($receipt);
        $this->assertSame('paid', $invoice->fresh()->status);
        $this->assertSame('confirmed', $application->fresh()->status);
        $this->get("/api/v1/payments/$second/receipt")->assertOk()->assertHeader('content-type', 'application/pdf');
    }

    public function test_tc026_reallocation_needs_a_reason_and_cancellation_frees_the_space(): void
    {
        $application = $this->approvedApplication($this->exhibitorUser());
        Sanctum::actingAs($this->staff('space_officer'));
        $id = $this->postJson('/api/v1/allocations', ['application_id' => $application->id, 'space_id' => $this->spaces['A01']->id])->json('id');

        $this->postJson("/api/v1/allocations/$id/reallocate", ['space_id' => $this->spaces['A02']->id])->assertStatus(422)->assertJsonValidationErrors('reason');
        $new = $this->postJson("/api/v1/allocations/$id/reallocate", ['space_id' => $this->spaces['A02']->id, 'reason' => 'Closer to power supply'])->assertSuccessful()->json('id');

        $this->assertSame('available', $this->spaces['A01']->fresh()->status);
        $this->assertSame('allocated', $this->spaces['A02']->fresh()->status);
        $this->assertDatabaseHas('audit_logs', ['action' => 'allocation.reallocated']); // BR-007

        $this->postJson("/api/v1/allocations/$new/cancel", ['reason' => 'Exhibitor withdrew'])->assertOk();
        $this->assertSame('available', $this->spaces['A02']->fresh()->status);
        $this->assertSame('cancelled', Invoice::first()->status);
        $this->assertSame('approved', $application->fresh()->status);
    }

    public function test_tc047_exhibitors_see_only_their_own_records(): void
    {
        $application = $this->approvedApplication($this->exhibitorUser('Owner Co'));
        Sanctum::actingAs($this->exhibitorUser('Stranger Co'));

        $this->getJson("/api/v1/applications/{$application->id}")->assertForbidden();
        $this->getJson('/api/v1/applications')->assertOk()->assertJsonCount(0, 'data');
        $this->getJson('/api/v1/audit-logs')->assertForbidden();
        $this->getJson('/api/v1/users')->assertForbidden();
    }

    public function test_tc023_exhibitor_self_selection_waits_for_officer_confirmation(): void
    {
        $exhibitor = $this->exhibitorUser();
        $application = $this->approvedApplication($exhibitor);

        Sanctum::actingAs($exhibitor);
        $id = $this->postJson('/api/v1/allocations', ['application_id' => $application->id, 'space_id' => $this->spaces['A03']->id])->assertCreated()->assertJsonPath('status', 'pending')->json('id');
        $this->assertSame('pending_approval', $this->spaces['A03']->fresh()->status);
        $this->assertSame(0, Invoice::count());
        $this->postJson('/api/v1/allocations', ['application_id' => $application->id, 'space_id' => $this->spaces['A02']->id])->assertStatus(422); // asked for one space

        Sanctum::actingAs($this->staff('space_officer'));
        $this->postJson("/api/v1/allocations/$id/confirm")->assertOk();
        $this->assertSame('allocated', $this->spaces['A03']->fresh()->status);
        $this->assertSame(1, Invoice::count());
    }

    public function test_tc048_audit_trail_is_append_only(): void
    {
        Sanctum::actingAs($this->staff('exhibition_admin'));
        $this->postJson("/api/v1/exhibitions/{$this->exhibition->id}/transition", ['status' => 'registration_closed'])->assertOk();
        $log = AuditLog::where('action', 'exhibition.registration_closed')->firstOrFail();
        $this->assertSame(['status' => 'published'], $log->old_values);

        $this->expectException(LogicException::class);
        $log->update(['summary' => 'tampered']);
    }

    public function test_br010_registration_closes_automatically_after_the_deadline(): void
    {
        $this->exhibition->update(['registration_closes_on' => now()->subDay()]);
        Sanctum::actingAs($this->exhibitorUser());
        $this->postJson('/api/v1/applications', ['exhibition_id' => $this->exhibition->id, 'spaces_requested' => 1, 'product_category' => 'Drills'] + $this->business())->assertStatus(422);

        $this->artisan('gememis:housekeeping')->assertSuccessful();
        $this->assertSame('registration_closed', $this->exhibition->fresh()->status);
    }

    public function test_tc011_sign_up_asks_only_for_the_person(): void
    {
        $form = ['first_name' => 'Asha', 'last_name' => 'Mrisho', 'phone' => '+255700000001', 'email' => 'Asha@Example.tz',
            'password' => 'Secret#12345', 'password_confirmation' => 'Secret#12345'];

        $this->postJson('/api/v1/auth/register', $form)->assertStatus(422)->assertJsonValidationErrors('terms');
        $this->postJson('/api/v1/auth/register', $form + ['terms' => true])->assertCreated()
            ->assertJsonPath('user.name', 'Asha Mrisho')
            ->assertJsonPath('user.email', 'asha@example.tz')
            ->assertJsonPath('user.exhibitor.entity_type', null); // said when applying

        $this->assertNotNull(User::where('email', 'asha@example.tz')->value('terms_accepted_at'));
    }

    public function test_tc014_applying_says_individual_or_business_and_checks_the_details(): void
    {
        Sanctum::actingAs($user = $this->exhibitorUser());
        $apply = fn (array $who) => $this->postJson('/api/v1/applications', ['exhibition_id' => $this->exhibition->id, 'spaces_requested' => 1, 'product_category' => 'Gold'] + $who);

        $apply([])->assertStatus(422)->assertJsonValidationErrors('entity_type');
        // A business needs its name and registration number; the country must be one on the list.
        $apply(['entity_type' => 'business', 'phone' => '+255700000000', 'country' => 'Tanzana'])
            ->assertStatus(422)->assertJsonValidationErrors(['organization_name', 'registration_number', 'contact_person', 'country']);
        // An individual needs none of the business details, and is billed in their own name.
        $apply(['entity_type' => 'individual', 'phone' => '+255700000000', 'country' => 'Tanzania', 'tin' => '999'])->assertCreated();

        $exhibitor = $user->exhibitor->fresh();
        $this->assertSame('individual', $exhibitor->entity_type);
        $this->assertSame($user->name, $exhibitor->organization_name);
        $this->assertNull($exhibitor->tin);
    }

    public function test_tc014_individuals_have_their_own_mandatory_documents(): void
    {
        $this->exhibition->update(['required_documents_individual' => ['National ID']]);
        Sanctum::actingAs($this->exhibitorUser());
        $id = $this->postJson('/api/v1/applications', ['exhibition_id' => $this->exhibition->id, 'spaces_requested' => 1, 'product_category' => 'Gold',
            'entity_type' => 'individual', 'phone' => '+255700000000', 'country' => 'Tanzania'])->assertCreated()->json('id');

        // Not the business licence: an individual has none.
        $this->postJson("/api/v1/applications/$id/submit")->assertStatus(422)
            ->assertJsonFragment(['message' => 'Upload the required documents before submitting: National ID.']);
        $this->post("/api/v1/applications/$id/documents", ['type' => 'National ID', 'file' => UploadedFile::fake()->create('nida.pdf', 20, 'application/pdf')])->assertCreated();
        $this->postJson("/api/v1/applications/$id/submit")->assertOk();
    }

    /** Records a payment by hand as $who; returns the response. */
    private function recordManually(User $who, Invoice $invoice, array $overrides = [])
    {
        Sanctum::actingAs($who);

        return $this->post("/api/v1/invoices/{$invoice->id}/payments", $overrides + [
            'amount' => 106200, 'paid_on' => now()->toDateString(), 'method' => 'bank', 'payer_reference' => 'CRDB-'.uniqid(),
            'evidence' => UploadedFile::fake()->create('slip.pdf', 20, 'application/pdf'),
            'manual_reason' => 'Paid by direct transfer to the collection account before GePG went live.',
        ], ['Accept' => 'application/json']);
    }

    private function invoiceForSpace(): Invoice
    {
        $application = $this->approvedApplication($this->exhibitorUser());
        Sanctum::actingAs($this->staff('space_officer'));
        $this->postJson('/api/v1/allocations', ['application_id' => $application->id, 'space_id' => $this->spaces['A01']->id])->assertCreated();

        return Invoice::firstOrFail();
    }

    public function test_tc033_a_payment_recorded_by_hand_needs_a_second_person_to_verify_it(): void
    {
        $invoice = $this->invoiceForSpace();
        $maker = $this->staff('finance_officer');
        $checker = User::create(['name' => 'Second Finance', 'email' => 'finance2@test.tz', 'password' => 'Secret#12345', 'role' => 'finance_officer'])->refresh();
        $this->staff('management');
        $this->staff('auditor');

        $id = $this->recordManually($maker, $invoice)->assertCreated()->json('id');

        // The person who recorded it cannot accept it.
        $this->postJson("/api/v1/payments/$id/verify", ['decision' => 'verify'])->assertStatus(422)->assertJsonPath('rule', 'two-person rule');
        $this->assertSame('unpaid', $invoice->fresh()->status);

        Sanctum::actingAs($checker);
        $this->postJson("/api/v1/payments/$id/verify", ['decision' => 'verify'])->assertOk();
        $this->assertSame('paid', $invoice->fresh()->status);

        // Management and the Auditor hear about every manual acceptance.
        $this->assertSame(2, \App\Models\AppNotification::where('title', 'Manual payment accepted')->count());
        $this->assertDatabaseHas('audit_logs', ['action' => 'payment.verified_manual']);
    }

    public function test_tc033_the_system_administrator_cannot_handle_money(): void
    {
        $invoice = $this->invoiceForSpace();
        $admin = $this->staff('system_admin');

        $this->recordManually($admin, $invoice)->assertForbidden();

        $id = $this->recordManually($this->staff('finance_officer'), $invoice)->assertCreated()->json('id');
        Sanctum::actingAs($admin);
        $this->postJson("/api/v1/payments/$id/verify", ['decision' => 'verify'])->assertForbidden();
    }

    public function test_tc033_with_gepg_on_payments_go_through_the_control_number(): void
    {
        $invoice = $this->invoiceForSpace();
        config(['gepg.mode' => 'simulator']);
        $finance = $this->staff('finance_officer');

        // The exhibitor pays with the control number and cannot record a payment.
        $this->recordManually(User::where('role', 'exhibitor')->first(), $invoice, ['manual_reason' => null])->assertForbidden();

        // Finance may record an exception, but only with evidence and a reason, and never cash.
        $this->recordManually($finance, $invoice, ['manual_reason' => '', 'evidence' => null])->assertStatus(422)->assertJsonValidationErrors(['manual_reason', 'evidence']);
        $this->recordManually($finance, $invoice, ['method' => 'cash'])->assertStatus(422)->assertJsonValidationErrors('method');
        $this->recordManually($finance, $invoice)->assertCreated();

        // MANUAL_PAYMENTS=off: GePG only.
        config(['gememis.manual_payments' => 'off']);
        $this->recordManually($finance, $invoice)->assertForbidden();
    }

    public function test_tc012_account_locks_after_repeated_failed_sign_ins(): void
    {
        $user = $this->staff('auditor');
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'wrong'])->assertStatus(422);
        }
        $this->assertNotNull($user->fresh()->locked_at);
        $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'Secret#12345'])->assertStatus(422);
    }
}
