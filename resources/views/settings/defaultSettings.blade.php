<x-app-layout>
    <title>{{ env('APP_NAME') }} | System Settings</title>

    <style>
        .settings-wrapper {
            background: #f3f6fb;
            padding: 28px;
        }

        .settings-section {
            background: #ffffff;
            border-radius: 16px;
            padding: 26px;
            margin-bottom: 28px;
            box-shadow: 0 8px 22px rgba(0,0,0,.06);
        }

        .settings-title {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #111827;
            margin-bottom: 4px;
        }

        .settings-desc {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .info-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 18px;
        }

        .badge-status,
        .badge-event {
            padding: 7px 16px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .06em;
            display: inline-block;
        }

        .badge-closed { background:#fee2e2; color:#991b1b; }
        .badge-domestic { background:#e0f2fe; color:#075985; }
        .badge-foreign { background:#dcfce7; color:#166534; }
        .badge-all { background:#ede9fe; color:#5b21b6; }

        .badge-event {
            background:#ecfeff;
            color:#0f766e;
        }

        .form-group label {
            font-weight: 700;
            color: #374151;
        }

        .sticky-save {
            position: sticky;
            bottom: 0;
            background: #ffffff;
            padding: 18px;
            border-top: 1px solid #e5e7eb;
            text-align: right;
            z-index: 20;
        }

        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 22px 0;
        }
    </style>

    {{-- PAGE HEADER --}}
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a><i class="icon-gear position-left"></i> System Settings</a></li>
                <li class="active">Defaults & Application Control</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>

    <div class="settings-wrapper">

        @php
            $activeEvent = $events->firstWhere('event_status', 1);
            $status = $item?->applicationStatus ?? 'CLOSED';
        @endphp

        <form action="{{ route('default_settings.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <input type="hidden" name="default_setting_id" value="{{ $item->default_setting_id ?? '' }}">

            {{-- ================= CURRENT SYSTEM STATE ================= --}}
            <div class="settings-section">
                <div class="settings-title">Current System State</div>
                <div class="settings-desc">
                    Snapshot of the system’s current operational context. This helps administrators
                    understand what is active before making changes.
                </div>

                <div class="info-box">
                    <strong>Active Exhibition Event</strong><br><br>

                    @if ($activeEvent)
                        <span class="badge-event">
                            {{ $activeEvent->event_name }}
                            ({{ date('M d, Y', strtotime($activeEvent->event_start_date)) }}
                            –
                            {{ date('M d, Y', strtotime($activeEvent->event_end_date)) }})
                        </span>
                    @else
                        <span class="badge-status badge-closed">
                            No Active Event Configured
                        </span>
                    @endif
                </div>

                <div class="info-box">
                    <strong>Application Access Status</strong><br><br>

                    @if ($status === 'CLOSED')
                        <span class="badge-status badge-closed">CLOSED</span>
                    @elseif ($status === 'OPENED_FOR_DOMESTIC')
                        <span class="badge-status badge-domestic">OPENED FOR DOMESTIC</span>
                    @elseif ($status === 'OPENED_FOR_FOREIGNERS')
                        <span class="badge-status badge-foreign">OPENED FOR FOREIGNERS</span>
                    @else
                        <span class="badge-status badge-all">OPENED FOR ALL</span>
                    @endif
                </div>
            </div>

            {{-- ================= EVENT CONTROL ================= --}}
            <div class="settings-section">
                <div class="settings-title">Event Control</div>
                <div class="settings-desc">
                    Defines which exhibition event is currently active system-wide.
                    Only one event can be active at any time.
                </div>

                <div class="form-group">
                    <label>Select Active Event <span class="text-danger">*</span></label>
                    <select name="active_event" class="form-control" required>
                        <option value="">-- Select Event --</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->event_id }}"
                                {{ ($item->active_event ?? '') === $event->event_id ? 'selected' : '' }}>
                                {{ $event->event_name }}
                                ({{ $event->event_start_date }} – {{ $event->event_end_date }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- ================= SYSTEM IDENTITY ================= --}}
            <div class="settings-section">
                <div class="settings-title">System Identity & Notifications</div>
                <div class="settings-desc">
                    Controls system naming, versioning, and how notifications are delivered.
                </div>

                <div class="row">
                    <div class="col-md-3 form-group">
                        <label>System Title <span class="text-danger">*</span></label>
                        <input type="text" name="system_title" class="form-control"
                               value="{{ $item->system_title ?? '' }}" required>
                    </div>

                    <div class="col-md-3 form-group">
                        <label>System Version <span class="text-danger">*</span></label>
                        <input type="text" name="system_version" class="form-control"
                               value="{{ $item->system_version ?? '' }}" required>
                    </div>

                    <div class="col-md-3 form-group">
                        <label>Exchange Rate <span class="text-danger">*</span></label>
                        <input type="number" name="exchangeRate" class="form-control"
                               value="{{ $item->exchangeRate ?? '' }}" required>
                    </div>

                    <div class="col-md-3 form-group">
                        <label>Notification Channel <span class="text-danger">*</span></label>
                        <select name="notification_channel" class="form-control" required>
                            <option value="">-- Select Channel --</option>
                            <option value="EMAIL" {{ ($item->notification_channel ?? '') === 'EMAIL' ? 'selected' : '' }}>Email</option>
                            <option value="SMS" {{ ($item->notification_channel ?? '') === 'SMS' ? 'selected' : '' }}>SMS</option>
                            <option value="BOTH" {{ ($item->notification_channel ?? '') === 'BOTH' ? 'selected' : '' }}>Email & SMS</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- ================= APPLICATION ACCESS ================= --}}
            <div class="settings-section">
                <div class="settings-title">Application Access Control</div>
                <div class="settings-desc">
                    Determines who is allowed to apply for exhibitions at this moment.
                </div>

                <div class=" row">
                    <div class="form-group col-md-8">
                    <label>Application Status</label>
                    <select name="applicationStatus" class="form-control" required>
                        <option value="CLOSED" {{ $status === 'CLOSED' ? 'selected' : '' }}>
                            CLOSED – No applications allowed
                        </option>
                        <option value="OPENED_FOR_DOMESTIC" {{ $status === 'OPENED_FOR_DOMESTIC' ? 'selected' : '' }}>
                            OPENED – Domestic exhibitors only
                        </option>
                        <option value="OPENED_FOR_FOREIGNERS" {{ $status === 'OPENED_FOR_FOREIGNERS' ? 'selected' : '' }}>
                            OPENED – Foreign exhibitors allowed
                        </option>
                        <option value="OPENED_FOR_ALL" {{ $status === 'OPENED_FOR_ALL' ? 'selected' : '' }}>
                            OPENED – All exhibitors allowed
                        </option>
                    </select>
                </div>
                {{-- Allowed  --}}
                <div class="form-group col-md-4">
                    <label>Allowed Networks</label>
                    <input type="text" name="allowedNetworks" class="form-control"
                           value="{{ $item->allowedNetworks ?? '154.118.226.150' }}" required>
                </div>
                </div>
            </div>

            {{-- ================= BOOKING DEADLINE ================= --}}
            <div class="settings-section">
                <div class="settings-title">Booking Deadline</div>
                <div class="settings-desc">
                    Final date after which new bookings will not be accepted.
                </div>

                <div class="form-group">
                    <label>Booking Deadline Date</label>
                    <input type="date"
                           name="invoice_deadline_date"
                           class="form-control"
                           value="{{ $item?->invoice_deadline_date ?? '' }}"
                           required>
                </div>
            </div>

            {{-- ================= SCANNER ADMIN PIN ================= --}}
            <div class="settings-section">
                <div class="settings-title">Scanner Admin PIN</div>
                <div class="settings-desc">
                    4-digit PIN that gates access to the base-URL settings in the eTAC Scanner app.
                    Share with supervisors only. Change it here whenever needed.
                </div>
                <div style="background:#13487e;color:#fff;border-radius:12px;padding:18px 22px;margin-top:10px;display:flex;align-items:center;gap:28px;flex-wrap:wrap;">
                    <div>
                        <div style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;opacity:.6;margin-bottom:6px;">Current PIN</div>
                        <div style="font-size:44px;font-weight:700;font-family:monospace;letter-spacing:8px;">{{ $currentAdminPin }}</div>
                    </div>
                    <div style="border-left:1px solid rgba(255,255,255,.25);padding-left:24px;display:flex;flex-direction:column;gap:8px;">
                        <div style="font-size:11px;opacity:.65;text-transform:uppercase;letter-spacing:1px;">Change PIN</div>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <input type="text" name="scanner_pin"
                                   inputmode="numeric"
                                   pattern="[0-9]{1,4}"
                                   value="{{ $item?->scanner_pin ?? '0000' }}"
                                   maxlength="4"
                                   placeholder="0000"
                                   oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,4)"
                                   style="width:100px;font-size:24px;font-weight:700;font-family:monospace;letter-spacing:6px;text-align:center;padding:6px 8px;border-radius:8px;border:none;outline:none;color:#13487e;background:#fff;">
                            <span style="font-size:11px;opacity:.6;line-height:1.6;">4 digits<br>saved with the<br>"Save System Settings" button</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= OPTIONAL FEATURES ================= --}}
            <div class="settings-section">
                <div class="settings-title">Optional Features</div>
                <div class="settings-desc">
                    Toggle optional operational capabilities.
                </div>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="self_printing"
                               {{ ($item->self_printing ?? 0) ? 'checked' : '' }}>
                        Allow Self Printing
                    </label>
                </div>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="print_notification"
                               {{ ($item->print_notification ?? 0) ? 'checked' : '' }}>
                        Enable Print Notifications
                    </label>
                </div>

                <div class="divider"></div>

                {{-- ── Badge Live Test Mode ─────────────────────────────── --}}
                <div style="background:#fffbeb; border:1px solid #fcd34d; border-radius:12px; padding:16px; margin-top:8px;">
                    <div style="display:flex; align-items:flex-start; gap:14px;">
                        <div style="padding-top:2px;">
                            <input type="checkbox"
                                   id="badge_live_test"
                                   name="badge_live_test"
                                   style="width:18px; height:18px; cursor:pointer;"
                                   {{ ($item->badge_live_test ?? false) ? 'checked' : '' }}>
                        </div>
                        <div>
                            <label for="badge_live_test"
                                   style="font-weight:800; font-size:14px; color:#92400e; cursor:pointer; margin:0;">
                                🧪 Badge Live Test Mode
                            </label>
                            <p style="font-size:12px; color:#78350f; margin:6px 0 0;">
                                When <strong>ON</strong> — bypasses all badge preconditions (control number,
                                confirmation steps, payment status). Use this when testing a new badge design
                                each year. Any member can be previewed directly via
                                <code style="background:#fef3c7; padding:1px 5px; border-radius:4px;">
                                    /badgesManagement/badge_preview/{member_id}
                                </code>
                                without completing the allocation workflow.<br><br>
                                When <strong>OFF</strong> — normal badge workflow is enforced.
                                <span style="color:#dc2626; font-weight:700;">
                                    ⚠ Turn this OFF before going live.
                                </span>
                            </p>
                            @if($item->badge_live_test ?? false)
                                <span style="display:inline-block; margin-top:8px; background:#fef08a;
                                             color:#854d0e; font-size:11px; font-weight:800;
                                             padding:3px 10px; border-radius:999px; letter-spacing:.05em;">
                                    ● LIVE TEST MODE IS CURRENTLY ACTIVE
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- SAVE --}}
            <div class="sticky-save">
                <button type="submit" class="btn btn-primary btn-lg">
                    Save System Settings
                </button>
            </div>

        </form>
    </div>
</x-app-layout>
