@php
    $services = [
        ['MANAGE_PAYMENT', 'payments.index', 'icon-cash3', $isStaff ? 'Payments and receipts' : 'My payments and receipts'],
    ];
    if (!$isStaff) {
        $services = array_merge($services, [
            ['BOOK_SERVICES', 'book-services.index', 'icon-cart-add', 'Book extra services'],
            ['MANAGE_BADGES_MENU', 'exhibitors_list.index', 'icon-profile', 'Exhibition badges'],
            ['CONFIRM_JUDGING', 'exhibitor_judging.index', 'icon-trophy2', 'Judging participation'],
        ]);
    }
    $services = array_filter($services, fn ($service) => Auth::user()->can($service[0]));
@endphp

@if ($services)
    <nav class="dashboard-services" aria-labelledby="dashboard-services-title">
        <h2 id="dashboard-services-title">More actions</h2>
        <ul>
            @foreach ($services as [, $route, $icon, $label])
                <li>
                    <a href="{{ route($route) }}">
                        <i class="{{ $icon }}" aria-hidden="true"></i> {{ $label }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
@endif
