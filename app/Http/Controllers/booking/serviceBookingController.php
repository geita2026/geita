<?php

namespace App\Http\Controllers\booking;

use App\Http\Controllers\Controller;
use App\Models\advice_note\service_charges;
use App\Models\companies;
use App\Models\exhibition\exhibitors;
use App\Models\settings\events;
use App\Models\space\additional_services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class serviceBookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return $this->bookingPage($request);
    }

    public function create(Request $request)
    {
        return $this->bookingPage($request);
    }

    public function show(Request $request, string $id)
    {
        return $this->bookingPage($request, $id);
    }

    private function bookingPage(Request $request, ?string $eventId = null)
    {
        if ($request->user()->cannot('BOOK_SERVICES')) {
            return back()->with('fail', $this->permissionDeniedMessage('BOOK_SERVICES'));
        }

        $pendingServices = $this->draftQuery($request)
            ->leftJoin('companies', 'service_charges.company_id', '=', 'companies.companyID')
            ->leftJoin('additional_services', 'service_charges.service_id', '=', 'additional_services.serviceID')
            ->get([
                'service_charges.*', 'companies.companyName',
                'additional_services.serviceName', 'additional_services.serviceCost',
                'additional_services.type_of_currency', 'additional_services.service_event',
            ]);
        $draft = $pendingServices->first();
        $eventId = $draft?->exhibition_year ?? $draft?->service_event ?? $eventId ?? $this->currentEvent($request);
        $event = $eventId ? events::find($eventId) : null;
        $services = $event
            ? $this->serviceOptions($request, $eventId, $draft?->type_of_currency)
                ->whereNotIn('serviceID', $pendingServices->pluck('service_id')->filter())
                ->orderBy('serviceName')->get()
            : collect();
        $companies = $event && !$draft
            ? $this->companyOptions($request, $eventId)->orderBy('companyName')->get(['companyID', 'companyName', 'registrationID'])
            : collect();
        $bookingOpen = (bool) config('global.BOOKING_STATUS');

        return view('booking.book-service', compact('event', 'services', 'companies', 'pendingServices', 'draft', 'bookingOpen'));
    }

    public function store(Request $request)
    {
        if ($request->user()->cannot('BOOK_SERVICES')) {
            return back()->with('fail', $this->permissionDeniedMessage('BOOK_SERVICES'));
        }
        if (!config('global.BOOKING_STATUS')) {
            return back()->with('fail', 'Service booking is currently closed.');
        }

        $data = $request->validate([
            'customer_name' => ['required', 'string'],
            'service_name' => ['required', 'string'],
            'service_quantity' => ['required', 'integer', 'min:1'],
            'exhibition_year' => ['nullable', 'string', 'exists:events,event_id'],
        ]);
        $eventId = DB::transaction(function () use ($request, $data) {
            $request->user()->newQuery()->whereKey($request->user()->getKey())->lockForUpdate()->firstOrFail();

            $draft = $this->draftQuery($request)
                ->leftJoin('additional_services', 'service_charges.service_id', '=', 'additional_services.serviceID')
                ->first(['service_charges.company_id', 'service_charges.exhibition_year',
                    'additional_services.type_of_currency', 'additional_services.service_event']);
            $eventId = $draft?->exhibition_year ?? $draft?->service_event ?? $data['exhibition_year'] ?? $this->currentEvent($request);

            if (!$eventId || ($draft && $data['customer_name'] !== $draft->company_id)
                || !$this->companyOptions($request, $eventId)->whereKey($data['customer_name'])->exists()) {
                throw ValidationException::withMessages(['customer_name' => 'Select an eligible company for this event.']);
            }
            if ($this->draftQuery($request)->where('service_id', $data['service_name'])->exists()) {
                throw ValidationException::withMessages(['service_name' => 'This service is already in your booking.']);
            }
            if (!$this->serviceOptions($request, $eventId, $draft?->type_of_currency)
                    ->whereKey($data['service_name'])->exists()) {
                throw ValidationException::withMessages(['service_name' => 'Select an available service for this booking.']);
            }

            service_charges::create([
                'service_charge_id' => Str::uuid(),
                'company_id' => $data['customer_name'],
                'user_id' => $request->user()->user_uuid,
                'service_id' => $data['service_name'],
                'exhibition_year' => $eventId,
                'service_quantity' => $data['service_quantity'],
            ]);

            return $eventId;
        }, 3);

        $request->session()->put('liveEvent', $eventId);

        return back()->with('success', 'Service added to your booking.');
    }

    public function destroy(Request $request, string $id)
    {
        if ($request->user()->cannot('CANCEL_SERVICE_BOOKING')) {
            return back()->with('fail', $this->permissionDeniedMessage('CANCEL_SERVICE_BOOKING'));
        }

        $this->draftQuery($request)->whereKey($id)->firstOrFail()->delete();

        return back()->with('success', 'Service removed from your booking.');
    }

    private function currentEvent(Request $request): ?string
    {
        return $request->session()->get('liveEvent')
            ?? events::where('event_status', 1)->value('event_id');
    }

    private function draftQuery(Request $request)
    {
        return service_charges::where('service_charges.user_id', $request->user()->user_uuid)
            ->whereNull('service_charges.advice_note_id')
            ->orderBy('service_charges.service_charge_id');
    }

    private function serviceOptions(Request $request, ?string $eventId, ?string $currency = null)
    {
        return additional_services::where('service_event', $eventId)
            ->whereNotNull('gfs_code')
            ->when($currency, fn ($query) => $query->where('type_of_currency', $currency))
            ->when($request->user()->cannot('BOOK_ALL_SERVICE'), fn ($query) => $query->where('self_booking', 1));
    }

    private function companyOptions(Request $request, ?string $eventId)
    {
        $viewer = $request->user();

        return companies::where('status', 1)
            ->whereIn('companyID', exhibitors::where('exhibitor_event', $eventId)->select('exhibitor_id'))
            ->when((int) $viewer->account_type === 2, function ($query) use ($viewer) {
                $query->whereExists(function ($memberships) use ($viewer) {
                    $memberships->selectRaw('1')->from('company_admins')
                        ->whereColumn('company_admins.companyId', 'companies.companyID')
                        ->where('company_admins.adminId', $viewer->user_uuid);
                });
            });
    }
}
