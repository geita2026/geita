<x-app-layout>
    <title>{{ env('APP_NAME') }} | TRANSACTIONS</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> TRANSACTIONS</a></li>
                <li class="active">View All Individual Transactions</li>
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

                <span class=" text-danger">@error('posName') {{ $message }} @enderror</span>
                <div class=" col-md-12 table-header form-group">
                    <h4 class=" text-center text-bold text-uppercase">
                        @foreach ($agents as $data)
                            <b class=" text-warning">{{ $data->agentName }}'S</b> CASH TRANSACTIONS FOR {{ $data->event_name }}.
                        @endforeach
                        
                    </h4>
                </div>

                <div class="category-content">
                    <div class="row row-condensed">

                        <a>
                            <div class="col-md-4 form-group zoom">
                                <button type="submit"
                                    class="btn btn-block btn-float btn-float-lg text-uppercase text-bold border-primary"
                                    style="border:5px solid rgb(69, 150, 182);">
                                    <i>{{ number_format($exactlyCollected ?? 0) }}</i>
                                    <span>Total Exactly Collected</span>
                                </button>
                            </div>
                        </a>

                        <a>
                            <div class="col-md-4 form-group zoom">
                                <button type="submit"
                                    class="btn btn-block btn-float btn-float-lg text-uppercase text-bold border-primary"
                                    style="border:5px solid rgb(69, 150, 182);">
                                    <i>{{ number_format($totalDemaged ?? 0) }} TZS</i>
                                    <span>Total Demaged Ticket</span>
                                </button>
                            </div>
                        </a>

                        <a>
                            <div class="col-md-4 form-group zoom">
                                <button type="submit"
                                    class="btn btn-block btn-float btn-float-lg text-uppercase text-bold border-primary"
                                    style="border:5px solid rgb(69, 150, 182);">
                                    <i>{{ number_format($totalCollections ?? 0) }} TZS</i>
                                    <span>Total Expected Collections</span>
                                </button>
                            </div>
                        </a> 
                    </div>
                </div>
                
                <form action="{{ route('agents.show',['agent'=>session('agentId')]) }}">
                    <div class=" col-md-12 form-group">
                        <div class=" form-group col-md-10">
                            <label>Collection Date<i class=" text-danger">*</i></label>
                            <div class="form-group ">
                                <input type="date" name="transactionDate" class=" form-control">
                            </div>
                        </div> 
                        <div class=" form-group col-md-2">
                            <br>
                            <button type="submit" class=" btn btn-primary">Fetch Transactions</button>
                        </div>
                    </div>
                </form>

                <div class=" form-group col-md-12">
                    <table class="table datatable-button-html5-columns table-responsive table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class=" text-bold">#</th>
                                <th class=" text-bold">TRANSACTION DATE</th>
                                <th class=" text-bold">TICKET NUMBER</th>
                                <th class=" text-bold">TICKET NAME</th>
                                <th class=" text-bold">AGENT NAME</th>
                                <th class=" text-bold">TICKET STATUS</th>
                                <th class=" text-bold">SCAN STATUS</th>
                                <th class=" text-bold">TICKET AMOUNT (TZS)</th>
                                <th class=" text-bold">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $i=1;
                            @endphp
                            @foreach ($collections as  $item)
                            <tr>
                                <td>{{ $i}}</td>
                                <td class=" text-uppercase">{{ date('d-m-Y',strtotime($item->ticketDate)) }}</td>
                                <td>{{ $item->ticketNo }}</td>
                                <td>{{ $item->package_discriptions }}</td>
                                <td>{{ $item->agentName }}</td>
                                <td class=" text-center">
                                    @if ($item->ticketStatus==0)
                                        <label class=" bg-danger p-5">DEMAGED TICKET</label>
                                    @elseif ($item->ticketStatus==1)
                                    <label class=" bg-success p-5">VALID TICKET</label>
                                    @endif
                                </td>
                                <td class=" text-center">
                                    @if ($item->scanStatus==0)
                                        <label class=" bg-danger p-5">NOT SCANNED</label>
                                    @elseif ($item->scanStatus==1)
                                    <label class=" bg-success p-5">SCANNED</label>
                                    @endif
                                </td>
                                <td class="">{{ number_format($item->ticketAmount,2) }}</td>
    
                                <td class="text-center">
                                    <ul class="icons-list">
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                <i class="icon-menu4"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li>
                                                    <a  data-toggle="modal" data-target="#editTicket{{ $item->ticketId }}" class="text-primary">MarkDestroyedTicket</a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </td>
    
                                {{-- edit role modal --}}
                                <div id="editTicket{{ $item->ticketId }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title">MARK DESTROYED TICKET </h5>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('gate_Collections.update',['gate_Collection' => $item->ticketId]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class=" form-group">
                                                        <label>Mark as Destroyed Ticket<i class=" text-danger">*</i></label>
                                                        <select name="ticketStatus" class="form-control" required>
                                                            @if ($item->ticketStatus==0)
                                                            <option value="0">DESTROYED TICKET</option> 
                                                            @else
                                                            <option value=""> - - - Select Ticket Status - - -</option>
                                                            @endif
                                                            <option value="0">DESTROYED TICKET</option>
                                                        </select>
                                                    </div>
                                                    <div class=" form-group">
                                                        <label>Demage Descriptions<i class=" text-danger">*</i></label>
                                                        <textarea name="ticketDescription" class=" form-control" placeholder="Pos Descriptions" required>{{ $item->ticketDescription }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- /edit role modal -->
    
                                {{-- delete modal --}}
                                <div id="deletePos{{ $item->ticketId }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title">Are you sure you whant to delete this POS ?</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('pos.destroy', ['po' => $item->ticketId]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div class=" text-right">
                                                        <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                                        <button type="submit" class="btn btn-danger ml-20" >Yes, Delete</button>
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
                <div class=" form-group col-md-12 text-right">
                    {{ $collections->links() }}

                </div>
            </div>
        </div>
    </div>
    <!-- /dashboard content -->
    {{-- add role modal --}}
    <form action="{{ route('pos.store') }}" method="POST"  enctype="multipart/form-data">
        @csrf
        <div id="addPos" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-blue" >
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title">Add user POS.</h5>
                    </div>
                    <div class="modal-body">

                        <div class="">
                            <div class=" form-group">
                                <label>POS Name<i class=" text-danger">*</i></label>
                                <input type="text" id="posName" name="posName" class=" form-control" placeholder=" permission Name" value="{{ old('posName') }}" required>
                            </div>
                            <div class=" form-group">
                                <label>POS Name<i class=" text-danger">*</i></label>
                                <textarea name="posDescription" class=" form-control" placeholder="Pos Descriptions"></textarea>
                            </div>
                            <div class=" form-group">
                                <label>POS STATUS<i class=" text-danger">*</i></label>
                                <select name="posStatus" class=" form-control">
                                    <option value="1">ACTIVE</option>
                                    <option value="0">INACTIVE</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Add POS</span></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{--  end  --}}
</x-app-layout>



