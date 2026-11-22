
<style>
    #logo {
        font-size: 250%;
        padding: 5%;
        text-shadow: 1px 1px 1px rgb(0, 0, 0),
                     2px 2px 1px rgb(0, 0, 0);
        text-align: center;
    }

    #name:hover {
        color: black;
    }

    /* Top Bar avatar - same appearance as Sidebar */
    #profile .dropdown-toggle img.img-circle.img-sm {
        width: 35px !important;
        height: 35px !important;
        min-width: 35px;
        max-width: 35px;
        object-fit: cover;
        object-position: center;
        border-radius: 50%;
        display: inline-block;
        vertical-align: middle;
    }
</style>

<!-- Main navbar -->
<div class="navbar navbar-default navbar-fixed-top header-highlight"
     style="background-color: rgb(16, 140, 230);">

    <div class="navbar-header"
         style="background-color: rgb(16, 140, 230);">

        <a class="navbar-brand text-bold text-white"
           href="<?php echo e(route('startApp')); ?>"
           id="h1">
            GeMEMIS
        </a>

        <ul class="nav navbar-nav visible-xs-block">

            <li>
                <a data-toggle="collapse" data-target="#navbar-mobile">
                    <i class="icon-user"></i>
                </a>
            </li>

            <li>
                <a class="sidebar-mobile-main-toggle">
                    <i class="icon-paragraph-justify3"></i>
                </a>
            </li>

        </ul>
    </div>

    <div class="navbar-collapse collapse row" id="navbar-mobile">

        <ul class="nav navbar-nav">

            <li>
                <a class="sidebar-control sidebar-main-toggle hidden-xs text-white">
                    <i class="icon-paragraph-justify3"></i>
                </a>
            </li>

            <!-- System Modules -->
            <li class="dropdown">

                <a href="#" class="dropdown-toggle" data-toggle="dropdown">

                    <i class="icon-grid6 text-white"></i>

                    <span class="visible-xs-inline-block position-right">
                        System Modules
                    </span>

                </a>

                <div class="dropdown-menu dropdown-content">

                    <div class="dropdown-content-heading">

                        System Modules

                        <ul class="icons-list">
                            <li>
                                <a href="#">
                                    <i class="icon-sync"></i>
                                </a>
                            </li>
                        </ul>

                    </div>

                    <ul class="media-list dropdown-content-body width-350">

                        <li class="media">

                            <div class="media-left">
                                <a href="#"
                                   class="btn border-primary text-primary btn-flat btn-rounded btn-icon btn-sm">
                                    <i class="icon icon-certificate"></i>
                                </a>
                            </div>

                            <div class="media-body"
                                 title="click to shift Module">

                                <a href="<?php echo e(route('dashboard')); ?>">

                                    <?php echo e(config('global.MODULE_ONE')); ?>


                                    <div class="media-annotation">
                                        <?php echo e(config('global.MODULE_ONE_DESCRIPTION')); ?>

                                    </div>

                                </a>

                            </div>

                        </li>

                    </ul>

                    <div class="dropdown-content-footer">

                        <a href="<?php echo e(route('startApp')); ?>"
                           data-popup="tooltip"
                           title="This shows the list of all Modules available in this system.">

                            <i class="icon-menu display-block"></i>

                        </a>

                    </div>

                </div>
            </li>

        </ul>

        <!-- Header profile -->
        <div>

            <ul class="nav navbar-nav navbar-right">

                <!-- Language -->
                <li class="dropdown">

                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">

                        <i class="icon-earth text-white"></i>

                        <span class="text-white">
                            <?php echo e(app()->getLocale() == 'sw' ? 'Kiswahili' : 'English'); ?>

                        </span>

                    </a>

                    <ul class="dropdown-menu dropdown-menu-right">

                        <li>
                            <a href="<?php echo e(route('lang.switch', 'en')); ?>">
                                English
                            </a>
                        </li>

                        <li>
                            <a href="<?php echo e(route('lang.switch', 'sw')); ?>">
                                Kiswahili
                            </a>
                        </li>

                    </ul>

                </li>

                <!-- User Profile -->
                <?php if(auth()->guard()->check()): ?>

                <li class="dropdown dropdown-user" id="profile">

                    <a href="#"
                       class="dropdown-toggle text-bold"
                       data-toggle="dropdown">

                        <?php if(empty(Auth::user()->profile_image)): ?>

                            <img
                                src="<?php echo e(asset('assets/images/placeholder.jpg')); ?>"
                                class="img-circle img-sm"
                                alt="Profile">

                        <?php else: ?>

                            <img
                                src="<?php echo e(asset('profile_images/' . Auth::user()->profile_image)); ?>"
                                class="img-circle img-sm"
                                alt="Profile">

                        <?php endif; ?>

                        <span class="text-black">
                            <?php echo e(Auth::user()->first_name); ?>

                            <?php echo e(Auth::user()->last_name); ?>

                        </span>

                        <i class="caret"></i>

                    </a>

                    <ul class="dropdown-menu dropdown-menu-right">

                        <li>
                            <a href="<?php echo e(route('profiles.index')); ?>">
                                <i class="icon-user-plus"></i>
                                My Profile
                            </a>
                        </li>

                        <li class="divider"></li>

                        <li>
                            <a href="<?php echo e(route('profiles.show', ['profile' => Auth::user()->user_uuid])); ?>">
                                <i class="icon-user-lock"></i>
                                Change Password
                            </a>
                        </li>

                        <li class="divider"></li>

                        <li>

                            <a href="<?php echo e(route('logout')); ?>"
                               onclick="event.preventDefault(); document.getElementById('form').submit();">

                                <i class="icon-switch2"></i>
                                Logout

                            </a>

                            <form action="<?php echo e(route('logout')); ?>"
                                  method="POST"
                                  id="form"
                                  style="display: none;">

                                <?php echo csrf_field(); ?>

                            </form>

                        </li>

                    </ul>

                </li>

                <?php endif; ?>

            </ul>

        </div>

    </div>

</div>
<!-- /main navbar -->
<?php /**PATH C:\xampp\htdocs\geita\resources\views/layouts/header.blade.php ENDPATH**/ ?>