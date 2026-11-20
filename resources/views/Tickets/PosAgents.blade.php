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
                    <h4 class=" text-center">REGISTERED AGENTS</h4>
                </div>
                <div class=" form-group text-right">
                    <button type="submit" class=" btn btn-success" data-toggle="modal" data-target="#addAgent">Add
                        Agent <i class=" icon-plus2"></i></button>
                </div>
                <div class=" form-group col-md-12">
                    <form action="{{ route('agents.index') }}" method="POST">
                        @csrf
                        @method('GET')
                        <div class=" form-group col-md-8">
                            <input type="text" placeholder="Full Name" name="search" class=" form-control">
                        </div>
                        <div class=" col-md-4">
                            <button type="submit" class=" btn btn-primary">Search <i class=" icon-arrow-right13"></i></button>
                        </div>

                    </form>
                </div>
                <div class=" col-md-12 form-group">
                    <table class="table datatable-button-html5-columns table-responsive table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class=" text-bold">#</th>
                                <th class=" text-bold">AGENT NAME</th>
                                <th class=" text-bold">USERNAME</th>
                                <th class=" text-bold">Phone Number</th>
                                <th class=" text-bold">AGENT LOCATION</th>
                                <th class=" text-bold">AGENT POS</th>
                                <th class=" text-bold">AGENT STATUS</th>
                                <th class=" text-bold">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($agents as $item)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td class=" text-uppercase">{{ $item->agentName }}</td>
                                    <td>{{ $item->userName }}</td>
                                    <td>{{ $item->mobileNumber }}</td>
                                    <td>{{ $item->locationName }}</td>
                                    <td>{{ $item->posName }}</td>
                                    <td class=" text-center">
                                        @if ($item->agentStatus == 0)
                                            <label class=" bg-danger p-5">INACTIVE</label>
                                        @elseif ($item->agentStatus == 1)
                                            <label class=" bg-success p-5">ACTIVE</label>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <ul class="icons-list">
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                    <i class="icon-menu4"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li>
                                                        <a href="{{ route('agents.show', ['agent' => $item->agentId]) }}"
                                                            class="text-primary">Manage Collections<i
                                                                class=" icon-database-edit2"></i></a>
                                                    </li>
                                                    <li>
                                                        <a data-toggle="modal"
                                                            data-target="#editAgent{{ $item->agentId }}"
                                                            class="text-primary">Edit Agent <i
                                                                class=" icon-database-edit2"></i></a>
                                                    </li>
                                                    <li>
                                                        {{-- <form>
                                                        <button id="sweet_warning{{ $item->agentId }}" > Delete Permission <i class=" icon-bin"></i></button>
                                                    </form> --}}
                                                        <a href="" data-toggle="modal"
                                                            data-target="#deleteAgent{{ $item->agentId }}"
                                                            class="text-danger">
                                                            Delete Agent <i class=" icon-bin"></i></a>
                                                        <a class="text-danger btn"> </a>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </td>

                                    {{-- edit role modal --}}
                                    <div id="editAgent{{ $item->agentId }}" class="modal fade" aria-modal="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">Edit Agent.</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form
                                                        action="{{ route('agents.update', ['agent' => $item->agentId]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')

                                                        <div class="">

                                                            <div class=" form-group col-md-6">
                                                                <label>Agent Full Name<i
                                                                        class=" text-danger">*</i></label>
                                                                <input type="text" name="agentName"
                                                                    class=" form-control" placeholder=" agent Name"
                                                                    value="{{ $item->agentName }}" required>
                                                            </div>
                                                            <div class=" form-group col-md-6">
                                                                <label>Agent UserName<i
                                                                        class=" text-danger">*</i></label>
                                                                <input type="text" name="userName"
                                                                    class=" form-control" placeholder=" User Name"
                                                                    value="{{ $item->userName }}" required>
                                                            </div>
                                                            <div class=" form-group col-md-6">
                                                                <label>Agent Mobile Number<i
                                                                        class=" text-danger">*</i></label>
                                                                <input type="text" name="mobileNumber"
                                                                    class=" form-control"
                                                                    placeholder=" agent Phone Number"
                                                                    value="{{ $item->mobileNumber }}" required>
                                                            </div>
                                                            <div class=" form-group col-md-6">
                                                                <label>Agent Location<i
                                                                        class=" text-danger">*</i></label>
                                                                <select name="asignedLocation" class=" form-control"
                                                                    required>
                                                                    <option value="{{ $item->asignedLocation }}">
                                                                        {{ $item->locationName }}</option>
                                                                    @foreach ($locations as $locate)
                                                                        <option value="{{ $locate->locationId }}">
                                                                            {{ $locate->locationName }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class=" form-group col-md-6">
                                                                <label>Agent POS <i class=" text-danger">*</i></label>
                                                                <select name="asignedPos" class=" form-control"
                                                                    required>
                                                                    <option value="{{ $item->asignedPos }}">
                                                                        {{ $item->posName }}</option>
                                                                    @foreach ($pos as $po)
                                                                        <option value="{{ $po->agentId }}">
                                                                            {{ $po->posName }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class=" form-group col-md-6">
                                                                <label>POS STATUS<i class=" text-danger">*</i></label>
                                                                <select name="agentStatus" class=" form-control">
                                                                    <option value="1">ACTIVE</option>
                                                                    <option value="0">INACTIVE</option>
                                                                </select>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-link"
                                                                    data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Save
                                                                    changes</button>
                                                            </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /edit role modal -->

                                    {{-- delete modal --}}
                                    <div id="deleteAgent{{ $item->agentId }}" class="modal fade" aria-modal="true">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">
                                                    <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                    <h5 class="modal-title">Are you sure you whant to delete this Agent
                                                        ?
                                                    </h5>
                                                </div>
                                                <div class="modal-body">
                                                    <form
                                                        action="{{ route('agents.destroy', ['agent' => $item->agentId]) }}"
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
                    {{ $agents->links() }}
                </div>
            </div>
        </div>
    </div>
    <!-- /dashboard content -->
    {{-- add role modal --}}
    <form action="{{ route('agents.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div id="addAgent" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-blue">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title">Add Agent.</h5>
                    </div>
                    <div class="modal-body">
                        <div class=" row col-md-12">
                            <div class=" form-group col-md-6">
                                <label>Agent Full Name<i class=" text-danger">*</i></label>
                                <input type="text" name="agentName" class=" form-control"
                                    placeholder=" agent Name" value="{{ old('agentName') }}" required>
                            </div>
                            <div class=" form-group col-md-6">
                                <label>Agent UserName<i class=" text-danger">*</i></label>
                                <input type="text" name="userName" class=" form-control" placeholder=" User Name"
                                    value="{{ old('userName') }}" required>
                            </div>
                            <div class=" form-group col-md-6">
                                <label>Agent Mobile Number<i class=" text-danger">*</i></label>
                                <input type="text" name="mobileNumber" class=" form-control"
                                    placeholder=" mobile Number" value="{{ old('mobileNumber') }}" required>
                            </div>
                            
                            <div class=" form-group col-md-6">
                                <label>Agent Location<i class=" text-danger">*</i></label>
                                <select name="asignedLocation" class=" form-control" required>
                                    <option>--- Select Location - - -</option>
                                    @foreach ($locations as $locate)
                                        <option value="{{ $locate->locationId }}">{{ $locate->locationName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" form-group col-md-6">
                                <label>Agent POS<i class=" text-danger">*</i></label>
                                <select name="asignedPos" class=" form-control" required>
                                    <option>--- Select POS - - -</option>
                                    @foreach ($pos as $po)
                                        <option value="{{ $po->posId }}">{{ $po->posName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class=" form-group col-md-6">
                                <label>AGENT STATUS<i class=" text-danger">*</i></label>
                                <select name="agentStatus" class=" form-control">
                                    <option value="1">ACTIVE</option>
                                    <option value="0">INACTIVE</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span
                                class="ladda-label">Add Agent</span></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{--  end  --}}

    {{-- The per-row Edit/Delete modals are rendered inside the table, so the
         browser foster-parents them and they end up trapped behind the modal
         backdrop (black overlay, no dialog). Relocate them to <body> so they
         display correctly. The Add Agent modal is intentionally NOT moved —
         its <form> wraps the modal, and detaching it would break submitting. --}}
    <script>
        $(function () {
            $('[id^="editAgent"], [id^="deleteAgent"]').appendTo(document.body);
        });
    </script>
</x-app-layout>
