
<?php if(auth()->guard()->check()): ?>
<!-- MAIN SIDEBAR -->
<div class="sidebar sidebar-main sidebar-fixed gememis-sidebar">
    <div class="sidebar-content">

        <!-- USER PROFILE -->
        <div class="sidebar-user gememis-user">
            <div class="category-content">
                <div class="media">

                    <a href="<?php echo e(route('profiles.index')); ?>" class="media-left gememis-avatar">
                        <?php if(empty(Auth::user()->profile_image)): ?>
                            <img src="<?php echo e(asset('assets/images/placeholder.jpg')); ?>"
                                 class="img-circle img-sm" alt="Profile">
                        <?php else: ?>
                            <img src="<?php echo e(asset('profile_images/' . Auth::user()->profile_image)); ?>"
                                 class="img-circle img-sm" alt="Profile">
                        <?php endif; ?>
                    </a>

                    <div class="media-body">
                        <span class="media-heading text-semibold">
                            <i class="icon-phone-wave gememis-icon"></i>
                            <a href="tel:<?php echo e(Auth::user()->phone_number); ?>">
                                <?php echo e(Auth::user()->phone_number); ?>

                            </a>
                        </span>

                        <div class="text-size-mini gememis-email">
                            <?php echo e(Auth::user()->email); ?>

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
                    <?php if(session('moduleNo') == false): ?>
                        <li class="<?php echo e(request()->is('startApp') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('startApp')); ?>">
                                <i class="icon-grid6"></i>
                                <span>Start System</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- SAMS DASHBOARD -->
                    <?php if(session('moduleNo') == 1): ?>
                        <li class="<?php echo e(request()->is('dashboard') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('dashboard')); ?>">
                                <i class="icon-home4"></i>
                                <span>GeMEMIS Dashboard</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- GE MEMIS BOOKING -->
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('BOOK_SPACE')): ?>
                        <li class="<?php echo e(request()->routeIs('booking.*', 'event-booking-spaces.*', 'pending_bookings') ? 'active' : ''); ?>">

                            <a href="<?php echo e(route('booking.index')); ?>">
                                <i class="icon-cart-add"></i>
                                <span class="text-bold">Booking</span>
                            </a>

                            <ul>

                                <li class="<?php echo e(request()->routeIs('booking.index') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('booking.index')); ?>">
                                        <i class="icon-calendar3"></i>
                                        <span>Manage Bookings</span>
                                    </a>
                                </li>

                                <li class="<?php echo e(request()->routeIs('event-booking-spaces.*') ? 'active' : ''); ?>">
                                    <a href="<?php echo e(route('event-booking-spaces.index')); ?>">
                                        <i class="icon-map5"></i>
                                        <span>Book Exhibition Space</span>
                                    </a>
                                </li>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('VIEW_PENDING_BOOKING')): ?>
                                    <li class="<?php echo e(request()->routeIs('pending_bookings') ? 'active' : ''); ?>">
                                        <a href="<?php echo e(route('pending_bookings')); ?>">
                                            <i class="icon-clipboard3"></i>
                                            <span>Pending Bookings</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                            </ul>
                        </li>
                    <?php endif; ?>

                    <!-- DYNAMIC MENUS -->
                    <?php
                        $level_one_menu = App\Models\setting\level_one_menus::orderBy('level_one_common_name', 'ASC')
                            ->where('level_one_menu_status', 1)
                            ->where('moduleNo', session('moduleNo'))
                            ->get();

                        $level_two_menu = App\Models\setting\level_two_menus::orderBy('level_two_common_name', 'ASC')
                            ->where('level_two_menu_status', 1)
                            ->get();
                    ?>

                    <?php $__currentLoopData = $level_one_menu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check($item->level_one_gate_name)): ?>

                            <li>
                                <a href="#">
                                    <i class="<?php echo e(filled($item->level_one_icon_name) ? $item->level_one_icon_name : 'icon-folder-open'); ?>"></i>

                                    <span class="text-bold">
                                        <?php echo e($item->level_one_common_name); ?>

                                    </span>
                                </a>

                                <ul>

                                    <?php $__currentLoopData = $level_two_menu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                        <?php if($data->upper_level_id == $item->level_one_menu_id): ?>

                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check($data->level_two_gate_name)): ?>

                                                <?php
                                                    try {
                                                        $menuUrl = route($data->level_two_menu_link);
                                                    } catch (\Throwable $th) {
                                                        $menuUrl = '#';
                                                    }
                                                ?>

                                                <li class="text-capitalize <?php echo e(Route::is($data->level_two_menu_link) ? 'active' : ''); ?>">

                                                    <a href="<?php echo e($menuUrl); ?>">
                                                        <i class="<?php echo e(filled($data->level_two_icon_name) ? $data->level_two_icon_name : 'icon-circle-small'); ?>"></i>

                                                        <span class="text-bold">
                                                            <?php echo e($data->level_two_common_name); ?>

                                                        </span>
                                                    </a>

                                                </li>

                                            <?php endif; ?>

                                        <?php endif; ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                </ul>
                            </li>

                        <?php endif; ?>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

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
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\geita\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>