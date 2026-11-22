
 <style>
    .zoom{
        padding: 2%;
        transition: transform .5s;
        font-size: 20px;
    }
    .zoom:hover {
      -ms-transform: scale(1.1); /* IE 9 */
      -webkit-transform: scale(1.1); /* Safari 3-8 */
      transform: scale(1.1);
    }
    </style>

<?php if (isset($component)) { $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\AppLayout::class, []); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
    <title><?php echo e(env('APP_NAME')); ?> | Home</title>
   
   <div class="page-header">
       <div class="page-header-content">
           <?php echo $__env->make('layouts.heading-elements', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
       </div>
   <div class="breadcrumb-line breadcrumb-line-component">
       <ul class="breadcrumb">
           <li><a href="<?php echo e(route('dashboard')); ?>"><i class="icon-home2 position-left"></i> Home</a></li>
           <li class="active">Dashboard</li>
       </ul>

       <ul class="breadcrumb-elements">
        <?php echo $__env->make('layouts.help', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
       </ul>
   </div>
   </div>
<!-- /page header -->

<!-- Content area -->
<div class="content">

    
    <?php if(Auth::user()->account_type==1): ?>
    <?php echo $__env->make('Dashboards.InternalUser', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
    
    
    <?php elseif(Auth::user()->account_type==2): ?>
    <?php echo $__env->make('Dashboards.ExternalUser', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    

    <?php endif; ?>
</div>
</div>


<!-- /primary modal -->

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da)): ?>
<?php $component = $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da; ?>
<?php unset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da); ?>
<?php endif; ?>


<script>
$(function () {

    // Generate chart
    var bar_chart = c3.generate({
        bindto: '#exhibitor_chart2',
        size: { height: 400,
        width:950, },
        data: {
            columns: [

                <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $total_Exhibitor=0; ?>
                  <?php $__currentLoopData = $exhibitors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exhibitor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($exhibitor->exhibitor_event==$event->event_id): ?>
                        <?php $total_Exhibitor++; ?>
                    <?php endif; ?>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                ['<?php echo e($event->event_name); ?>', <?php echo e($total_Exhibitor); ?>],
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ],

            type: 'bar'
        },
        color: {
            pattern: ['#2196F3', '#FF9800', '#4CAF50']
        },
        bar: {
            width: {
                ratio: 0.35
            }
        },
        grid: {
            y: {
                show: true,
            }
        },


    });

    // Resize chart on sidebar width change
    $(".sidebar-control").on('click', function() {
        pie_chart.resize();
        donut_chart.resize();
        bar_chart.resize();
        bar_stacked_chart.resize();
        combined_chart.resize();
        scatter_chart.resize();
    });
});

</script>

<script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/visualization/d3/d3.min.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('assets/js/plugins/visualization/c3/c3.min.js')); ?>"></script>





<?php /**PATH C:\xampp\htdocs\geita\resources\views/dashboard.blade.php ENDPATH**/ ?>