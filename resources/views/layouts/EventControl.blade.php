@php
    $currentEvent = App\Models\settings\events::where(
        'event_id',
        session('liveEvent')
    )->first(['event_name']);
@endphp

<style>
    .event-controller {
        background: linear-gradient(135deg, #ffffff, #f8fafc);
        border-radius: 14px;
        padding: 24px 28px;
        box-shadow: 0 8px 24px rgba(0,0,0,.06);
        margin-bottom: 24px;
    }

    .event-controller-title {
        font-size: 18px;
        font-weight: 700;
        color: #1e3a8a;
        margin-bottom: 6px;
    }

    .event-controller-desc {
        font-size: 14px;
        color: #475569;
        line-height: 1.7;
        max-width: 900px;
    }

    .event-controller-footer {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 18px;
        padding-top: 14px;
        border-top: 1px solid #e5e7eb;
        font-size: 13px;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .event-badge {
        background: #f59e0b;
        color: #fff;
        padding: 6px 14px;
        border-radius: 999px;
        font-weight: 600;
        font-size: 12px;
        white-space: nowrap;
    }
</style>

<div class="event-controller">

    <div class="event-controller-title">
        Space Allocation Management System (SAMS)
    </div>

    <div class="event-controller-desc">
        Manage all exhibition business processes including exhibitor registration,
        space and service booking, entrance and exit permits, badge management, and gate control.
    </div>

    <div class="event-controller-footer">
        <i class="icon-calendar22 text-primary"></i>
        <span>Current Event</span>
        <span class="event-badge">
            {{ $currentEvent->event_name ?? 'NOT SET' }}
        </span>
    </div>

</div>
