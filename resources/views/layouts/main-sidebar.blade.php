<div class="sidebar sidebar-main sidebar-fixed bg-success-300">
    <div class="sidebar-content">

        <!-- User menu -->
        {{-- <div class="sidebar-user">
            <div class="category-content">
                <div class="media">
                    <a href="{{ route('dashboard') }}" class="media-left"><img src="{{ asset('assets/images/placeholder.jpg') }}" class="img-circle img-sm" alt=""></a>
                    <div class="media-body">
                        <span class="media-heading text-semibold">Victoria Baker</span>
                        <div class="text-size-mini text-muted">
                            <i class="icon-pin text-size-small"></i> &nbsp;Santa Ana, CA
                        </div>
                    </div>

                    <div class="media-right media-middle">
                        <ul class="icons-list">
                            <li>
                                <a href="#"><i class="icon-cog3"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div> --}}
        <!-- /user menu -->


        <!-- Main navigation -->
        <div class="sidebar-category sidebar-category-visible">
            <div class="category-content no-padding">
                <ul class="navigation navigation-main navigation-accordion">

                    <!-- Main -->
                    <li class="navigation-header"><span>Main-Sidebar</span> <i class="icon-menu" title="Main pages"></i></li>
                    <li><a href="{{ route('dashboard') }}"><i class="icon-home4"></i> <span>Dashboard</span></a></li>
                    {{-- @can('VIEW_ANY_PERMISSION') --}}
                    @can('VIEW_ANY_PERMISSION')
                    <li><a href="{{ route('permissions.index') }}"><i class=" icon-lock2"></i> <span>Permissions</span></a></li>
                    @endcan
                    {{-- @endcan --}}
                    @can('VIEW_ANY_ROLE')
                    <li><a href="{{ route('roles.index') }}"><i class=" icon-accessibility"></i> <span>Roles</span></a></li>
                    @endcan
                    @can('VIEW_ANY_USER')
                    <li><a href="{{ route('users.index') }}"><i class=" icon-users4"></i> <span>Users</span></a></li>
                    @endcan
                    @can('VIEW_ANY_AUDIT')
                    <li><a href="{{ route('audits.index') }}"><i class=" icon-repo-forked"></i> <span>User-Audit</span></a></li>
                    @endcan
                    @can('VIEW_ANY_COMPANY')
                    <li><a href="{{ route('companies.index') }}"><i class=" icon-office"></i> <span>Companies</span></a></li>
                    @endcan



                </ul>
            </div>
        </div>
        <!-- /main navigation -->

    </div>
</div>
