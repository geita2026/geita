<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Exhibition;
use App\Models\Exhibitor;
use App\Models\GepgBill;
use App\Models\GepgMessage;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Space;
use App\Models\User;
use App\Services\Gepg\GepgService;
use App\Services\Gepg\GepgSigner;
use App\Services\Gepg\GepgXml;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/** FR-059 / BR-008: GePG bills, control numbers, payments and reconciliation, against the simulator. */
class GepgTest extends TestCase
{
    use RefreshDatabase;

    private Exhibition $exhibition;

    /** @var array<string, Space> */
    private array $spaces = [];

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        config(['gepg.mode' => 'simulator', 'gepg.payment_option' => 2]);

        $this->exhibition = Exhibition::create([
            'code' => 'T27', 'name' => 'Test Expo', 'venue' => 'Geita', 'start_date' => now()->addDays(60), 'end_date' => now()->addDays(65),
            'registration_opens_on' => now()->subDay(), 'registration_closes_on' => now()->addDays(30), 'payment_deadline' => now()->addDays(40),
            'required_documents' => [], 'status' => 'published', 'vat_rate' => 18,
        ]);
        $hall = $this->exhibition->halls()->create(['code' => 'A', 'name' => 'Hall A']);
        $std = $this->exhibition->spaceCategories()->create(['code' => 'STD', 'name' => 'Standard', 'price_per_sqm' => 10000, 'gfs_code' => '140201']);
        $prm = $this->exhibition->spaceCategories()->create(['code' => 'PRM', 'name' => 'Premium', 'price_per_sqm' => 20000, 'gfs_code' => '140202']);
        foreach (['A01' => [$std, 90000], 'A02' => [$std, 90000], 'A03' => [$prm, 180000]] as $number => [$category, $price]) {
            $this->spaces[$number] = $this->exhibition->spaces()->create([
                'hall_id' => $hall->id, 'space_category_id' => $category->id, 'number' => $number,
                'width_m' => 3, 'length_m' => 3, 'area_sqm' => 9, 'price' => $price,
            ])->refresh();
        }
    }

    private function officer(): User
    {
        return User::firstOrCreate(['email' => 'spaces@test.tz'], ['name' => 'Officer', 'password' => 'Secret#12345', 'role' => 'space_officer'])->refresh();
    }

    private function finance(): User
    {
        return User::firstOrCreate(['email' => 'finance@test.tz'], ['name' => 'Finance', 'password' => 'Secret#12345', 'role' => 'finance_officer'])->refresh();
    }

    /** An approved application with one space allocated: the invoice exists and GePG has been asked. */
    private function allocated(string $space = 'A01', int $spaces = 1): Invoice
    {
        $exhibitor = Exhibitor::create(['organization_name' => 'Acme Mining', 'contact_person' => 'Jane', 'phone' => '+255 700 000 001', 'email' => 'acme@test.tz']);
        User::create(['name' => 'Jane', 'email' => 'jane@acme.tz', 'password' => 'Secret#12345', 'role' => 'exhibitor', 'exhibitor_id' => $exhibitor->id, 'phone' => '+255700000001']);
        $application = Application::create([
            'reference' => 'APP-T27-1', 'exhibition_id' => $this->exhibition->id, 'exhibitor_id' => $exhibitor->id,
            'spaces_requested' => $spaces, 'product_category' => 'Drills', 'status' => 'approved',
        ]);
        Sanctum::actingAs($this->officer());
        $this->postJson('/api/v1/allocations', ['application_id' => $application->id, 'space_id' => $this->spaces[$space]->id])->assertCreated();

        return Invoice::where('application_id', $application->id)->firstOrFail();
    }

    /** A message as GePG would post it, signed with the simulator's GePG key. */
    private function fromGepg(string $content): string
    {
        return app(GepgXml::class)->envelope($content, asGepg: true);
    }

    public function test_allocation_sends_a_signed_bill_and_the_control_number_reaches_the_invoice(): void
    {
        $invoice = $this->allocated()->refresh();
        $bill = GepgBill::sole();

        $this->assertSame('issued', $bill->status);
        $this->assertSame(106200.0, $bill->amount); // 90,000 + 18% VAT
        $this->assertMatchesRegularExpression('/^\d{12}$/', $bill->control_number);
        $this->assertSame($bill->control_number, $invoice->control_number);
        $this->assertSame('issued', $invoice->gepg_status);

        // The request carried GeMEMIS's signature over the exact content, and the bill lines add up.
        $sent = GepgMessage::where('type', 'gepgBillSubReq')->sole()->payload;
        preg_match('#<Gepg>(<gepgBillSubReq>.*</gepgBillSubReq>)<gepgSignature>(.*)</gepgSignature>#s', $sent, $m);
        $key = openssl_pkey_get_public(app(GepgSigner::class)->spPublicKey());
        $this->assertSame(1, openssl_verify($m[1], base64_decode($m[2]), $key, OPENSSL_ALGO_SHA1));
        $this->assertStringContainsString('<BillAmt>106200.00</BillAmt>', $sent);
        $this->assertStringContainsString('<GfsCode>140201</GfsCode>', $sent);

        // The exhibitor is told the control number.
        $this->assertDatabaseHas('app_notifications', ['title' => "Control number {$bill->control_number}"]);
    }

    public function test_a_gepg_payment_issues_the_receipt_and_confirms_participation_once(): void
    {
        $invoice = $this->allocated();
        $bill = GepgBill::sole();
        $payment = fn () => $this->fromGepg('<gepgPmtSpInfo><PymtTrxInf><TrxId>TRX-1</TrxId><SpCode>SP00000</SpCode><PayRefId>TRX-1</PayRefId>'
            ."<BillId>{$bill->bill_id}</BillId><PayCtrNum>{$bill->control_number}</PayCtrNum><BillAmt>106200.00</BillAmt><PaidAmt>106200.00</PaidAmt>"
            .'<BillPayOpt>2</BillPayOpt><CCy>TZS</CCy><TrxDtTm>'.now()->format('Y-m-d\TH:i:s').'</TrxDtTm><UsdPayChnl>BANK</UsdPayChnl>'
            .'<PyrCellNum>255700000001</PyrCellNum><PyrName>Acme</PyrName><PspReceiptNumber>CRDB-77</PspReceiptNumber><PspName>CRDB</PspName><CtrAccNum>0150</CtrAccNum>'
            .'</PymtTrxInf></gepgPmtSpInfo>');

        $this->call('POST', '/api/v1/integrations/gepg/payment', [], [], [], ['CONTENT_TYPE' => 'application/xml'], $payment())
            ->assertOk()->assertHeader('Content-Type', 'application/xml')->assertSee('<TrxStsCode>7101</TrxStsCode>', false);

        $p = Payment::sole();
        $this->assertSame('verified', $p->verification_status);
        $this->assertNotNull($p->receipt_number);
        $this->assertSame('CRDB', $p->psp_name);
        $this->assertSame('paid', $invoice->fresh()->status);
        $this->assertSame('confirmed', $invoice->application->fresh()->status);
        $this->assertSame('paid', $bill->fresh()->status);

        // GePG retries the same notification: acknowledged, not recorded twice.
        $this->call('POST', '/api/v1/integrations/gepg/payment', [], [], [], ['CONTENT_TYPE' => 'application/xml'], $payment())
            ->assertOk()->assertSee('<TrxStsCode>7101</TrxStsCode>', false);
        $this->assertSame(1, Payment::count());
    }

    public function test_a_message_not_signed_by_gepg_is_refused_and_changes_nothing(): void
    {
        $this->allocated();
        $bill = GepgBill::sole();
        $content = "<gepgPmtSpInfo><PymtTrxInf><TrxId>FAKE</TrxId><BillId>{$bill->bill_id}</BillId><PaidAmt>106200.00</PaidAmt></PymtTrxInf></gepgPmtSpInfo>";
        // Signed by GeMEMIS's key, not GePG's: a forger cannot produce GePG's signature.
        $forged = app(GepgXml::class)->envelope($content);

        $this->call('POST', '/api/v1/integrations/gepg', [], [], [], ['CONTENT_TYPE' => 'application/xml'], $forged)
            ->assertOk()->assertSee('<TrxStsCode>7303</TrxStsCode>', false);

        $this->assertSame(0, Payment::count());
        $this->assertFalse(GepgMessage::where('type', 'gepgPmtSpInfo')->sole()->signature_valid);
    }

    public function test_partial_payment_keeps_the_control_number_and_the_rest_completes_it(): void
    {
        $invoice = $this->allocated();
        $gepg = app(GepgService::class);

        $gepg->simulatePayment($invoice, 50000);
        $bill = GepgBill::sole();
        $this->assertSame('issued', $bill->status);
        $this->assertSame(50000.0, $bill->paid_amount);
        $this->assertSame('partially_paid', $invoice->fresh()->status);

        $gepg->simulatePayment($invoice->fresh(), 56200);
        $this->assertSame('paid', $invoice->fresh()->status);
        $this->assertSame(1, GepgBill::count()); // never re-billed
    }

    public function test_a_change_in_amount_cancels_the_bill_and_issues_a_new_control_number(): void
    {
        $invoice = $this->allocated();
        $first = GepgBill::sole();

        Sanctum::actingAs($this->officer());
        $allocation = $invoice->items()->first()->allocation_id;
        $this->postJson("/api/v1/allocations/$allocation/reallocate", ['space_id' => $this->spaces['A03']->id, 'reason' => 'Upgraded to premium'])->assertSuccessful();

        $this->assertSame('superseded', $first->fresh()->status);
        $this->assertDatabaseHas('gepg_messages', ['type' => 'gepgBillCanclReq', 'reference' => $first->bill_id]);
        $second = GepgBill::where('status', 'issued')->sole();
        $this->assertSame(212400.0, $second->amount); // 180,000 + VAT
        $this->assertNotSame($first->control_number, $second->control_number);
        $this->assertSame($second->control_number, $invoice->fresh()->control_number);
        $this->assertStringContainsString('<GfsCode>140202</GfsCode>', GepgMessage::where('type', 'gepgBillSubReq')->latest('id')->first()->payload);
    }

    public function test_settling_outside_gepg_cancels_the_control_number(): void
    {
        $invoice = $this->allocated();
        Sanctum::actingAs($this->finance());
        $id = $this->post("/api/v1/invoices/{$invoice->id}/payments", ['amount' => 106200, 'paid_on' => now()->toDateString(), 'method' => 'bank',
            'payer_reference' => 'TRF-1', 'manual_reason' => 'Paid by direct transfer before receiving the control number.',
            'evidence' => \Illuminate\Http\UploadedFile::fake()->create('slip.pdf', 20, 'application/pdf')], ['Accept' => 'application/json'])->assertCreated()->json('id');
        // A second Finance Officer accepts it (two-person rule).
        Sanctum::actingAs(User::create(['name' => 'Finance 2', 'email' => 'finance2@test.tz', 'password' => 'Secret#12345', 'role' => 'finance_officer'])->refresh());
        $this->postJson("/api/v1/payments/$id/verify", ['decision' => 'verify'])->assertOk();

        $this->assertSame('cancelled', GepgBill::sole()->status);
        $this->assertSame('paid', $invoice->fresh()->gepg_status);
    }

    public function test_reconciliation_recovers_a_lost_notification_and_flags_unknown_payments(): void
    {
        $invoice = $this->allocated();
        $gepg = app(GepgService::class);

        // GePG takes the money but its notification never arrives.
        $gepg->simulatePayment($invoice, 106200, notify: false);
        $this->assertSame(0, Payment::count());
        // And GeMEMIS holds a "GePG" payment GePG has no record of.
        Payment::create(['reference' => 'PAY-X', 'invoice_id' => $invoice->id, 'amount' => 10, 'paid_on' => now()->toDateString(),
            'method' => 'control_number', 'channel' => 'gateway', 'gepg_trx_id' => 'GHOST', 'verification_status' => 'verified']);

        $run = $gepg->reconcile(Carbon::today());

        $this->assertSame('received', $run->status);
        $this->assertCount(1, $run->recovered);
        $this->assertSame('GHOST', $run->missing_at_gepg[0]['trx']);
        $this->assertTrue(Payment::where('gepg_trx_id', '!=', 'GHOST')->sole()->verification_status === 'verified');
        $this->assertSame('paid', $invoice->fresh()->status);
    }

    public function test_live_mode_posts_signed_xml_with_the_routing_headers_and_records_a_refusal(): void
    {
        config(['gepg.mode' => 'live', 'gepg.base_url' => 'https://gepg.example.test']);
        // Live mode needs real key files: use the simulator's, written out like real ones.
        $signer = app(GepgSigner::class);
        config(['gepg.mode' => 'simulator']);
        $signer->sign('x'); // generates GeMEMIS's simulator key pair
        $signer->signAsGepg('x'); // and GePG's
        $dir = Storage::disk('local')->path('gepg-simulator');
        config(['gepg.mode' => 'live', 'gepg.private_key_path' => "$dir/sp.pem", 'gepg.gepg_certificate_path' => "$dir/gepg.pub.pem"]);
        app()->forgetInstance(GepgService::class);

        $this->finance(); // someone to tell when GePG refuses
        Http::fake(['gepg.example.test/*' => Http::response(app(GepgXml::class)->envelope('<gepgBillSubReqAck><TrxStsCode>7280</TrxStsCode></gepgBillSubReqAck>', asGepg: true))]);

        $invoice = $this->allocated();

        Http::assertSent(function (HttpRequest $r) {
            return $r->url() === 'https://gepg.example.test/api/bill/sigqrequest'
                && $r->hasHeader('Gepg-Com', 'default.sp.in') && $r->hasHeader('Gepg-Code', 'SP00000')
                && str_contains($r->body(), '<gepgSignature>');
        });
        $bill = GepgBill::sole();
        $this->assertSame('failed', $bill->status);
        $this->assertSame('7280', $bill->status_code);
        $this->assertSame('failed', $invoice->fresh()->gepg_status);
        $this->assertDatabaseHas('app_notifications', ['title' => 'Control number not issued']);
    }

    public function test_off_mode_sends_nothing(): void
    {
        config(['gepg.mode' => 'off']);
        $invoice = $this->allocated();

        $this->assertSame(0, GepgBill::count());
        $this->assertNull($invoice->fresh()->control_number);
    }
}
