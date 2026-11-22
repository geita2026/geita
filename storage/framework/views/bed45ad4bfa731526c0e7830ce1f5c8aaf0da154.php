
<?php
$booths=App\Models\booking\booked_booths::where('userID',Auth::user()->user_uuid)->where('advice_note_id',Null)->count();
$space=App\Models\booking\booked_pavilions::where('userID',Auth::user()->user_uuid)->where('advice_note_id',Null)->count();
$services=App\Models\advice_note\service_charges::where('user_id',Auth::user()->user_uuid)->where('advice_note_id',Null)->count();
$cat=$booths+$space+$services;
?>


<div class="page-header">
    <div class="page-title">
        
    </div>
    <div class="heading-elements">
        <div class="heading-btn-group">
           <?php if(session('moduleNo')==1): ?>
           <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Book Space')): ?>
           <a href="<?php echo e(route('booking.index')); ?>" class="btn btn-link btn-float has-text"><i class=" icon-cart-add text-primary"></i><span>Book Space</span></a>
           <a href="<?php echo e(route('booking.edit',['booking'])); ?>" class="btn btn-link btn-float has-text"><i class=" icon-cart5 text-primary"></i> <span class=" text-warning text-bold"><?php echo e($cat); ?></span></a>
              
           <?php endif; ?>
           <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\geita\resources\views/layouts/heading-elements.blade.php ENDPATH**/ ?>