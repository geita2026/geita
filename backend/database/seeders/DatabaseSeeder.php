<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Exhibition;
use App\Models\Exhibitor;
use App\Models\Space;
use App\Models\User;
use App\Services\AllocationService;
use App\Services\BillingService;
use App\Services\Ref;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Demonstration data: one published exhibition with a drawn floor plan, a
 * staff account per role, and exhibitors at every stage of the process.
 *
 * Every account uses the password in DEMO_PASSWORD (default below). These are
 * development credentials — never run this seeder against production; use
 * `php artisan db:seed --class=ProductionSeeder` there instead.
 */
class DatabaseSeeder extends Seeder
{
    public const DEMO_PASSWORD = 'Gememis@2026';

    public function run(): void
    {
        $password = env('DEMO_PASSWORD', self::DEMO_PASSWORD);

        $staff = [];
        foreach ([
            'system_admin' => ['Neema Mwakyusa', 'admin@gememis.test'],
            'exhibition_admin' => ['Baraka Lugendo', 'exhibitions@gememis.test'],
            'space_officer' => ['Zawadi Masanja', 'spaces@gememis.test'],
            'registration_officer' => ['Juma Kapinga', 'registration@gememis.test'],
            'finance_officer' => ['Rehema Shija', 'finance@gememis.test'],
            'management' => ['Dkt. Elias Magesa', 'management@gememis.test'],
            'customer_service' => ['Upendo Ndaki', 'service@gememis.test'],
            'auditor' => ['Godfrey Mabula', 'audit@gememis.test'],
        ] as $role => [$name, $email]) {
            $staff[$role] = User::create(['name' => $name, 'email' => $email, 'phone' => '+2557'.random_int(10000000, 99999999), 'password' => $password, 'role' => $role]);
        }
        auth()->setUser($staff['exhibition_admin']);

        $exhibition = Exhibition::create([
            'code' => 'GMTE27',
            'name' => 'Geita Mineral & Mining Technology Exhibition 2027',
            'theme' => 'Technology and local content for a sustainable mining economy',
            'objectives' => "Connect small-scale miners with technology suppliers and financiers.\nPromote value addition and local content in the mineral sector.\nShowcase safe, environmentally responsible mining practice.",
            'venue' => 'EPZA Bombambili Grounds, Geita',
            'start_date' => now()->addDays(120), 'end_date' => now()->addDays(130),
            'registration_opens_on' => now()->subDays(20), 'registration_closes_on' => now()->addDays(75),
            'payment_deadline' => now()->addDays(90),
            'categories' => ['Mining equipment', 'Mineral processing', 'Gold refining & trading', 'Financial services', 'Safety & environment', 'Government & regulators', 'Small-scale miners'],
            'required_documents' => ['Business licence', 'TIN certificate'],
            'terms' => 'Spaces are allocated subject to availability and are confirmed only after full payment is verified. Allocated spaces may not be sublet or transferred without written authorisation. Exhibitors must observe site safety rules and vacate the space within 48 hours of the closing date.',
            'contact_email' => 'exhibitions@gememis.test', 'contact_phone' => '+255 28 252 0000',
            'status' => 'draft', 'created_by' => $staff['exhibition_admin']->id,
        ]);

        $halls = [];
        foreach ([['A', 'Main Pavilion', 'indoor', 10], ['B', 'Technology Hall', 'indoor', 8], ['OUT', 'Heavy Machinery Yard', 'outdoor', 6], ['SME', 'Small-Scale Miners Village', 'pavilion', 10]] as $i => [$code, $name, $kind, $cols]) {
            $halls[$code] = $exhibition->halls()->create(['code' => $code, 'name' => $name, 'kind' => $kind, 'grid_cols' => $cols, 'sort' => $i]);
        }
        $cats = [];
        foreach ([['PRM', 'Premium booth', 75000, '#f59e0b'], ['STD', 'Standard booth', 45000, '#3b82f6'], ['INT', 'International exhibitor space', 90000, '#8b5cf6'],
            ['GOV', 'Government space', 30000, '#14b8a6'], ['OUT', 'Outdoor space', 20000, '#84cc16'], ['SME', 'SME space', 15000, '#ec4899']] as [$code, $name, $rate, $color]) {
            $cats[$code] = $exhibition->spaceCategories()->create(['code' => $code, 'name' => $name, 'price_per_sqm' => $rate, 'color' => $color]);
        }

        // Rows come in pairs with an aisle between each pair, like a real hall.
        $lay = function (string $hall, int $count, callable $spec) use ($exhibition, $halls, $cats) {
            $cols = $halls[$hall]->grid_cols;
            for ($i = 0; $i < $count; $i++) {
                [$cat, $w, $l, $facilities] = $spec($i);
                $band = intdiv($i, $cols);
                $exhibition->spaces()->create([
                    'hall_id' => $halls[$hall]->id, 'space_category_id' => $cats[$cat]->id,
                    'number' => $hall.str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT),
                    'width_m' => $w, 'length_m' => $l, 'area_sqm' => $w * $l, 'price' => $w * $l * $cats[$cat]->price_per_sqm,
                    'facilities' => $facilities, 'map_col' => $i % $cols + 1, 'map_row' => $band + intdiv($band, 2) + 1,
                    'location_note' => $band === 0 ? 'Facing the main entrance' : null,
                ]);
            }
        };
        $lay('A', 40, fn ($i) => $i < 10 ? ['PRM', 3, 6, ['Power 13A', 'Wi-Fi', 'Carpet', 'Fascia board', 'Spotlights']] : ($i >= 30 ? ['GOV', 3, 3, ['Power 13A', 'Table & 2 chairs']] : ['STD', 3, 3, ['Power 13A', 'Table & 2 chairs', 'Fascia board']]));
        $lay('B', 24, fn ($i) => $i < 8 ? ['INT', 3, 6, ['Power 32A', 'Wi-Fi', 'Carpet', 'Storage room']] : ['STD', 3, 3, ['Power 13A', 'Table & 2 chairs']]);
        $lay('OUT', 12, fn ($i) => ['OUT', 6, $i < 6 ? 12 : 6, ['Open ground', 'Vehicle access', 'Three-phase power on request']]);
        $lay('SME', 30, fn ($i) => ['SME', 3, 3, ['Shared power', 'Table & 2 chairs']]);

        Space::where('number', 'A20')->update(['status' => 'maintenance', 'special_conditions' => 'Floor repair in progress']);
        Space::where('number', 'B24')->update(['status' => 'blocked', 'special_conditions' => 'Fire exit clearance']);
        Space::whereIn('number', ['A31', 'A32'])->update(['status' => 'reserved', 'special_conditions' => 'Held for the Ministry of Minerals']);

        $exhibition->update(['status' => 'published', 'published_at' => now()->subDays(20)]);

        Exhibition::create([
            'code' => 'GGIF27', 'name' => 'Geita Gold Investment Forum 2027', 'theme' => 'Financing the next generation of mines',
            'venue' => 'Geita Gold Mine Conference Centre', 'start_date' => now()->addDays(240), 'end_date' => now()->addDays(242),
            'registration_opens_on' => now()->addDays(60), 'registration_closes_on' => now()->addDays(200), 'payment_deadline' => now()->addDays(215),
            'required_documents' => ['Business licence'], 'status' => 'draft', 'created_by' => $staff['exhibition_admin']->id,
        ]);

        /* ---- exhibitors at each stage ---- */
        $allocations = app(AllocationService::class);
        $billing = app(BillingService::class);
        $make = function (array $e, string $stage, string $cat, array $spaces = [], int $requested = 1, ?string $size = '3m × 3m') use ($exhibition, $cats, $staff, $password, $allocations, $billing) {
            [$org, $person, $country, $type, $sector, $product] = $e;
            $slug = strtolower(preg_replace('/[^a-z]/i', '', explode(' ', $org)[0]));
            $exhibitor = Exhibitor::create([
                'organization_name' => $org, 'registration_number' => 'BRELA-'.random_int(100000, 999999), 'tin' => random_int(100, 199).'-'.random_int(100, 999).'-'.random_int(100, 999),
                'contact_person' => $person, 'phone' => '+2557'.random_int(10000000, 99999999), 'email' => "info@$slug.test",
                'physical_address' => 'P.O. Box '.random_int(100, 9999), 'country' => $country, 'exhibitor_type' => $type,
                'business_sector' => $sector, 'business_activity' => $product,
            ]);
            $user = User::create(['name' => $person, 'email' => "$slug@exhibitor.test", 'phone' => $exhibitor->phone, 'password' => $password, 'role' => 'exhibitor', 'exhibitor_id' => $exhibitor->id]);
            if ($stage === 'registered') {
                return;
            }

            auth()->setUser($user);
            $application = Application::create([
                'reference' => Ref::temp(), 'exhibition_id' => $exhibition->id, 'exhibitor_id' => $exhibitor->id, 'space_category_id' => $cats[$cat]->id,
                'preferred_size' => $size, 'spaces_requested' => $requested, 'product_category' => $sector, 'products_description' => $product,
                'special_requirements' => $cat === 'OUT' ? 'Low-loader access for a 40-tonne excavator.' : null, 'status' => 'draft',
            ]);
            Ref::assign($application, 'reference', 'APP', $exhibition->code);
            if ($stage === 'draft') {
                return;
            }
            foreach ($exhibition->required_documents as $type) {
                $path = "applications/{$application->id}/".str($type)->slug().'.pdf';
                Storage::disk('local')->put($path, "%PDF-1.4\n% Demo placeholder for $type — {$org}\n");
                $application->documents()->create(['type' => $type, 'original_name' => str($type)->slug().'.pdf', 'path' => $path, 'mime' => 'application/pdf', 'size' => 64, 'uploaded_by' => $user->id]);
            }
            $application->update(['status' => 'submitted', 'submitted_at' => now()->subDays(random_int(2, 15))]);
            if ($stage === 'submitted') {
                return;
            }

            auth()->setUser($staff['registration_officer']);
            if ($stage === 'returned' || $stage === 'rejected') {
                $application->update(['status' => $stage, 'reviewed_by' => $staff['registration_officer']->id, 'reviewed_at' => now()->subDay(),
                    'review_notes' => $stage === 'returned' ? 'The business licence uploaded has expired. Please upload the current one.' : 'Products are outside the scope of a mineral-sector exhibition.']);

                return;
            }
            $application->update(['status' => 'approved', 'reviewed_by' => $staff['registration_officer']->id, 'reviewed_at' => now()->subDays(2)]);
            if ($stage === 'approved') {
                return;
            }

            foreach ($spaces as $number) {
                $space = Space::where('exhibition_id', $exhibition->id)->where('number', $number)->first();
                if ($stage === 'selected') {
                    auth()->setUser($user);
                    $allocations->allocate($application->fresh(), $space, $user, 'self');
                } else {
                    auth()->setUser($staff['space_officer']);
                    $allocations->allocate($application->fresh(), $space, $staff['space_officer']);
                }
            }
            if (in_array($stage, ['selected', 'invoiced'], true)) {
                return;
            }

            $invoice = $application->invoices()->first();
            auth()->setUser($user);
            $payment = $billing->submitPayment($invoice, [
                'amount' => $stage === 'part_paid' ? round($invoice->total / 2) : $invoice->total, 'paid_on' => now()->subDay()->toDateString(),
                'method' => 'bank', 'payer_reference' => 'CRDB'.random_int(10000000, 99999999),
            ], null, $user);
            if ($stage === 'payment_pending') {
                return;
            }
            auth()->setUser($staff['finance_officer']);
            $billing->verify($payment, $staff['finance_officer'], 'Matched to bank statement');
        };

        $make(['Kahama Drilling Supplies Ltd', 'Amina Hassan', 'Tanzania', 'local', 'Mining equipment', 'Drill rigs, bits and consumables for exploration and small-scale mining'], 'confirmed', 'PRM', ['A01'], 1, '3m × 6m');
        $make(['Lake Zone Gold Refiners', 'Peter Mashauri', 'Tanzania', 'local', 'Gold refining & trading', 'Gold refining, assaying and bullion trading'], 'confirmed', 'PRM', ['A02', 'A03'], 2, '3m × 6m');
        $make(['Sandvik Rock Technology', 'Lars Eriksson', 'Sweden', 'international', 'Mining equipment', 'Underground loaders, rock drills and automation systems'], 'part_paid', 'INT', ['B01']);
        $make(['Shanxi Heavy Machinery Co.', 'Wei Zhang', 'China', 'international', 'Mining equipment', 'Excavators, crushers and haul trucks'], 'payment_pending', 'OUT', ['OUT01']);
        $make(['Mwanza Safety Gear', 'Grace Lema', 'Tanzania', 'sme', 'Safety & environment', 'Personal protective equipment and gas detectors'], 'invoiced', 'STD', ['A11']);
        $make(['Tume ya Madini — Geita', 'Eng. Daniel Mapunda', 'Tanzania', 'government', 'Government & regulators', 'Licensing desk and mineral market information'], 'invoiced', 'GOV', ['A33']);
        $make(['Nyarugusu Miners Cooperative', 'Hamisi Bundala', 'Tanzania', 'sme', 'Small-scale miners', 'Cooperative of small-scale gold miners — ore samples and services'], 'selected', 'SME', ['SME01']);
        $make(['Benki ya Wachimbaji', 'Flora Mtei', 'Tanzania', 'local', 'Financial services', 'Equipment leasing and gold-backed loans for miners'], 'approved', 'STD');
        $make(['Geita Mercury-Free Processing', 'Said Omari', 'Tanzania', 'sme', 'Mineral processing', 'Mercury-free gravity concentration and cyanidation plants'], 'approved', 'SME');
        $make(['Kibo Assay Laboratories', 'Dr. Anna Mrema', 'Tanzania', 'local', 'Mineral processing', 'Fire assay and geochemical laboratory services'], 'submitted', 'STD');
        $make(['Katoro Explosives & Blasting', 'Emmanuel Kazimoto', 'Tanzania', 'local', 'Mining equipment', 'Licensed supply of explosives and blasting services'], 'submitted', 'OUT');
        $make(['Bukombe Pump Services', 'Mariam Seif', 'Tanzania', 'sme', 'Mining equipment', 'Dewatering pumps and generators'], 'returned', 'SME');
        $make(['Rift Valley Solar Kits', 'John Otieno', 'Kenya', 'international', 'Safety & environment', 'Solar lighting kits'], 'rejected', 'STD');
        $make(['Chato Gemstone Dealers', 'Rose Kabati', 'Tanzania', 'sme', 'Gold refining & trading', 'Gemstone cutting and jewellery'], 'draft', 'SME');
        $make(['Ushirombo Mining Tools', 'Paulo Nkwabi', 'Tanzania', 'sme', 'Mining equipment', 'Hand tools and hoists'], 'registered', 'SME');

        auth()->forgetGuards();
    }
}
