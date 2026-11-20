<x-app-layout>
    <title>{{ env('APP_NAME') }} | AGENTS</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> Agents</a></li>
                <li class="active">View All Agents</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->
    <!-- Content area -->
    <div class="content">
        <div class="panel panel-white">
            <div class=" panel-body table-responsive">

                <span class=" text-danger">
                    @error('agentName')
                        {{ $message }}
                    @enderror
                </span>
                <div class=" col-md-12 table-header form-group">
                    <h4 class=" text-center">ONLINE TICKET, AGENT COLLECTION RECONCILIATION POINT.</h4>
                </div>

                <a>
                    <div class="col-md-4 form-group zoom">
                        <button type="submit"
                            class="btn btn-block btn-float btn-float-lg text-uppercase text-bold border-primary"
                            style="border:5px solid rgb(69, 150, 182);">
                            <i>{{ number_format($vCollections) }}</i>
                            <span>Total M-PESA Collections</span>
                        </button>
                    </div>
                </a>
                <a>
                    <div class="col-md-4 form-group zoom">
                        <button type="submit"
                            class="btn btn-block btn-float btn-float-lg text-uppercase text-bold border-primary"
                            style="border:5px solid rgb(69, 150, 182);">
                            <i>{{ number_format($tCollections) }}</i>
                            <span>Total TANTRADE Collections</span>
                        </button>
                    </div>
                </a>

                <a>
                    <div class="col-md-4 form-group zoom">
                        <button type="submit"
                            class="btn btn-block btn-float btn-float-lg text-uppercase text-bold border-primary"
                            style="border:5px solid rgb(69, 150, 182);">
                            <i>{{ number_format($tCollections+$vCollections) }}</i>
                            <span>TOTAL ONLINE TICKET COLLECTIONS</span>
                        </button>
                    </div>
                </a>

                <div class=" col-md-12 form-group">
                    <table class="table datatable-button-html5-columns table-responsive table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class=" text-bold">#</th>
                                <th class=" text-bold">COLLECTION DATE</th>
                                <th class=" text-bold">AGENT NAME</th>
                                <th class=" text-bold">AGENT COLLECTED AMOUNT</th>
                                <th class=" text-bold">SYSTEM AMOUNT</th>
                                <th class=" text-bold">VARIATION</th>
                                <th class=" text-bold">Control Number</th>
                                <th class=" text-bold">PAYMENT STATUS</th>
                                <th class=" text-bold">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($invoices as $item)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ date('d-m-y',strtotime($item->paymentDate)) }}</td>
                                    <td>{{ $item->companyName }}</td>
                                    <td class=" text-right">{{ number_format($item->amountCollected,2) }} TZS</td>
                                    <td class=" text-right">{{ number_format($item->amountExpected,2) }} TZS</td>
                                    <td class=" text-right">{{ number_format($item->amountCollected-$item->amountExpected,2) }} TZS</td>
                                    <td>{{ $item->controlNumber }}</td>
                                    <td class=" text-center">
                                        @if ($item->amountCollected != $item->amountExpected)
                                            <label class=" bg-danger p-5 text-bold">NOT SETTLED</label>
                                        @elseif ($item->amountExpected == $item->amountCollected)
                                            <label class=" bg-success p-5 text-bold">SETTLED</label>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <ul class="icons-list">
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                    <i class="icon-menu4"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    @if ($item->controlNumber==Null)
                                                    <li>
                                                        <a href="{{ route('reconciliations.show',['reconciliation'=>$item->collectionId]) }}"
                                                            class="text-primary">Request Control Number<i
                                                                class=" icon-database-edit2"></i></a>
                                                    </li>
                                                    @endif
                                                    <li>
                                                        <a data-toggle="modal" data-target="#deleteCollection{{ $item->collectionId }}"
                                                            class="text-primary">Remove Reconciliation<i
                                                                class=" icon-insert-template"></i></a>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </td>
    
                                    {{-- edit role modal --}}
                                    <div id="deleteCollection{{ $item->collectionId }}" class="modal fade" aria-modal="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">DELETE RECONCILITION FOR {{ $item->paymentDate }}.</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('reconciliations.destroy', ['reconciliation' => $item->collectionId]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <div class="">
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-link"
                                                                    data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Delete
                                                                    Reconciliation </button>
                                                            </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /edit role modal -->
    
                                    {{-- delete modal --}}
                                    <div id="deleteAgent{{ $item->collectionId }}" class="modal fade" aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">Are you sure you whant to delete this Agent ?
                                                    </h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('agents.destroy', ['agent' => $item->collectionId]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <div class=" text-right">
                                                            <button type="button" data-dismiss="modal"
                                                                class=" btn btn-secondary">Close</button>
                                                            <button type="submit" class="btn btn-danger ml-20">Yes,
                                                                Delete</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- DELETE --}}
    
                                </tr>
                                @php
                                    $i++;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class=" col-md-12 text-right">
                    {{-- {{ $invoices->links() }} --}}
                </div>
            </div>
        </div>
    </div>
    <!-- /dashboard content -->
   
</x-app-layout>
