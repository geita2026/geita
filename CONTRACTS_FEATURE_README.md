# Contracts Management & Digital Signing - Implementation Guide

> **Version:** 4.0 Secure Architecture
> **Last Updated:** February 13, 2026
> **Purpose:** Step-by-step instructions to add the Contracts feature to an older copy of this Laravel project
>
> **🔒 Security Update:** Flutter app now served through Laravel authentication (not public directory)

---

## CONTEXT

This is a **Laravel 8** project (`laravel/framework: ^8.0`, PHP `^8.1.2`) for Tanzania Trade Development Authority (TanTrade). It manages the Dar es Salaam International Trade Fair (DITF).

### Security Architecture

The Contracts feature implements **secure digital contract signing** with the following protections:

- **Authentication Required**: All contract access requires Laravel authentication
- **Role-Based Access**: Admins can view any contract, customers only their own
- **Protected Flutter App**: Served from `resources/flutter/` (not public directory)
- **API Key Protection**: REST API endpoints secured with custom middleware
- **Directory Traversal Prevention**: Asset serving includes security checks

The system includes:
- Flutter web app for digital signatures (served securely through Laravel)
- REST API backend with rate limiting and API key authentication
- Admin panel for contract management (roles 20, 21, 34)
- Customer portal with self-service contract access
- Audit trail and permission-based access control

### Key Dependencies Already in the Project
- `spatie/laravel-permission` (role-based access)
- `owen-it/laravel-auditing` (audit trail)
- `laravel/sanctum` (API auth)
- `fruitcake/laravel-cors` (CORS handling)
- `barryvdh/laravel-dompdf` / `carlos-meneses/laravel-mpdf` (PDF generation)

---

## PREREQUISITE CHECKS

Before starting, verify these exist in the older project:

1. **Auth system** - The project uses Laravel Breeze (`laravel/breeze: ^1.8`). The `auth` middleware must be registered.
2. **`companies` table** - Must have columns: `companyID`, `registrationID`, `companyName`, `companyEmail`, `companyPhone`, `companyAddress`. The `registrationID` column is the foreign key for contracts.
3. **`users` table** - Must have column: `myCompany` (VARCHAR/CHAR UUID linking to `companies.companyID`)
4. **Layout component** - Views use `<x-app-layout>` component (Blade component at `resources/views/layouts/app.blade.php`).
5. **Sidebar** - Menu is driven by `level_one_menuses` and `level_two_menuses` database tables rendered in `resources/views/layouts/sidebar.blade.php`.
6. **Dashboard** - External users (account_type=2) see dashboard at `resources/views/Dashboards/ExternalUser.blade.php`
7. **Named routes** - `dashboard` and `contracts.index` route names are referenced in views.
8. **Icon set** - Views use `icon-*` classes (icomoon icon font already in layouts).
9. **W3CSS** - The admin index view loads W3CSS from CDN inline.

---

## SECURE FLUTTER DEPLOYMENT ARCHITECTURE

### Why Not Public Directory?

**Security Risk** ❌: Placing Flutter app in `public/` allows direct URL access without authentication:
```
http://site.com/cs_467.../index.html?registrationID=ANY_ID  ← Anyone can access!
```

**Secure Approach** ✅: Serve Flutter app through Laravel with authentication:
```
http://site.com/contract-signing-app  ← Requires login first
```

### How It Works

1. **Flutter App Location**: `resources/flutter/contract-signing/` (not accessible directly)
2. **Laravel Routes**: Protected routes serve HTML and assets after auth check
3. **Base Href**: `/contract-signing-app/` points to Laravel route, not public path
4. **Access Control**: Controller validates user permissions before serving

### File Structure

```
resources/
  └── flutter/
      └── contract-signing/     ← Flutter web app (SECURE)
          ├── index.html
          ├── main.dart.js
          ├── assets/
          ├── canvaskit/
          └── ...

public/                         ← Old location (REMOVED for security)
  └── cs_467.../                ← NO LONGER USED
```

### Request Flow

```
User → /contract-signing?registrationID=X
  ↓
Laravel Auth Middleware (checks login)
  ↓
Controller (validates access to contract X)
  ↓
Serves Flutter app from resources/flutter/
  ↓
Flutter loads assets through /contract-signing-app/{asset}
  ↓
Each asset request goes through Laravel auth
```

---

## STEP-BY-STEP IMPLEMENTATION

Execute these steps **in order**. Each step lists the exact file to create or modify, with complete content.

---

### STEP 1: Add Environment Variables

**File:** `.env`
**Action:** Edit your `.env` file and add these configuration lines at the end:

```bash
# Contract API Security Configuration
CONTRACT_API_KEY=
CONTRACT_API_SECRET=
```

**Important Notes:**
- `.env` is gitignored and will NOT be pushed to GitHub (this is by design for security)
- These are the actual keys used by the TanTrade system
- When deploying to a new environment, manually add these lines to that server's `.env` file
- Do NOT create `.env.example` with these real keys - keep them internal only

---

### STEP 2: Add Config Entry

**File:** `config/services.php`
**Action:** Add the following array entry inside the returned array, after the last existing service entry (e.g., after the `'ses'` block):

```php
'contract_api' => [
    'key' => env('CONTRACT_API_KEY', 'your-secure-random-key-here'),
    'secret' => env('CONTRACT_API_SECRET', 'your-secure-random-secret-here'),
],
```

---

### STEP 3: Create the Migration

**File:** `database/migrations/2026_02_10_084315_create_signed_contracts_table.php`
**Action:** Create this file with the following complete content:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSignedContractsTable extends Migration
{
    public function up()
    {
        Schema::create('signed_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('registration_id', 100);
            $table->enum('signer_type', ['exhibitor', 'witness']);
            $table->string('first_name', 100);
            $table->string('middle_name', 100);
            $table->string('last_name', 100);
            $table->string('qualification', 200)->nullable();
            $table->text('address')->nullable();
            $table->string('signature_path', 255);
            $table->unsignedBigInteger('related_signature_id')->nullable();
            $table->timestamps();

            $table->index('registration_id', 'idx_registration');
            $table->index('signer_type', 'idx_signer_type');
            $table->index('related_signature_id', 'idx_related');
        });
    }

    public function down()
    {
        Schema::dropIfExists('signed_contracts');
    }
}
```

**Note:** PDF storage removed - signatures only. PDF is generated on-the-fly by Flutter app.

**Then run:**
```bash
php artisan migrate
```

---

### STEP 4: Create Middleware - ValidateContractApiKey

**File:** `app/Http/Middleware/ValidateContractApiKey.php`
**Action:** Create this file with the following complete content:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateContractApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-Key') ?? $request->query('api_key');

        $validApiKey = config('services.contract_api.key');

        if (!$apiKey || $apiKey !== $validApiKey) {
            return response()->json([
                'status' => 0,
                'message' => 'Unauthorized. Invalid API key.'
            ], 401);
        }

        return $next($request);
    }
}
```

---

### STEP 5: Create Middleware - ContractApiRateLimiter

**File:** `app/Http/Middleware/ContractApiRateLimiter.php`
**Action:** Create this file with the following complete content:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ContractApiRateLimiter
{
    public function handle(Request $request, Closure $next)
    {
        $registrationId = $request->query('registrationID') ?? $request->input('registration_id') ?? 'unknown';

        $key = 'contract-api:' . Str::lower($registrationId);

        $maxAttempts = 10;
        $decayMinutes = 1;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'status' => 0,
                'message' => 'Too many requests. Please try again in ' . $seconds . ' seconds.',
                'retry_after' => $seconds
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', RateLimiter::remaining($key, $maxAttempts));

        return $response;
    }
}
```

---

### STEP 6: Create Middleware - ValidateContractSignature

**File:** `app/Http/Middleware/ValidateContractSignature.php`
**Action:** Create this file with the following complete content:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateContractSignature
{
    public function handle(Request $request, Closure $next)
    {
        $signature = $request->header('X-Signature');
        $timestamp = $request->header('X-Timestamp');

        if (!$signature || !$timestamp) {
            return response()->json([
                'status' => 0,
                'message' => 'Missing signature or timestamp header'
            ], 401);
        }

        if (abs(time() - $timestamp) > 300) {
            return response()->json([
                'status' => 0,
                'message' => 'Request timestamp expired'
            ], 401);
        }

        $secret = config('services.contract_api.secret');

        $method = $request->method();
        $uri = $request->getRequestUri();
        $body = $request->getContent();

        $payload = $method . $uri . $body . $timestamp;
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($expectedSignature, $signature)) {
            return response()->json([
                'status' => 0,
                'message' => 'Invalid signature'
            ], 401);
        }

        return $next($request);
    }
}
```

---

### STEP 7: Register Middleware in Kernel

**File:** `app/Http/Kernel.php`
**Action:** Add these three entries to the `$routeMiddleware` array (at the end, before the closing `];`):

```php
'contract.api' => \App\Http\Middleware\ValidateContractApiKey::class,
'contract.ratelimit' => \App\Http\Middleware\ContractApiRateLimiter::class,
'contract.signature' => \App\Http\Middleware\ValidateContractSignature::class,
```

Also verify that this line exists in the global `$middleware` array (for iframe support):
```php
\App\Http\Middleware\XFrameHeadersMiddleware::class,
```

---

### STEP 8: Update CSRF Exception List

**File:** `app/Http/Middleware/VerifyCsrfToken.php`
**Action:** Add contract endpoints to the `$except` array:

```php
protected $except = [
    'contract/upload',
    'contract/get-company',
    'contract/check-signature',
    'api/contract/*',
];
```

**Reason:** Flutter app uploads signatures without CSRF tokens. This prevents 419 errors.

---

### STEP 9: Create the API Controller

**File:** `app/Http/Controllers/APIs/ContractSignatureController.php`
**Action:** Create the `APIs` subdirectory if it doesn't exist, then create this file:

```php
<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ContractSignatureController extends Controller
{
    /**
     * Get company information by registration ID
     * Endpoint: GET /api/contract/get-company?registrationID=xxx
     */
    public function getCompany(Request $request)
    {
        $registrationID = $request->query('registrationID');

        if (!$registrationID) {
            return response()->json([
                'status' => 0,
                'message' => 'Registration ID is required'
            ], 400);
        }

        $company = DB::table('companies')
            ->where('registrationID', $registrationID)
            ->first();

        if (!$company) {
            return response()->json([
                'status' => 0,
                'message' => 'Company not found with this Registration ID'
            ], 404);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Company found',
            'data' => [
                'company_name' => $company->companyName,
                'address' => $company->companyAddress ?? '',
                'email' => $company->companyEmail ?? '',
                'phone' => $company->companyPhone ?? $company->companyTel ?? '',
            ]
        ]);
    }

    /**
     * Check if signature exists for this registration ID
     * Endpoint: GET /api/contract/check-signature?registrationID=xxx
     */
    public function checkSignature(Request $request)
    {
        $registrationID = $request->query('registrationID');

        if (!$registrationID) {
            return response()->json([
                'status' => 0,
                'message' => 'Registration ID is required'
            ], 400);
        }

        $signatures = DB::table('signed_contracts')
            ->where('registration_id', $registrationID)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($signatures->isEmpty()) {
            return response()->json([
                'status' => 1,
                'has_signature' => false,
                'is_complete' => false,
                'message' => 'No signatures found'
            ]);
        }

        $isComplete = $signatures->count() >= 2;
        $latestSignature = $signatures->first();

        return response()->json([
            'status' => 1,
            'has_signature' => true,
            'is_complete' => $isComplete,
            'data' => [
                'id' => $latestSignature->id,
                'signer_type' => $latestSignature->signer_type,
                'first_name' => $latestSignature->first_name,
                'middle_name' => $latestSignature->middle_name,
                'last_name' => $latestSignature->last_name,
                'qualification' => $latestSignature->qualification,
                'address' => $latestSignature->address,
                'signature_path' => asset($latestSignature->signature_path),
                'created_at' => $latestSignature->created_at,
            ],
            'signatures' => $signatures->map(function ($sig) {
                $sig->signature_path = asset($sig->signature_path);
                return $sig;
            }),
        ]);
    }

    /**
     * Upload signature image + signer data (PDF generated on-the-fly by Flutter app)
     * Endpoint: POST /api/contract/upload
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'registration_id' => 'required|string',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'qualification' => 'nullable|string|max:200',
            'address' => 'nullable|string',
            'signer_type' => 'required|in:exhibitor,witness',
            'signature_image' => 'required|file|mimes:png,jpg,jpeg|max:2048',
            'existing_signature_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $signatureFile = $request->file('signature_image');
            $signatureFileName = $request->registration_id . '_' . $request->signer_type . '_' . time() . '.png';
            $signaturePath = $signatureFile->storeAs('signatures', $signatureFileName, 'public');

            $signatureId = DB::table('signed_contracts')->insertGetId([
                'registration_id' => $request->registration_id,
                'signer_type' => $request->signer_type,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'qualification' => $request->qualification,
                'address' => $request->address,
                'signature_path' => $signaturePath,
                'related_signature_id' => $request->existing_signature_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($request->existing_signature_id) {
                DB::table('signed_contracts')
                    ->where('id', $request->existing_signature_id)
                    ->update([
                        'related_signature_id' => $signatureId,
                        'updated_at' => now(),
                    ]);
            }

            $signatureCount = DB::table('signed_contracts')
                ->where('registration_id', $request->registration_id)
                ->count();

            return response()->json([
                'status' => 1,
                'message' => 'Signature uploaded successfully',
                'signature_id' => $signatureId,
                'both_signatures_complete' => $signatureCount >= 2,
            ]);

        } catch (\Exception $e) {
            \Log::error('Contract upload failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'registration_id' => $request->registration_id,
            ]);
            return response()->json([
                'status' => 0,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
```

---

### STEP 10: Create the Web Controller

**File:** `app/Http/Controllers/ContractsManagementController.php`
**Action:** Create this file with the following complete content:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractsManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view-contracts')->except(['myContract', 'contractSigning', 'contractSigningFullpage']);
    }

    public function index(Request $request)
    {
        try {
            $contracts = DB::table('signed_contracts')
                ->leftJoin('companies', 'signed_contracts.registration_id', '=', 'companies.registrationID')
                ->select(
                    'signed_contracts.registration_id',
                    'companies.companyName',
                    'companies.companyEmail',
                    'companies.companyPhone',
                    DB::raw("MAX(CASE WHEN signer_type = 'exhibitor' THEN CONCAT(signed_contracts.first_name, ' ', signed_contracts.middle_name, ' ', signed_contracts.last_name) END) as exhibitor_name"),
                    DB::raw("MAX(CASE WHEN signer_type = 'witness' THEN CONCAT(signed_contracts.first_name, ' ', signed_contracts.middle_name, ' ', signed_contracts.last_name) END) as witness_name"),
                    DB::raw("MAX(signed_contracts.updated_at) as last_updated"),
                    DB::raw("COUNT(DISTINCT signed_contracts.signer_type) as signature_count")
                )
                ->groupBy(
                    'signed_contracts.registration_id',
                    'companies.companyName',
                    'companies.companyEmail',
                    'companies.companyPhone'
                )
                ->orderBy('last_updated', 'desc')
                ->paginate(15);

            return view('Contracts.index', compact('contracts'));
        } catch (\Throwable $th) {
            return back()->with('fail', 'Error loading contracts: ' . $th->getMessage());
        }
    }

    public function contractSigning(Request $request)
    {
        $flutterAppPath = 'cs_467807b523254a24356771dd94c810b552de5bacee1ef7ebb27b0de1461d1c81';
        return view('Contracts.signing', compact('flutterAppPath'));
    }

    public function contractSigningFullpage(Request $request)
    {
        $flutterAppPath = 'cs_467807b523254a24356771dd94c810b552de5bacee1ef7ebb27b0de1461d1c81';
        return view('Contracts.signing-fullpage', compact('flutterAppPath'));
    }

    public function myContract(Request $request)
    {
        try {
            $user = auth()->user();

            // Get user's company registration ID (TSRN)
            if (!$user->myCompany) {
                return back()->with('fail', 'No company associated with your account. Please contact support.');
            }

            $company = DB::table('companies')
                ->where('companyID', $user->myCompany)
                ->first();

            if (!$company || !$company->registrationID) {
                return back()->with('fail', 'Company registration ID not found. Please contact support.');
            }

            // Redirect to the contract signing app with the user's registration ID
            $flutterAppPath = 'cs_467807b523254a24356771dd94c810b552de5bacee1ef7ebb27b0de1461d1c81';
            $registrationID = $company->registrationID;

            return redirect("/{$flutterAppPath}/?registrationID={$registrationID}");
        } catch (\Throwable $th) {
            return back()->with('fail', 'Error loading your contract: ' . $th->getMessage());
        }
    }
}
```

---

### STEP 11: Create Routes File

**File:** `routes/contracts.php`
**Action:** Create this file with the following complete content:

```php
<?php

use App\Http\Controllers\ContractsManagementController;
use Illuminate\Support\Facades\Route;

Route::prefix('contracts-management')->middleware('auth')->name('contracts.')->group(function() {
    Route::get('/', [ContractsManagementController::class, 'index'])->name('index');
});

// Customer's own contract view - No permission check needed, users can view their own contract
Route::get('my-contract', [ContractsManagementController::class, 'myContract'])
    ->middleware('auth')
    ->name('contracts.my-contract');

// Contract Signing App - Fullpage (DEFAULT - No Laravel UI, just Flutter app)
Route::get('contract-signing', [ContractsManagementController::class, 'contractSigningFullpage'])
    ->middleware('auth')
    ->name('contract.signing');

// Contract Signing App - Embedded (With Laravel UI - for admins)
Route::get('contract-signing-embedded', [ContractsManagementController::class, 'contractSigning'])
    ->middleware('auth')
    ->name('contract.signing.embedded');

// Redirect old contract_signing URL to new protected route (for backward compatibility)
Route::get('/contract_signing', function() {
    return redirect()->route('contract.signing');
})->middleware('auth');
```

---

### STEP 12: Include Routes in web.php

**File:** `routes/web.php`
**Action:** Add this line among the other `require` statements (the project already groups them together):

```php
require __DIR__.'/contracts.php';
```

---

### STEP 13: Add API Routes

**File:** `routes/api.php`
**Action:** First, add the import at the top of the file:

```php
use App\Http\Controllers\APIs\ContractSignatureController;
```

Then append this route group at the end of the file:

```php
// Contract Signing API Routes (Protected with API Key)
Route::prefix('contract')->middleware('contract.api')->group(function () {
    Route::get('/get-company', [ContractSignatureController::class, 'getCompany']);
    Route::get('/check-signature', [ContractSignatureController::class, 'checkSignature']);
    Route::post('/upload', [ContractSignatureController::class, 'upload']);
});
```

---

### STEP 14: Create Permission & Menu Seeder

**File:** `database/seeders/ContractsMenuSeeder.php`
**Action:** Create this seeder to add the permission and sidebar menu:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ContractsMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Create the permission if it doesn't exist
        Permission::firstOrCreate([
            'name' => 'view-contracts',
            'guard_name' => 'web'
        ]);

        echo "✓ Permission 'view-contracts' created/verified\n";

        // 2. Assign permission to roles 20, 21, and 34
        $roleIds = [20, 21, 34]; // Super Admin, admin, SAC Officer
        foreach ($roleIds as $roleId) {
            $role = Role::find($roleId);
            if ($role) {
                if (!$role->hasPermissionTo('view-contracts')) {
                    $role->givePermissionTo('view-contracts');
                    echo "✓ Permission 'view-contracts' assigned to role: {$role->name} (ID: {$roleId})\n";
                } else {
                    echo "  Permission 'view-contracts' already assigned to role: {$role->name} (ID: {$roleId})\n";
                }
            } else {
                echo "⚠ Warning: Role with ID {$roleId} not found\n";
            }
        }

        // 3. Create menu item under Companies parent
        $companiesMenuId = '8c947bcc-a035-484f-8060-adc9c8d7a49a'; // Companies menu ID

        // Generate a unique UUID for the menu item
        $menuId = 'cc7e1ed5-f20f-42b6-bea9-eef26eacb001'; // Contracts menu UUID

        // Check if menu already exists
        $existingMenu = DB::table('level_two_menuses')->where('level_two_menu_id', $menuId)->first();

        if (!$existingMenu) {
            // Check if there's already a contracts menu by name
            $existingByName = DB::table('level_two_menuses')
                ->where('level_two_common_name', 'Manage Contracts')
                ->first();

            if ($existingByName) {
                echo "⚠ Menu item 'Manage Contracts' already exists with ID: {$existingByName->level_two_menu_id}\n";
                echo "  Updating existing menu item...\n";

                DB::table('level_two_menuses')
                    ->where('level_two_menu_id', $existingByName->level_two_menu_id)
                    ->update([
                        'level_two_gate_name' => 'view-contracts',
                        'level_two_menu_link' => 'contracts.index',
                        'level_two_icon_name' => 'icon-file-text2',
                        'level_two_menu_status' => 1,
                        'upper_level_id' => $companiesMenuId
                    ]);

                echo "✓ Menu item updated successfully\n";
            } else {
                DB::table('level_two_menuses')->insert([
                    'level_two_menu_id' => $menuId,
                    'level_two_common_name' => 'Manage Contracts',
                    'level_two_menu_status' => 1,
                    'level_two_icon_name' => 'icon-file-text2',
                    'level_two_menu_link' => 'contracts.index',
                    'level_two_gate_name' => 'view-contracts',
                    'upper_level_id' => $companiesMenuId,
                    'level_two_menu_created_at' => now(),
                    'level_two_menu_updated_at' => now()
                ]);

                echo "✓ Menu item 'Manage Contracts' created successfully\n";
            }
        } else {
            echo "  Menu item 'Manage Contracts' already exists\n";
        }

        echo "\n✓ All changes completed successfully!\n";
        echo "⚠ Remember to run: php artisan permission:cache-reset\n";
    }
}
```

**Then run:**
```bash
php artisan db:seed --class=ContractsMenuSeeder
php artisan permission:cache-reset
```

---

### STEP 15: Update Dashboard for External Users

**File:** `resources/views/Dashboards/ExternalUser.blade.php`
**Action:** Add the "MY CONTRACT" card after the last service card (before the closing `</div></div>`):

```blade
{{-- MY CONTRACT --}}
@if(Auth::user()->myCompany)
<div class="col-md-3">
    <a href="{{ route('contracts.my-contract') }}" class="text-decoration-none">
        <div class="service-card">
            <div class="service-icon">
                <i class="icon-file-text2"></i>
            </div>
            <div class="service-title">My Contract</div>
            <div class="service-desc">
                View and manage your signed contract.
            </div>
        </div>
    </a>
</div>
@endif
```

---

### STEP 16: Create Blade Views

Create the directory `resources/views/Contracts/` if it doesn't exist.

#### 16a: Admin Index View

**File:** `resources/views/Contracts/index.blade.php`
**Action:** Create with complete content (see original documentation for full blade code - unchanged from Step 13a)

#### 16b: Embedded Signing View

**File:** `resources/views/Contracts/signing.blade.php`
**Action:** Create with complete content (see original documentation for full blade code - unchanged from Step 13b)

#### 16c: Fullpage Signing View

**File:** `resources/views/Contracts/signing-fullpage.blade.php`
**Action:** Create with complete content (see original documentation for full blade code - unchanged from Step 13c)

---

### STEP 17: Add Logo Preloader to Flutter App

**File:** `public/cs_467807b523254a24356771dd94c810b552de5bacee1ef7ebb27b0de1461d1c81/index.html`
**Action:** Replace the `<head>` and `<body>` sections with:

```html
<head>
  <base href="/cs_467807b523254a24356771dd94c810b552de5bacee1ef7ebb27b0de1461d1c81/">

  <meta charset="UTF-8">
  <meta content="IE=Edge" http-equiv="X-UA-Compatible">
  <meta name="description" content="A new Flutter project.">

  <!-- iOS meta tags & icons -->
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-title" content="contract_signing_tantrade">
  <link rel="apple-touch-icon" href="icons/Icon-192.png">

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="favicon.png"/>

  <title>TanTrade E-Signature</title>
  <link rel="manifest" href="manifest.json">

  <style>
    #preloader {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: #ffffff;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      transition: opacity .4s ease;
    }

    #preloader.fade-out {
      opacity: 0;
      pointer-events: none;
    }

    #preloader img {
      width: 140px;
      margin-bottom: 28px;
    }

    .loader-spinner {
      width: 40px;
      height: 40px;
      border: 4px solid #e5e7eb;
      border-top: 4px solid #1a237e;
      border-radius: 50%;
      animation: spin .8s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    #preloader p {
      margin-top: 18px;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      font-size: 14px;
      color: #6b7280;
    }
  </style>
</head>
<body>
  <!-- Preloader - shows while Flutter initializes -->
  <div id="preloader">
    <img src="../images/logo.jpg" alt="TanTrade" onerror="this.style.display='none'">
    <div class="loader-spinner"></div>
    <p>Loading contract signing...</p>
  </div>

  <script src="flutter_bootstrap.js" async></script>

  <script>
    // Hide preloader once Flutter renders its first frame
    window.addEventListener('flutter-first-frame', function () {
      var el = document.getElementById('preloader');
      if (el) {
        el.classList.add('fade-out');
        setTimeout(function () { el.remove(); }, 400);
      }
    });
  </script>
</body>
```

---

### STEP 18: Create Storage Directories

Run these commands:

```bash
mkdir -p storage/app/public/signatures
php artisan storage:link
```

**Note:** Removed `signed_contracts` directory as PDFs are no longer stored.

---

### STEP 19: Clear Caches

```bash
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear
php artisan permission:cache-reset
```

---

## POST-IMPLEMENTATION: FIX myCompany LINKING

**Common Issue:** External users may have companies but `myCompany` field is NULL, preventing "MY CONTRACT" card from showing.

### Diagnostic Script

Run this to find users with missing myCompany links:

```bash
php artisan tinker --execute="
\$orphanedUsers = DB::table('users')
    ->leftJoin('companies', 'users.email', '=', 'companies.companyEmail')
    ->whereNotNull('companies.companyID')
    ->whereNull('users.myCompany')
    ->where('users.account_type', 2)
    ->select('users.id', 'users.email', 'companies.companyID', 'companies.companyName')
    ->get();

echo 'Found ' . \$orphanedUsers->count() . ' users with companies but NULL myCompany' . PHP_EOL;
foreach (\$orphanedUsers as \$user) {
    echo '  - ' . \$user->email . ' => ' . \$user->companyName . PHP_EOL;
}
"
```

### Fix Script

Create a seeder or run in tinker:

```php
// Link users to their companies by matching email
$results = DB::table('users')
    ->join('companies', 'users.email', '=', 'companies.companyEmail')
    ->where('users.account_type', 2)
    ->whereNull('users.myCompany')
    ->select('users.id as user_id', 'companies.companyID')
    ->get();

foreach ($results as $result) {
    DB::table('users')
        ->where('id', $result->user_id)
        ->update(['myCompany' => $result->companyID]);
}

echo "✓ Linked {$results->count()} users to their companies\n";
```

---

## FEATURE OVERVIEW

### Admin Features (Roles 20, 21, 34)

1. **Sidebar Menu** - "Manage Contracts" appears under Companies section
2. **Contracts List** - View all signed contracts at `/contracts-management`
3. **View Contracts** - Click "View" to open contract in Flutter app
4. **Permission Gate** - `view-contracts` permission required

### Customer Features (All External Users with myCompany)

1. **Dashboard Card** - "MY CONTRACT" card appears on external user dashboard
2. **Direct Access** - Redirects to their own contract using their TSRN
3. **No Permission** - No special permission required, users can always view own contract
4. **Security** - Users can only see contracts for their linked company

### Technical Features

1. **Flutter Web App** - Embedded at obscured path for security
2. **Logo Preloader** - Shows TanTrade logo while Flutter initializes
3. **CSRF Exception** - Upload endpoints work without CSRF tokens
4. **API Authentication** - All API calls require `X-API-Key` header
5. **Signature Storage** - Only signatures stored, PDF generated on-the-fly
6. **Rate Limiting** - Built-in middleware (optional)

---

## DATABASE SCHEMA REFERENCE

### Table: `signed_contracts`

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| `id` | BIGINT UNSIGNED | NO | Auto-increment primary key |
| `registration_id` | VARCHAR(100) | NO | Links to `companies.registrationID` |
| `signer_type` | ENUM('exhibitor','witness') | NO | Type of signer |
| `first_name` | VARCHAR(100) | NO | Signer's first name |
| `middle_name` | VARCHAR(100) | NO | Signer's middle name |
| `last_name` | VARCHAR(100) | NO | Signer's last name |
| `qualification` | VARCHAR(200) | YES | Job title/role |
| `address` | TEXT | YES | Signer's address |
| `signature_path` | VARCHAR(255) | NO | Storage path to signature image |
| `related_signature_id` | BIGINT UNSIGNED | YES | Links exhibitor to witness signature |
| `created_at` | TIMESTAMP | YES | Auto-managed by Laravel |
| `updated_at` | TIMESTAMP | YES | Auto-managed by Laravel |

**Indexes:** `idx_registration` on `registration_id`, `idx_signer_type` on `signer_type`, `idx_related` on `related_signature_id`

### Table: `companies` (existing - key columns used)

| Column | Used By |
|--------|---------|
| `registrationID` | JOIN key in contracts query, API lookup |
| `companyName` | Displayed in admin panel |
| `companyEmail` | Displayed in admin panel, user linking |
| `companyPhone` | Displayed in admin panel |
| `companyAddress` | Returned by API |

### Table: `users` (existing - key column added)

| Column | Used By |
|--------|---------|
| `myCompany` | Links user to their company, determines "MY CONTRACT" visibility |

---

## API REFERENCE

### Authentication
All endpoints require header: `X-API-Key: {CONTRACT_API_KEY from .env}`

### GET /api/contract/get-company

**Query params:** `registrationID` (required)

**Success (200):**
```json
{
  "status": 1,
  "message": "Company found",
  "data": {
    "company_name": "...",
    "address": "...",
    "email": "...",
    "phone": "..."
  }
}
```

**Not found (404):** `{"status": 0, "message": "Company not found with this Registration ID"}`
**No API key (401):** `{"status": 0, "message": "Unauthorized. Invalid API key."}`

### GET /api/contract/check-signature

**Query params:** `registrationID` (required)

**No signatures:**
```json
{"status": 1, "has_signature": false, "is_complete": false, "message": "No signatures found"}
```

**Has signatures:**
```json
{
  "status": 1,
  "has_signature": true,
  "is_complete": true,
  "data": { "id": 1, "signer_type": "exhibitor", "first_name": "...", ... },
  "signatures": [...]
}
```

### POST /api/contract/upload

**Content-Type:** `multipart/form-data`

**Fields:**
| Field | Type | Required | Constraints |
|-------|------|----------|-------------|
| `registration_id` | string | YES | |
| `first_name` | string | YES | max:100 |
| `middle_name` | string | YES | max:100 |
| `last_name` | string | YES | max:100 |
| `qualification` | string | NO | max:200 |
| `address` | string | NO | |
| `signer_type` | enum | YES | "exhibitor" or "witness" |
| `signature_image` | file | YES | PNG/JPG, max 2MB |
| `existing_signature_id` | integer | NO | Links to first signer's ID |

**Success (200):**
```json
{
  "status": 1,
  "message": "Signature uploaded successfully",
  "signature_id": 1,
  "both_signatures_complete": false
}
```

---

## ROUTE SUMMARY

| Method | URI | Controller@Method | Middleware | Name |
|--------|-----|-------------------|------------|------|
| GET | `/contracts-management` | ContractsManagementController@index | auth, can:view-contracts | contracts.index |
| GET | `/my-contract` | ContractsManagementController@myContract | auth | contracts.my-contract |
| GET | `/contract-signing` | ContractsManagementController@contractSigningFullpage | auth | contract.signing |
| GET | `/contract-signing-embedded` | ContractsManagementController@contractSigning | auth | contract.signing.embedded |
| GET | `/contract_signing` | Redirect to contract.signing | auth | - |
| GET | `/api/contract/get-company` | ContractSignatureController@getCompany | contract.api | - |
| GET | `/api/contract/check-signature` | ContractSignatureController@checkSignature | contract.api | - |
| POST | `/api/contract/upload` | ContractSignatureController@upload | contract.api | - |

---

## SECURITY LAYERS

1. **Laravel Auth Middleware** - All web routes require login (`middleware('auth')`)
2. **Permission Gates** - Admin routes require `view-contracts` permission
3. **User Isolation** - Customers only see their own contract via `myCompany` link
4. **API Key Middleware** (`contract.api`) - Validates `X-API-Key` header against `config('services.contract_api.key')`
5. **CSRF Exception** - Upload endpoints excluded from CSRF verification
6. **Rate Limiter** (`contract.ratelimit`) - 10 requests/min per registration ID (available but not applied by default)
7. **HMAC Signature** (`contract.signature`) - Validates `X-Signature` + `X-Timestamp` headers (available but not applied by default)
8. **Obscured Flutter folder** - 64-character random hash prevents directory guessing

---

## SIGNING FLOW

### Admin Flow
1. Admin logs in (role 20, 21, or 34)
2. Sees "Manage Contracts" in sidebar under Companies
3. Views contracts list at `/contracts-management`
4. Clicks "View" to open specific contract in Flutter app

### Customer Flow
1. Customer logs in (account_type=2, must have myCompany set)
2. Sees "MY CONTRACT" card on dashboard
3. Clicks card → Redirects to `/my-contract`
4. Controller fetches their company's TSRN
5. Redirects to Flutter app with their TSRN

### Signing Flow (Both)
1. Flutter app loads with TanTrade logo preloader
2. User enters/has registration ID → Flutter calls `GET /api/contract/get-company`
3. Flutter calls `GET /api/contract/check-signature` to check existing signatures
4. **Exhibitor signs first:** fills form, draws signature, Flutter calls `POST /api/contract/upload` with `signer_type=exhibitor`
5. **Witness signs second:** same flow, but includes `existing_signature_id` from exhibitor's response
6. Upload endpoint links the two signatures via `related_signature_id`
7. Admin views all contracts with completion status

---

## TROUBLESHOOTING

| Problem | Cause | Fix |
|---------|-------|-----|
| 404 on `/contract-signing` | Routes not loaded | Ensure `require __DIR__.'/contracts.php';` is in `routes/web.php`, then `php artisan route:clear` |
| 401 on API calls | Missing or wrong API key | Check `.env` has `CONTRACT_API_KEY`, check `config/services.php` has `contract_api` entry, run `php artisan config:clear` |
| 419 on upload | CSRF token required | Add contract endpoints to `$except` array in `VerifyCsrfToken.php` middleware |
| 403 on `/contracts-management` | Missing permission | Run `ContractsMenuSeeder`, verify user has role 20/21/34, run `php artisan permission:cache-reset` |
| "MY CONTRACT" not visible | myCompany is NULL | Run myCompany linking script (see "POST-IMPLEMENTATION" section) |
| 500 on `get-company` | Column name mismatch | Verify `companies` table has `companyName`, `companyEmail`, `companyPhone`, `companyAddress` columns |
| Blank Flutter iframe | Missing Flutter build | Deploy Flutter web build to `public/cs_467807b...81/` |
| Storage upload fails | Missing directories | Run `mkdir -p storage/app/public/signatures && php artisan storage:link` |
| Middleware not found | Not registered in Kernel | Verify `contract.api`, `contract.ratelimit`, `contract.signature` are in `$routeMiddleware` in `app/Http/Kernel.php` |
| No preloader shows | index.html not updated | Update Flutter app's `index.html` with logo preloader code (Step 17) |

---

## VERIFICATION CHECKLIST

After implementation, verify each item:

- [ ] `php artisan route:list | grep contract` shows all 5 web routes + 3 API routes
- [ ] Visiting `/contract-signing` while logged out redirects to login
- [ ] Visiting `/contract-signing` while logged in shows Flutter app with logo preloader
- [ ] `curl -H "X-API-Key: C0000-7066-767-1-KBmachin3" http://127.0.0.1:8000/api/contract/get-company?registrationID=VALID_ID` returns company data
- [ ] `curl http://127.0.0.1:8000/api/contract/get-company?registrationID=VALID_ID` returns 401
- [ ] `/contracts-management` shows admin table (visible only to roles 20/21/34)
- [ ] External users see "MY CONTRACT" card on dashboard (if myCompany is set)
- [ ] Clicking "MY CONTRACT" redirects to their specific contract
- [ ] `storage/app/public/signatures/` directory exists
- [ ] `public/storage` symlink exists (from `php artisan storage:link`)
- [ ] Permission cache cleared: `php artisan permission:cache-reset`

---

## COMPLETE FILE MAP

Files to **create** (do not exist in older project):

```
app/Http/Controllers/ContractsManagementController.php          (Step 10)
app/Http/Controllers/APIs/ContractSignatureController.php       (Step 9)
app/Http/Middleware/ValidateContractApiKey.php                   (Step 4)
app/Http/Middleware/ContractApiRateLimiter.php                   (Step 5)
app/Http/Middleware/ValidateContractSignature.php                (Step 6)
database/migrations/2026_02_10_084315_create_signed_contracts_table.php  (Step 3)
database/seeders/ContractsMenuSeeder.php                         (Step 14)
resources/views/Contracts/index.blade.php                       (Step 16a)
resources/views/Contracts/signing.blade.php                     (Step 16b)
resources/views/Contracts/signing-fullpage.blade.php            (Step 16c)
routes/contracts.php                                            (Step 11)
```

Files to **modify** (already exist):

```
.env                                         (Step 1 - append 2 lines)
config/services.php                          (Step 2 - add contract_api array)
app/Http/Kernel.php                          (Step 7 - add 3 middleware aliases)
app/Http/Middleware/VerifyCsrfToken.php      (Step 8 - add 4 exception routes)
routes/web.php                               (Step 12 - add 1 require line)
routes/api.php                               (Step 13 - add 1 import + 1 route group)
resources/views/Dashboards/ExternalUser.blade.php  (Step 15 - add MY CONTRACT card)
resources/flutter/contract-signing/index.html      (Deployed via script - custom preloader auto-injected)
```

---

## PRE-DEPLOYMENT CHECKLIST

Before deploying the contracts feature to production at https://tradefair.tantrade.go.tz/, complete these steps to avoid failures:

### Step 1: Verify Database Prerequisites

Check that production database has the required structure:

```bash
# Connect to production database
mysql -u username -p database_name

# Verify companies table structure
mysql> DESCRIBE companies;
# Should have: companyID, registrationID, companyName, companyEmail, companyPhone, companyAddress

# Verify menu tables exist
mysql> SHOW TABLES LIKE '%menu%';
# Should show: level_one_menuses, level_two_menuses

# Verify role IDs exist
mysql> SELECT id, name FROM roles WHERE id IN (20, 21, 34);
# Should return 3 roles that need access to contracts feature
```

### Step 2: Deployment Commands (Run IN ORDER)

After deploying code to production, execute these commands:

```bash
# Navigate to production directory
cd /path/to/production

# Run database migration to create signed_contracts table
php artisan migrate --force

# Seed permissions and menu entries
php artisan db:seed --class=ContractsMenuSeeder --force

# Create storage directory for signatures
mkdir -p storage/app/public/contract_signatures
chmod -R 775 storage/app/public/contract_signatures
chown -R www-data:www-data storage/app/public/contract_signatures

# Clear all caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan permission:cache-reset
```

### Step 3: Add Environment Variables

Add these to production `.env` file (generate secure random strings):

```bash
# Contract API Security Configuration
CONTRACT_API_KEY=<generate-secure-random-key-here>
CONTRACT_API_SECRET=<generate-secure-random-secret-here>
```

**Important:** Generate strong random keys. Example:
```bash
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```

### Step 4: Deploy Flutter App (Using Secure Script)

If deploying from a development environment with Flutter installed:

```bash
# Use the updated deployment script
# Script location: /path/to/deploy.sh
#
# The script will:
# 1. Build Flutter web app with correct base-href
# 2. Auto-inject custom TanTrade preloader
# 3. Deploy to resources/flutter/contract-signing/ (secure location)
# 4. Set proper permissions
# 5. Create storage directories

./deploy.sh
```

**Manual deployment (if Flutter not available):**
```bash
# Copy pre-built Flutter app to secure location
cp -r /path/to/flutter/build/web/* resources/flutter/contract-signing/

# Ensure index.html has correct base href:
# <base href="/contract-signing-app/">

# Set permissions
chmod -R 755 resources/flutter/contract-signing/
```

### Step 5: Verify Deployment

Test that the feature is working:

```bash
# Test 1: Check routes registered (should see contract-signing-app routes)
php artisan route:list | grep contract

# Test 2: Verify old public URL returns 404 (security check)
curl -I https://tradefair.tantrade.go.tz/cs_467807b523254a24356771dd94c810b552de5bacee1ef7ebb27b0de1461d1c81/
# Expected: 404 Not Found

# Test 3: Verify new route requires auth (should redirect to login)
curl -I https://tradefair.tantrade.go.tz/contract-signing-app
# Expected: 302 redirect to login

# Test 4: Check contracts management page loads
curl -I https://tradefair.tantrade.go.tz/contracts-management

# Test 5: Check API endpoint (with your actual API key)
curl -H "X-API-Key: YOUR_CONTRACT_API_KEY" \
  "https://tradefair.tantrade.go.tz/api/contract/get-company?registrationID=TEST_ID"

# Test 6: Verify storage directory exists and is writable
ls -la storage/app/public/contract_signatures/
touch storage/app/public/contract_signatures/test.txt && rm storage/app/public/contract_signatures/test.txt

# Test 7: Verify Flutter app files deployed correctly
ls -la resources/flutter/contract-signing/
# Should contain: index.html, main.dart.js, assets/, canvaskit/, etc.
```

### Step 6: Post-Deployment Verification

**Security Verification:**
- [ ] Logout and try accessing `/contract-signing-app` → Should redirect to login ✅
- [ ] Try accessing old URL `/cs_467.../` → Should return 404 ✅
- [ ] Login and access contract → Should load successfully ✅

**Admin Testing (roles 20, 21, 34):**
- [ ] Login as admin user
- [ ] Verify "Manage Contracts" appears in sidebar under Companies menu
- [ ] Access `/contracts-management` → Should show admin table
- [ ] Click "View" on any contract → Should open in new tab
- [ ] Verify back button says "Back to Contracts" and returns to table
- [ ] Press ESC key → Should return to contracts table
- [ ] Test that admin can view any contract (not just their own)

**Customer Testing:**
- [ ] Login as external user with `myCompany` set
- [ ] Verify "MY CONTRACT" card appears on dashboard
- [ ] Click "View & Sign Contract" → Should load Flutter signing interface
- [ ] Verify back button says "Back to Dashboard" and returns to dashboard
- [ ] Test signature upload functionality
- [ ] Check `storage/app/public/contract_signatures/` for saved files
- [ ] Press ESC key → Should return to dashboard
- [ ] Verify customer CANNOT access other companies' contracts (403 error)

### Common Deployment Issues

| Issue | Cause | Solution |
|-------|-------|----------|
| 💥 500 error on contracts page | Migration not run | Run `php artisan migrate --force` |
| 🚫 Menu item not visible | Seeder not run | Run `php artisan db:seed --class=ContractsMenuSeeder --force` then `php artisan permission:cache-reset` |
| 💥 401 on API calls | Missing API keys in .env | Add `CONTRACT_API_KEY` and `CONTRACT_API_SECRET` to .env, then run `php artisan config:clear` |
| 💥 Signature upload fails | Storage directory missing or not writable | Create directory and set proper permissions (see Step 2) |
| 🔧 Old config cached | Config not cleared | Run all cache clear commands (see Step 2) |
| 💥 API returns wrong data | Companies table column mismatch | Verify production `companies` table has exact column names: `companyName`, `companyEmail`, etc. |

### Critical Notes

⚠️ **NEVER commit `.env` file** - API keys must be added manually on production server

⚠️ **Backup database** before running migrations on production

⚠️ **Test on staging first** if available

⚠️ **Flutter web app files** - Must be deployed to `resources/flutter/contract-signing/` (not public directory). Use the deployment script for proper setup (~3.5MB)

⚠️ **Security verification** - After deployment, confirm old public URLs return 404 and new routes require authentication

---

## FLUTTER BUILD & DEPLOYMENT SCRIPT

For development/staging environments where you have Flutter installed, use this automated deployment script.

### Script Purpose

This script automates the secure deployment of the Flutter contract signing app:

1. ✅ Builds Flutter web app with correct base href
2. ✅ Auto-injects custom TanTrade preloader with logo
3. ✅ Deploys to secure location (`resources/flutter/`, not `public/`)
4. ✅ Sets proper file permissions
5. ✅ Creates required storage directories

### Script Location

Save as: `/path/to/deploy.sh` (or update paths in script)

```bash
#!/bin/bash

# Configuration - SECURE DEPLOYMENT (resources/flutter, not public)
FLUTTER_PROJECT="/Users/macbook/Documents/apps/contract_signing_tantrade"
LARAVEL_PROJECT="/Users/macbook/tradefair.tantrade.go.tz"

# IMPORTANT: Now deploys to resources/flutter (protected by Laravel auth), not public
TARGET_DIR="$LARAVEL_PROJECT/resources/flutter/contract-signing"

echo "🚀 Starting Flutter deployment..."

# Navigate to Flutter project
cd "$FLUTTER_PROJECT" || { echo "❌ Could not find Flutter project"; exit 1; }

# Build Flutter web app with correct base href
echo "📦 Building Flutter app..."
flutter build web --release --base-href "/contract-signing-app/"

# Inject custom preloader (auto-adds TanTrade logo and loading animation)
echo "🎨 Injecting custom TanTrade preloader..."
INDEX_FILE="build/web/index.html"

# [Preloader injection code - see full script for details]
# This automatically adds:
# - Custom CSS for preloader styling
# - HTML for logo, spinner, and loading text
# - JavaScript to hide preloader when Flutter loads

echo "✨ Custom preloader injected successfully!"

# Deploy to Laravel (secure location)
echo "📁 Preparing target directory..."
mkdir -p "$TARGET_DIR"

echo "🗑️  Removing old files..."
rm -rf "$TARGET_DIR"/*

echo "📋 Copying new build..."
cp -r build/web/* "$TARGET_DIR/"

echo "🔐 Setting permissions..."
chmod -R 755 "$TARGET_DIR"

# Create storage directories
echo "📂 Checking upload directories..."
mkdir -p "$LARAVEL_PROJECT/storage/app/public/contract_signatures"
chmod -R 775 "$LARAVEL_PROJECT/storage/app/public/contract_signatures"

echo "✅ Deployment complete!"
echo ""
echo "🔒 SECURITY: Flutter app is now protected by Laravel authentication"
echo "   - App files in resources/flutter (not accessible directly)"
echo "   - Must login before accessing contract signing"
echo ""
echo "📱 Access: http://127.0.0.1:8000/contract-signing?registrationID=TEST"
```

### Usage

```bash
# Make executable
chmod +x /path/to/deploy.sh

# Run deployment
./deploy.sh
```

### What It Does

**Before (Insecure):**
```
Flutter build → public/cs_467.../ → Directly accessible ❌
```

**After (Secure):**
```
Flutter build → Auto-inject preloader → resources/flutter/ → Laravel auth required ✅
```

### Notes

- Script uses `sed` to inject preloader HTML/CSS/JS into generated `index.html`
- Preserves Flutter's original functionality while adding custom branding
- No manual editing of `index.html` needed after builds
- Deploy to production by copying `resources/flutter/contract-signing/` directory

---

## CHANGELOG

### Version 4.0 - Secure Architecture (February 13, 2026)
**🔒 MAJOR SECURITY UPDATE**

**Breaking Changes:**
- ⚠️ Flutter app moved from `public/` to `resources/flutter/contract-signing/`
- ⚠️ Old public URLs (`/cs_467.../`) no longer work (by design)
- ⚠️ All contract access now requires authentication
- ⚠️ Deploy script updated - old script will deploy to wrong location

**Security Improvements:**
- ✅ Flutter app served through Laravel authentication (not publicly accessible)
- ✅ Protected routes: `/contract-signing-app` and `/contract-signing-app/{asset}`
- ✅ Role-aware access control: Admins can view any contract, customers only their own
- ✅ Directory traversal prevention in asset serving
- ✅ Role-aware back button: Returns to contracts table (admins) or dashboard (customers)
- ✅ Removed duplicate preloaders for cleaner UX

**Deployment:**
- ✅ Updated deploy script with secure paths and auto-preloader injection
- ✅ Base href changed from `/cs_467.../` to `/contract-signing-app/`
- ✅ Storage moved to `storage/app/public/contract_signatures/`
- ✅ Added comprehensive pre-deployment checklist

**Controller Updates:**
- ✅ Added `serveFlutterApp()` - serves index.html with auth
- ✅ Added `serveFlutterAsset()` - serves assets with auth and MIME type handling
- ✅ Updated `contractSigningFullpage()` - validates user access to specific contracts
- ✅ Fixed `myContract()` - redirects to protected route instead of public URL

**View Updates:**
- ✅ `signing-fullpage.blade.php` - role-aware back button and navigation
- ✅ `index.blade.php` - uses protected routes instead of public URLs
- ✅ ESC key and post-signature redirects are role-aware

**Routes:**
- ✅ Added protected Flutter app routes with auth middleware
- ✅ All routes go through Laravel authentication
- ✅ Asset serving secured with wildcard route matching

### Version 3.0 (February 13, 2026)
- ✅ Added "Manage Contracts" sidebar menu for admin users (roles 20, 21, 34)
- ✅ Added "MY CONTRACT" dashboard card for external users
- ✅ Added myContract() controller method for customer self-service
- ✅ Added logo preloader to Flutter app index.html
- ✅ Added CSRF exceptions for contract upload endpoints
- ✅ Updated middleware to exclude customer routes from permission check
- ✅ Removed PDF storage requirement (signatures only, PDF generated on-the-fly)
- ✅ Added ContractsMenuSeeder for permission and menu setup
- ✅ Documented myCompany linking issue and fix

### Version 2.0 (February 11, 2026)
- Initial documentation with API, admin panel, and Flutter integration

---

**END OF IMPLEMENTATION GUIDE**
