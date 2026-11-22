
@auth
<!-- MAIN SIDEBAR -->
<div class="sidebar sidebar-main sidebar-fixed gememis-sidebar">
    <div class="sidebar-content">

        <!-- USER PROFILE -->
        <div class="sidebar-user gememis-user">
            <div class="category-content">
                <div class="media">

                    <a href="{{ route('profiles.index') }}" class="media-left gememis-avatar">
                        @if (empty(Auth::user()->profile_image))
                            <img src="{{ asset('assets/images/placeholder.jpg') }}"
                                 class="img-circle img-sm" alt="Profile">
                        @else
                            <img src="{{ asset('profile_images/' . Auth::user()->profile_image) }}"
                                 class="img-circle img-sm" alt="Profile">
                        @endif
                    </a>

                    <div class="media-body">
                        <span class="media-heading text-semibold">
                            <i class="icon-phone-wave gememis-icon"></i>
                            <a href="tel:{{ Auth::user()->phone_number }}">
                                {{ Auth::user()->phone_number }}
                            </a>
                        </span>

                        <div class="text-size-mini gememis-email">
                            {{ Auth::user()->email }}
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- MAIN NAVIGATION -->
        <div class="sidebar-category sidebar-category-visible">
            <div class="category-content no-padding">

                <ul class="navigation navigation-main navigation-accordion mb-20">

                    <li class="navigation-header">
                        <span>MAIN MENU</span>
                        <i class="icon-menu"></i>
                    </li>

                    <!-- SEARCH -->
                    <li class="gememis-search">
                        <input type="text"
                               class="form-control"
                               placeholder="Search menu">
                    </li>

                    <!-- START SYSTEM -->
                    @if (session('moduleNo') == false)
                        <li class="{{ request()->is('startApp') ? 'active' : '' }}">
                            <a href="{{ route('startApp') }}">
                                <i class="icon-grid6"></i>
                                <span>Start System</span>
                            </a>
                        </li>
                    @endif

                    <!-- SAMS DASHBOARD -->
                    @if (session('moduleNo') == 1)
                        <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
                            <a href="{{ route('dashboard') }}">
                                <i class="icon-home4"></i>
                                <span>GeMEMIS Dashboard</span>
                            </a>
                        </li>
                    @endif

                    <!-- GE MEMIS BOOKING -->
                    @can('BOOK_SPACE')
                        <li class="{{ request()->routeIs('booking.*', 'event-booking-spaces.*', 'pending_bookings') ? 'active' : '' }}">

                            <a href="{{ route('booking.index') }}">
                                <i class="icon-cart-add"></i>
                                <span class="text-bold">Booking</span>
                            </a>

                            <ul>

                                <li class="{{ request()->routeIs('booking.index') ? 'active' : '' }}">
                                    <a href="{{ route('booking.index') }}">
                                        <i class="icon-calendar3"></i>
                                        <span>Manage Bookings</span>
                                    </a>
                                </li>

                                <li class="{{ request()->routeIs('event-booking-spaces.*') ? 'active' : '' }}">
                                    <a href="{{ route('event-booking-spaces.index') }}">
                                        <i class="icon-map5"></i>
                                        <span>Book Exhibition Space</span>
                                    </a>
                                </li>

                                @can('VIEW_PENDING_BOOKING')
                                    <li class="{{ request()->routeIs('pending_bookings') ? 'active' : '' }}">
                                        <a href="{{ route('pending_bookings') }}">
                                            <i class="icon-clipboard3"></i>
                                            <span>Pending Bookings</span>
                                        </a>
                                    </li>
                                @endcan

                            </ul>
                        </li>
                    @endcan

                    <!-- DYNAMIC MENUS -->
                    @php
                        $level_one_menu = App\Models\setting\level_one_menus::orderBy('level_one_common_name', 'ASC')
                            ->where('level_one_menu_status', 1)
                            ->where('moduleNo', session('moduleNo'))
                            ->get();

                        $level_two_menu = App\Models\setting\level_two_menus::orderBy('level_two_common_name', 'ASC')
                            ->where('level_two_menu_status', 1)
                            ->get();
                    @endphp

                    @foreach ($level_one_menu as $item)

                        @can($item->level_one_gate_name)

                            <li>
                                <a href="#">
                                    <i class="{{ filled($item->level_one_icon_name) ? $item->level_one_icon_name : 'icon-folder-open' }}"></i>

                                    <span class="text-bold">
                                        {{ $item->level_one_common_name }}
                                    </span>
                                </a>

                                <ul>

                                    @foreach ($level_two_menu as $data)

                                        @if ($data->upper_level_id == $item->level_one_menu_id)

                                            @can($data->level_two_gate_name)

                                                @php
                                                    try {
                                                        $menuUrl = route($data->level_two_menu_link);
                                                    } catch (\Throwable $th) {
                                                        $menuUrl = '#';
                                                    }
                                                @endphp

                                                <li class="text-capitalize {{ Route::is($data->level_two_menu_link) ? 'active' : '' }}">

                                                    <a href="{{ $menuUrl }}">
                                                        <i class="{{ filled($data->level_two_icon_name) ? $data->level_two_icon_name : 'icon-circle-small' }}"></i>

                                                        <span class="text-bold">
                                                            {{ $data->level_two_common_name }}
                                                        </span>
                                                    </a>

                                                </li>

                                            @endcan

                                        @endif

                                    @endforeach

                                </ul>
                            </li>

                        @endcan

                    @endforeach

                    <!-- FOOTER -->
                    <li class="navigation-header">
                        <span></span>
                        <i class="icon-menu"></i>
                    </li>

                </ul>
            </div>
        </div>

    </div>
</div>
@endauth
