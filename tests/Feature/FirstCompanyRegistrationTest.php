<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CompanySectorSeeder;
use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class FirstCompanyRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function createApplication()
    {
        $app = parent::createApplication();
        // The User model explicitly uses the mysql connection.
        $app['config']->set('database.connections.mysql', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'foreign_key_constraints' => true,
        ]);
        $app['config']->set('database.default', 'mysql');
        $app['db']->purge('mysql');

        return $app;
    }

    public function test_the_first_login_modal_saves_company_locally_after_the_latest_yearly_id()
    {
        $this->withoutMiddleware(\App\Http\Middleware\NetworkMaintenance::class);
        Http::fake();
        $this->travelTo(Carbon::parse('2026-11-21 12:00:00'));
        DB::table('countries')->insert(['country_id' => 'tz', 'country_name' => 'Tanzania']);
        DB::table('sectors')->insert(['sectorID' => 'mining', 'sectorName' => 'Mining and Mineral Exploration']);

        $this->seed([CountrySeeder::class, CompanySectorSeeder::class]);
        $countryCount = DB::table('countries')->count();
        $sectorCount = DB::table('sectors')->count();
        $this->seed([CountrySeeder::class, CompanySectorSeeder::class]);
        $this->assertDatabaseCount('countries', $countryCount);
        $this->assertDatabaseCount('sectors', $sectorCount);
        $this->assertDatabaseHas('countries', ['country_id' => 'tz', 'country_name' => 'Tanzania']);
        $this->assertDatabaseHas('countries', ['country_short_code' => 'KE', 'phone_code' => '+254']);
        $this->assertDatabaseHas('sectors', ['sectorID' => 'mining', 'sectorName' => 'Mining and Mineral Exploration']);
        $this->assertDatabaseHas('sectors', ['sectorName' => 'Mining Equipment and Supplies', 'sectorStatus' => 1]);
        foreach (['GMMS260008', 'GMMS259999', 'T-026-999-999', 'GMMS26TEST'] as $id) {
            DB::table('companies')->insert([
                'companyID' => (string) Str::uuid(),
                'registrationID' => $id,
                'companyName' => $id,
            ]);
        }

        $first = $this->customer('first@example.test');
        $this->actingAs($first)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('No exhibition event is active.')
            ->assertSee('id="NoCompany"', false)
            ->assertSee('name="tinNo"', false)
            ->assertSee('Mining Equipment and Supplies')
            ->assertSee('Optional company details')
            ->assertSee(route('companies.first.store'))
            ->assertDontSee('name="originality"', false);

        $form = [
            'companyName' => 'Geita Mining',
            'companyEmail' => 'mining@example.test',
            'companyPhone' => '+255712345678',
            'companyAddress' => 'Geita',
            'companyLocation' => 'Tanzania',
            'companySector' => 'mining',
            'companyCategory' => 'Small Traders',
            'isAcceptedTerms' => '1',
        ];
        $this->from(route('dashboard'))->post(route('companies.first.store'), $form)
            ->assertSessionHasErrorsIn('firstCompany', ['tinNo']);
        $this->get(route('dashboard'))->assertOk()
            ->assertSee('aria-invalid="true"', false)
            ->assertSee('id="first-company-tinNo-error"', false);

        $form['tinNo'] = '012345678';
        $this->post(route('companies.first.store'), $form)
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Company registered successfully. Your registration ID is GMMS260009.');
        $companyId = $first->fresh()->myCompany;
        $this->assertDatabaseHas('companies', [
            'companyID' => $companyId,
            'registrationID' => 'GMMS260009',
            'tinNo' => '012345678',
        ]);
        $this->assertDatabaseHas('company_admins', ['companyId' => $companyId, 'adminId' => $first->user_uuid]);
        $this->get(route('dashboard'))->assertOk()->assertDontSee('id="NoCompany"', false);

        $second = $this->customer('second@example.test');
        $this->actingAs($second)->from(route('dashboard'))->post(route('companies.first.store'), $form)
            ->assertSessionHasErrorsIn('firstCompany', ['tinNo']);
        $form['companyName'] = 'Geita Equipment';
        $form['tinNo'] = '123456789';
        $this->post(route('companies.first.store'), $form)
            ->assertSessionHas('success', 'Company registered successfully. Your registration ID is GMMS260010.');

        $this->post(route('companies.first.store'), $form)->assertRedirect(route('dashboard'));
        $this->assertDatabaseCount('companies', 6);
        Http::assertNothingSent();
    }

    public function test_login_goes_directly_to_the_exhibition_dashboard()
    {
        $this->withoutMiddleware(\App\Http\Middleware\NetworkMaintenance::class);
        $user = $this->customer('login@example.test');
        $user->update(['password' => Hash::make('unused-for-this-test'), 'account_status' => 1]);

        $this->withSession(['url.intended' => route('companies.index')])
            ->post(route('login'), ['user_name' => $user->email, 'password' => 'unused-for-this-test'])
            ->assertRedirect(route('dashboard'))
            ->assertSessionMissing('url.intended');
        $this->assertAuthenticatedAs($user);
        $this->get(route('dashboard'))->assertOk()->assertSee('No exhibition event is active.');

        DB::table('events')->insert([
            'event_id' => (string) Str::uuid(),
            'event_name' => 'Geita Mineral Exhibition',
            'event_start_date' => '2026-11-21',
            'event_end_date' => '2026-11-22',
            'event_status' => 1,
        ]);
        DB::table('companies')->insert([
            'companyID' => $user->user_uuid,
            'companyName' => 'Legacy Company',
        ]);

        $this->get(route('dashboard'))->assertOk()
            ->assertSee('Geita Mineral Exhibition')
            ->assertDontSee('id="NoCompany"', false);
        $this->assertDatabaseHas('company_admins', ['adminId' => $user->user_uuid, 'companyId' => $user->user_uuid]);
        $this->assertSame($user->user_uuid, $user->fresh()->myCompany);
    }

    private function customer(string $email): User
    {
        return User::create([
            'user_uuid' => (string) Str::uuid(),
            'email' => $email,
            'password' => 'unused-for-this-test',
            'account_type' => 2,
        ]);
    }
}
