	@auth
    <!-- Main sidebar -->
    <div class="sidebar sidebar-main sidebar-fixed " style="background-color: rgb(0, 143, 245);">
        <div class="sidebar-content">
            <!-- User menu -->
            <div class="sidebar-user">
                <div class="category-content">
                    <div class="media">
                        <a href="{{ route('profiles.index') }}" class="media-left">
                            @if (empty(Auth::user()->profile_image))
                            <img src="{{ asset('assets/images/placeholder.jpg') }}" class="img-circle img-sm" alt="">
                            @else
                            <img src="{{ asset('profile_images/'.Auth::user()->profile_image) }}" class=" img-circle">
                            @endif
                            </a>
                        <div class="media-body">
                            <span class="media-heading text-semibold"><i class=" icon-phone-wave"></i> <a href="tel:{{ Auth::user()->phone_number }}" class=" text-white">{{ Auth::user()->phone_number }}</a></span>
                            <div class="text-size-mini text-muted">
                                &nbsp;{{ Auth::user()->email }}
                            </div>
                        </div>
                        <div class="media-right media-middle">
                            <ul class="icons-list">
                                
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /user menu -->
            <!-- Main navigation -->
            <div class="sidebar-category sidebar-category-visible">
                <div class="category-content no-padding ">
                    <ul class="navigation navigation-main navigation-accordion mb-20">
                        <!-- Main -->
                        <li class="navigation-header"><span>Main</span> <i class="icon-menu" title="Main pages" ></i>
                        </li>
                        <div class=" p-10">
                            <input type="text" class=" form-control bg-primary-400 text-muted" placeholder=" search menu">
                        </div>
                        
                        @if (session('moduleNo') ==false)
                        <li class="@if(request()->is('startApp')) active  @endif " ><a href="{{ route('startApp') }}" style=" border:1px solid white"><i class=" icon-grid6" style="font-size:23px" ></i> <span>Start System</span></a></li>
                        @endif
                        
                        @if (session('moduleNo')==1)
                        <li class="@if(request()->is('dashboard')) active  @endif " ><a href="{{ route('dashboard') }}" style=" border:1px solid white"><i class=" icon-home4" style="font-size:23px" ></i> <span>GeMEMIS Dashboard</span></a></li>
                        @endif


                        
                        @php
                        $level_one_menu=App\Models\setting\level_one_menus::orderBy('level_one_common_name','ASC')->where('level_one_menu_status',1)->where('moduleNo',session('moduleNo'))->get();
                        $level_two_menu=App\Models\setting\level_two_menus::orderBy('level_two_common_name','ASC')->where('level_two_menu_status',1)->get();
                        @endphp

                        @foreach ($level_one_menu as $item)
                        @can($item->level_one_gate_name)
                        <li>
                            <a href="#" style=" border:1px solid white;"><i style="font-size:25px" class=" {{ $item->level_one_icon_name }}"></i> <span class="text-bold">{{ $item->level_one_common_name }}</span></a>
                            <ul>
                                @foreach ($level_two_menu as $data)
                                @if ($data->upper_level_id==$item->level_one_menu_id)

                                @can( $data->level_two_gate_name )
                                    <li class="text-capitalize {{Route::is($data->level_two_menu_link)  ? 'active' : '' }}" ><a href="@php
                                        try {
                                            @endphp {{ route($data->level_two_menu_link) }} @php
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    echo "#";
                                }
                                @endphp"> <i style="font-size:20px" class="{{ $data->level_two_icon_name }}"></i> <span class="text-bold"> {{ $data->level_two_common_name }}</a></li>
                                @endcan

                                @endif
                                @endforeach
                            </ul>
                        </li>
                        @endcan
                        @endforeach

                        <li class="navigation-header "><span class=" text-muted text-small"></span> <i class="icon-menu" title="Main pages" ></i>
                    </ul>
                </div>
            </div>
            <!-- /main navigation -->
        </div>
    </div>
    <!-- /main sidebar -->
    @endauth
