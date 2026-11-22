<?php echo $__env->make('layouts.EventControl', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<style>
    .dashboard-wrapper {
        background: #f5f7fb;
        padding: 24px;
    }

    .dashboard-card {
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .06);
        padding: 20px;
        height: 100%;
        transition: all .25s ease;
    }

    .dashboard-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 26px rgba(0, 0, 0, .08);
    }

    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
    }

    .kpi-item {
        background: #ffffff;
        border-radius: 16px;
        padding: 22px 24px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all .25s ease;
    }

    .kpi-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, .08);
    }

    .kpi-content {
        display: flex;
        flex-direction: column;
    }

    .kpi-label {
        font-size: 12px;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 6px;
    }

    .kpi-value {
        font-size: 30px;
        font-weight: 700;
        color: #111827;
    }

    .kpi-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #ffffff;
    }

    /* Accent colors */
    .kpi-primary .kpi-icon {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
    }

    .kpi-success .kpi-icon {
        background: linear-gradient(135deg, #059669, #10b981);
    }

    .kpi-info .kpi-icon {
        background: linear-gradient(135deg, #0284c7, #38bdf8);
    }

    .kpi-pink .kpi-icon {
        background: linear-gradient(135deg, #db2777, #f472b6);
    }

    .kpi-brown .kpi-icon {
        background: linear-gradient(135deg, #92400e, #d97706);
    }

    .kpi-dark .kpi-icon {
        background: linear-gradient(135deg, #1f2933, #374151);
    }

    .section-title {
        font-size: 15px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 16px;
        letter-spacing: .06em;
    }

    table.dashboard-table th {
        background: #f1f5f9;
        font-size: 12px;
        text-transform: uppercase;
    }

    .trend-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 18px;
    }

    .trend-card {
        background: linear-gradient(135deg, #ffffff, #f8fafc);
        border-radius: 14px;
        padding: 22px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .06);
        transition: all .25s ease;
    }

    .trend-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 26px rgba(0, 0, 0, .08);
    }

    .trend-title {
        font-size: 12px;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 6px;
    }

    .trend-value {
        font-size: 28px;
        font-weight: 700;
        color: #111827;
    }

    .trend-meta {
        margin-top: 10px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .trend-up {
        color: #059669;
        font-weight: 600;
    }

    .trend-down {
        color: #dc2626;
        font-weight: 600;
    }

    .trend-neutral {
        color: #6b7280;
        font-weight: 600;
    }
</style>

<div class="dashboard-wrapper">

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('VIEW SAMMARY REPORT')): ?>
        <div class="kpi-grid mb-4">

            <a href="<?php echo e(route('Exhibition-Events.index')); ?>" class="text-decoration-none">
                <div class="kpi-item kpi-primary">
                    <div class="kpi-content">
                        <div class="kpi-label">Total Registered Events</div>
                        <div class="kpi-value"><?php echo e(number_format($total_events)); ?></div>
                    </div>
                    <div class="kpi-icon">
                        <i class="icon-calendar22"></i>
                    </div>
                </div>
            </a>

            <a href="<?php echo e(route('sectors.index')); ?>" class="text-decoration-none">
                <div class="kpi-item kpi-success">
                    <div class="kpi-content">
                        <div class="kpi-label">Total Sectors</div>
                        <div class="kpi-value"><?php echo e(number_format($sectorCount)); ?></div>
                    </div>
                    <div class="kpi-icon">
                        <i class="icon-grid"></i>
                    </div>
                </div>
            </a>

            <a href="<?php echo e(route('companies.index')); ?>" class="text-decoration-none">
                <div class="kpi-item kpi-info">
                    <div class="kpi-content">
                        <div class="kpi-label">Registered Companies</div>
                        <div class="kpi-value"><?php echo e(number_format($companies)); ?></div>
                    </div>
                    <div class="kpi-icon">
                        <i class="icon-office"></i>
                    </div>
                </div>
            </a>

            <a href="<?php echo e(route('products.index')); ?>" class="text-decoration-none">
                <div class="kpi-item kpi-pink">
                    <div class="kpi-content">
                        <div class="kpi-label">Exhibited Products</div>
                        <div class="kpi-value"><?php echo e(number_format($exhibited_products)); ?></div>
                    </div>
                    <div class="kpi-icon">
                        <i class="icon-box"></i>
                    </div>
                </div>
            </a>

            <a href="<?php echo e(route('products.index')); ?>" class="text-decoration-none">
                <div class="kpi-item kpi-brown">
                    <div class="kpi-content">
                        <div class="kpi-label">All Event Exhibitors</div>
                        <div class="kpi-value"><?php echo e(number_format($allExhibitors)); ?></div>
                    </div>
                    <div class="kpi-icon">
                        <i class="icon-users"></i>
                    </div>
                </div>
            </a>

            <a href="<?php echo e(route('discover_exhibitor.index')); ?>" class="text-decoration-none">
                <div class="kpi-item kpi-dark">
                    <div class="kpi-content">
                        <div class="kpi-label">Current Event Exhibitors</div>
                        <div class="kpi-value"><?php echo e(number_format($currentExhibitors)); ?></div>
                    </div>
                    <div class="kpi-icon">
                        <i class="icon-user-check"></i>
                    </div>
                </div>
            </a>

        </div>
    <?php endif; ?>


    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('View Participation Reports')): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="dashboard-card">
                    <div class="section-title text-center">
                        EXHIBITOR PARTICIPATION OVERVIEW (<?php echo e($total_events); ?> EVENTS)
                    </div>

                    <div class="trend-grid">

                        <a href="<?php echo e(route('exhibitors.index')); ?>" class="text-decoration-none">
                            <div class="trend-card">
                                <div class="trend-title">Total Exhibitors (All Events)</div>
                                <div class="trend-value">
                                    <?php echo e(number_format($allExhibitors)); ?>

                                </div>
                                <div class="trend-meta trend-neutral">
                                    <i class="icon-stats-bars"></i>
                                    cumulative participation
                                </div>
                            </div>
                        </a>

                        <div class="trend-card">
                            <div class="trend-title">Current Event Exhibitors</div>
                            <div class="trend-value">
                                <?php echo e(number_format($currentExhibitors)); ?>

                            </div>
                            <div class="trend-meta trend-up">
                                <i class="icon-arrow-up16"></i>
                                active exhibitors
                            </div>
                        </div>

                        <div class="trend-card">
                            <div class="trend-title">Average Per Event</div>
                            <div class="trend-value">
                                <?php echo e($total_events > 0 ? number_format($allExhibitors / $total_events) : 0); ?>

                            </div>
                            <div class="trend-meta trend-neutral">
                                <i class="icon-calculator"></i>
                                exhibitors per event
                            </div>
                        </div>

                        <div class="trend-card">
                            <div class="trend-title">Event Coverage</div>
                            <div class="trend-value">
                                <?php echo e(number_format($total_events)); ?>

                            </div>
                            <div class="trend-meta trend-neutral">
                                <i class="icon-calendar"></i>
                                total registered events
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>


    
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('View Collection Reports')): ?>
        <div class="row">
            <div class="col-12">
                <a href="<?php echo e(route('payments.index')); ?>" class="text-decoration-none text-dark">
                    <div class="dashboard-card">
                        <div class="section-title text-center">
                            INVOICE GENERATION & COLLECTION SUMMARY (CURRENT EVENT)
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered dashboard-table text-center">
                                <thead>
                                    <tr>
                                        <th>Total Invoices</th>
                                        <th>Expected Amount</th>
                                        <th>Amount Paid</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="fw-bold">
                                        <td><?php echo e(number_format($total_invoices)); ?></td>

                                        <td>
                                            <?php echo e(number_format($expected_tzs, 2)); ?> TZS <br>
                                            <?php echo e(number_format($expected_usd, 2)); ?> USD
                                        </td>

                                        <td class="text-success">
                                            <?php echo e(number_format($amount_paid_tzs, 2)); ?> TZS <br>
                                            <?php echo e(number_format($amount_paid_usd, 2)); ?> USD
                                        </td>

                                        <td
                                            class="<?php echo e($expected_tzs - $amount_paid_tzs > 0 ? 'text-warning' : 'text-success'); ?>">
                                            <?php echo e(number_format($expected_tzs - $amount_paid_tzs, 2)); ?> TZS <br>
                                            <?php echo e(number_format($expected_usd - $amount_paid_usd, 2)); ?> USD
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </a>
            </div>
        </div>
    <?php endif; ?>

</div>
<?php /**PATH C:\xampp\htdocs\geita\resources\views/Dashboards/InternalUser.blade.php ENDPATH**/ ?>