
<x-app-layout>
    <title>{{ env('APP_NAME') }} | My Profile</title>

    <style>
        .card {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .06);
            margin-bottom: 24px;
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* Profile avatar - same size and appearance as Sidebar and Top Bar */
        .gememis-profile-avatar {
            width: 35px !important;
            height: 35px !important;
            min-width: 35px;
            max-width: 35px;
            border-radius: 50%;
            object-fit: cover;
            object-position: center;
            display: block;
        }

        .profile-name {
            font-size: 22px;
            font-weight: 700;
            color: #1f2937;
        }

        .profile-meta {
            font-size: 13px;
            color: #6b7280;
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #374151;
            margin-bottom: 16px;
        }

        .progress {
            height: 10px;
            border-radius: 999px;
        }

        .progress-bar {
            background: linear-gradient(90deg, #2563eb, #38bdf8);
        }

        table th {
            background: #f8fafc;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .badge-action {
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-create {
            background: #dcfce7;
            color: #166534;
        }

        .badge-update {
            background: #e0f2fe;
            color: #075985;
        }

        .muted {
            font-size: 12px;
            color: #6b7280;
        }

        .ip-mask {
            font-family: monospace;
        }
    </style>

    {{-- PAGE HEADER --}}
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>

        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li>
                    <a href="{{ route('startApp') }}">
                        <i class="icon-home2 position-left"></i>
                        Home
                    </a>
                </li>
                <li class="active">My Profile</li>
            </ul>

            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="content">

        {{-- PROFILE OVERVIEW --}}
        <div class="card">

            <div class="profile-header">

                <img
                    src="{{ empty(Auth::user()->profile_image)
                        ? asset('assets/images/placeholder.jpg')
                        : asset('profile_images/' . Auth::user()->profile_image) }}"
                    class="gememis-profile-avatar"
                    alt="Profile"
                >

                <div>
                    <div class="profile-name">
                        {{ Auth::user()->first_name }}
                        {{ Auth::user()->last_name }}
                    </div>

                    <div class="profile-meta">
                        {{ $role ?? 'User' }} &bull;
                        {{ Auth::user()->email }}
                    </div>

                    <div class="profile-meta">
                        Account created on
                        {{ Auth::user()->user_created_at
                            ? date('M d, Y', strtotime(Auth::user()->user_created_at))
                            : 'Not available' }}
                    </div>
                </div>

            </div>

            {{-- PROFILE COMPLETION --}}
            @php
                $fields = [
                    Auth::user()->first_name,
                    Auth::user()->last_name,
                    Auth::user()->email,
                    Auth::user()->phone_number,
                    Auth::user()->profile_image,
                    Auth::user()->user_signature
                ];

                $completed = collect($fields)->filter()->count();
                $percent = count($fields) > 0
                    ? intval(($completed / count($fields)) * 100)
                    : 0;
            @endphp

            <div class="mt-20">
                <strong>Profile Completion</strong>

                <div class="progress mt-5">
                    <div
                        class="progress-bar"
                        role="progressbar"
                        style="width: {{ $percent }}%;"
                        aria-valuenow="{{ $percent }}"
                        aria-valuemin="0"
                        aria-valuemax="100">
                    </div>
                </div>

                <small class="muted">
                    {{ $percent }}% completed
                </small>
            </div>

        </div>

        {{-- UPDATE PROFILE --}}
        <div class="card">

            <div class="section-title">
                Update Profile Information
            </div>

            <form
                action="{{ route('profiles.update', ['profile' => Auth::user()->user_uuid]) }}"
                method="POST"
                enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-4 form-group">
                        <label>First Name</label>
                        <input
                            type="text"
                            name="first_name"
                            class="form-control"
                            value="{{ old('first_name', Auth::user()->first_name) }}">
                    </div>

                    <div class="col-md-4 form-group">
                        <label>Middle Name</label>
                        <input
                            type="text"
                            name="middle_name"
                            class="form-control"
                            value="{{ old('middle_name', Auth::user()->middle_name) }}">
                    </div>

                    <div class="col-md-4 form-group">
                        <label>Last Name</label>
                        <input
                            type="text"
                            name="last_name"
                            class="form-control"
                            value="{{ old('last_name', Auth::user()->last_name) }}">
                    </div>

                    <div class="col-md-6 form-group">
                        <label>Phone Number</label>
                        <input
                            type="text"
                            name="phone_number"
                            class="form-control"
                            value="{{ old('phone_number', Auth::user()->phone_number) }}">
                    </div>

                    <div class="col-md-6 form-group">
                        <label>Profile Image</label>
                        <input
                            type="file"
                            name="profile_image"
                            class="form-control"
                            accept="image/*">
                    </div>

                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary">
                        Update Profile
                    </button>
                </div>

            </form>
        </div>

        {{-- CHANGE PASSWORD --}}
        <div class="card">

            <div class="section-title">
                Change Password
            </div>

            <form action="{{ route('password.update') }}" method="POST">

                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-4 form-group">
                        <label>Current Password</label>
                        <input
                            type="password"
                            name="current_password"
                            class="form-control"
                            required>
                    </div>

                    <div class="col-md-4 form-group">
                        <label>New Password</label>
                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            required>
                    </div>

                    <div class="col-md-4 form-group">
                        <label>Confirm Password</label>
                        <input
                            type="password"
                            name="password_confirmation"
                            class="form-control"
                            required>
                    </div>

                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-warning">
                        Update Password
                    </button>
                </div>

            </form>
        </div>

        {{-- SECURITY & ACTIVITY LOG --}}
        <div class="card">

            <div class="section-title">
                Security & Activity Log
            </div>

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Action</th>
                            <th>Module</th>
                            <th>Reference</th>
                            <th>Location</th>
                            <th>IP</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($activities as $activity)

                            @php
                                $moduleMap = [
                                    'App\\Models\\advice_note\\service_charges' => 'Service Booking',
                                    'App\\Models\\advice_note\\advice_notes' => 'Advice Note',
                                    'App\\Models\\advice_note\\particulars' => 'Advice Note Item',
                                    'App\\Models\\Companies\\CompanyAdmin' => 'Company Administration',
                                    'App\\Models\\companies' => 'Company Profile',
                                    'App\\Models\\exhibition\\exhibitors' => 'Exhibitor Details',
                                ];

                                $module = $moduleMap[$activity->auditable_type]
                                    ?? 'System Activity';

                                $badge = $activity->event === 'created'
                                    ? 'badge-create'
                                    : 'badge-update';

                                $ip = preg_replace(
                                    '/\d+$/',
                                    'xxx',
                                    $activity->ip_address ?? ''
                                );

                                $path = parse_url(
                                    $activity->url ?? '',
                                    PHP_URL_PATH
                                );
                            @endphp

                            <tr>

                                <td>
                                    {{ date('M d, Y H:i', strtotime($activity->created_at)) }}
                                </td>

                                <td>
                                    <span class="badge-action {{ $badge }}">
                                        {{ ucfirst($activity->event) }}
                                    </span>
                                </td>

                                <td>{{ $module }}</td>

                                <td class="muted">
                                    {{ Str::limit($activity->auditable_id, 12, '...') }}
                                </td>

                                <td class="muted">
                                    {{ $path }}
                                </td>

                                <td class="ip-mask">
                                    {{ $ip }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="text-center muted">
                                    No activity records found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>
        </div>

    </div>
</x-app-layout>
