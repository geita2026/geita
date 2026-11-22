<x-app-layout>
    <title>{{ env('APP_NAME') }} | Menu</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-comptwont">
            <ul class="breadcrumb">
                <li><a href="{{ route('system_menus_level_two.index') }}"><i
                            class=" icon-menu10 position-left"></i>RECEIPTS</a></li>
                <li class="active">View all customer payments</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- Content area -->
    <div class="content">

        <div class="panel panel-flat">
            <div class=" panel-body">
                <div class=" form-group col-md-12">
                    <table data-table-paginated class="table datatable-save-state table-striped table-bordered  table-responsive">
                        <thead>
                            <tr >
                                <th colspan="6" class="text-center text-bold text-primary">
                                    <h4 class=" text-bold">{{ $company->companyName }}'S PAYMENT RECEIPTS</h4>
                                </th>
                            </tr>
                            <tr>
                                <th class=" text-center text-bold">#</th>
                                <th class=" text-center text-bold">CONTROL NO</th>
                                <th class=" text-center text-bold">AMOUNT PAID</th>
                                <th class=" text-center text-bold">EVENT NAME</th>
                                <th class=" text-center text-bold">PAYMENT DATE</th>
                                <th class=" text-center text-bold">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($payments as $item)
                                <tr>
                                    <td>{{ $i }}</td>
    
                                    <td class=" text-center">
                                        <a href="{{ route('adviceNotes.show', ['adviceNote' => $item->referenceId]) }}"
                                            title="{{ $item->referenceId }}">{{ $item->control_number }}</a>
                                    </td>
    
                                    <td class=" text-center">{{ number_format($item->amount_paid, 2) }} {{ $item->currency }}
                                    </td>
                                    <td>{{ $item->event_name }}</td>
                                    <td class="text-center">{{ date('d-m-Y h:i:s', strtotime($item->payment_made_on)) }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('payments.show', ['payment' => $item->payment_id]) }}"
                                            class=" btn btn-primary">print Receipt <i class=" icon-printer"></i></a>
                                    </td>
                                </tr>
                                @php
                                    $i++;
                                @endphp
                            @endforeach
                        </tbody>
    
                    </table>
                </div>


            </div>
            <div class=" text-right form-group mb-3">
                {{ $payments->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
