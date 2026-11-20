<x-app-layout>
    <title>{{ env('APP_NAME') }} | Catalogue Settings</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="#"><i class="icon-home2 position-left"></i>CATALOGUE SETTINGS</a></li>
            <li class="active">View All catalogue settings</li>
        </ul>

        <ul class="breadcrumb-elements">
            @include('layouts.help')
        </ul>
    </div>
    </div>
<!-- /page header -->

<!-- Content area -->
<div class="content">

<div class="panel panel-flat">
<div class=" panel-body table-responsive">

    {{-- errors --}}
     @if ($errors->any())
     @foreach ($errors->all() as $error)
     <span class=" text-danger"><li>{{ $error }}</li></span>
     @endforeach
     @endif
     {{-- end errors --}}

        <div class=" form-group text-right">
        <button type="submit" class=" btn btn-primary" data-toggle="modal" data-target="#AddApriciationWord"> Add Leader Quatation <i class=" icon-plus2"></i></button>
        </div>
        <table class="table datatable-save-state table-striped table-bordered  table-responsive">
            <thead>
                <tr>
                    <th class=" text-bold text-center">ORDER #</th>
                    <th class="text-bold text-center">LEADER NAME</th>
                    <th class="text-bold text-center">LEADER TITLE</th>
                    <th class="text-bold text-center">LEADER IMAGE</th>
                    <th class="text-bold text-center">LEADER WORD</th>
                    <th class="text-bold text-center">EXHIBITION EVENT</th>
                    <th class="text-bold text-center">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @php$i=1;@endphp
                @foreach ($catalogueSettings as $item)
                <tr>
                    <td class=" text-center">{{ $item->order_number }}</td>
                    <td >{{ $item->leader_name }}</td>
                    <td>{{ $item->leader_title }}</td>
                    <td class=" text-right">
                        <img src="{{ asset('CatalogueImages/'.$item->leader_image) }}" height="80" width="80">
                    </td>
                    <td >{{ $item->appriciation_word }}</td>
                    <td>{{ $item->event_name }}</td>




                    <td class="text-center">
                        <ul class="icons-list">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="icon-menu4"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a  data-toggle="modal" data-target="#UpdateLeader{{ $item->leader_id }}" class=" text-primary"><i class="icon-pencil7"></i> Edit Leader</a>
                                    </li>
                                    <li>
                                        <a  data-toggle="modal" data-target="#DeleteLeader{{ $item->leader_id }}"  class="text-danger"> <i class=" icon-bin"></i> Delete Leader</a>
                                    </li>

                                </ul>
                            </li>
                        </ul>
                    </td>
                    {{-- edit Leader modal --}}
                    <div id="UpdateLeader{{ $item->leader_id }}" class="modal fade" aria-modal="true" data-backdrop="static">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h5 class="modal-title text-uppercase">UPDATE LEADER DETAILS.</h5>
                                </div>
                                <div class=" modal-body">
                                <form action="{{ route('catalogue_settings.update',['catalogue_setting'=>$item->leader_id]) }}"  method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PATCH')
                                <div class="row">
                                    <div class=" row">
                                        <div class=" form-group col-md-6">
                                            <label>Leader Name<i class=" text-danger">*</i></label>
                                               <input type="text" name="leader_name" class=" form-control" placeholder=" Leader Name" value="{{ $item->leader_name}}">
                                               <span class=" text-danger">@error('leader_name') {{ $message }} @enderror</span>
                                         </div>
                                         <div class=" form-group col-md-6">
                                             <label>Leader Title<i class=" text-danger">*</i></label>
                                                <input type="text" name="leader_title" class=" form-control" placeholder=" Leader Title" value="{{ $item->leader_title }}">
                                                <span class=" text-danger "> @error('leader_title') {{ $message }} @enderror</span>
                                        </div>
                                    </div>

                                    <div class=" row">
                                        <div class=" form-group col-md-6">
                                            <label>Leader Image<i class="text-danger">*</i> </label>
                                            <input type="file" name="leader_image" class=" form-control">
                                            <img src="{{ asset('CatalogueImages/'.$item->leader_image) }}" height="50" width="50">
                                            <span class=" text-danger">@error('leader_image') {{ $message }} @enderror</span>
                                        </div>

                                        <div class=" form-group col-md-6">
                                            <label>Order Number<i class="text-danger">*</i> </label>
                                            <input type="number" name="order_number" class=" form-control" placeholder="Enter Order Number" value="{{ $item->order_number }}">
                                        </div>

                                    </div>

                                <div class=" row">
                                    <div class=" form-group col-md-6">
                                        <label> Select Event<i class=" text-danger">*</i></label>
                                       <select name="event" class=" form-control" data-live-search="true" data-width="100%" placehoder="Select Event">
                                           <option value="{{ $item->event }}">{{ $item->event_name }}</option>
                                             @foreach ($events as $event)
                                             <option value="{{ $event->event_id }}">{{ $event->event_name }}</option>
                                             @endforeach
                                       </select>
                                       <span class=" text-danger">@error('event'){{ $message }} @enderror</span>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Appriciation Word</label>
                                        <textarea class="form-control" name="appriciation_word" placeholder="Leader Appriciation Word">{{ $item->appriciation_word }}</textarea>
                                    </div>
                                </div>
                                </div>
                                <input type="hidden" name="old_image" value="{{ $item->leader_image }}">
                            </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span class="ladda-label">Update Leader</span></button>
                            </div>
                        </form>
                    </div>
                            </div>
                        </div>
                    </div>

                    <!-- /edit role modal -->
                    {{-- delete modal --}}
                             <div id="DeleteLeader{{ $item->leader_id }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Are you sure you whant to delete <b><q>{{ $item->leader_name }}</q></b> Leader ?</h5>
                                        </div>

                                        <div class="modal-body">
                                            <form action="{{ route('catalogue_settings.destroy', ['catalogue_setting' => $item->leader_id]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                               <div class=" text-right">
                                                <button type="button" data-dismiss="modal" class=" btn btn-default">Close</button>
                                                <button type="submit" class="btn btn-danger btn-ladda btn-ladda-spinner ml-20"  data-style="slide-down">Yes, Delete</button>
                                               </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- delete modal --}}
                       </tr>
                        @endforeach
                    </tbody>

                </table>

            </div>
            {{--  end here  --}}

        </div>
    </div>
<!-- /dashboard content -->

{{-- add role modal --}}
<div id="AddApriciationWord" class="modal fade" aria-modal="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-blue" >
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title text-center text-bold">ADD LEADER APPRICIATION WORD</h5>
            </div>
            <div class="modal-body">
                <form action="{{ route('catalogue_settings.store') }}"  method="POST" enctype="multipart/form-data">
                    @csrf
                <div class="row">
                    <div class=" row">
                        <div class=" form-group col-md-6">
                            <label>Leader Name<i class=" text-danger">*</i></label>
                               <input type="text" name="leader_name" class=" form-control" placeholder=" Leader Name" value="{{ old('leader_name')}}">
                               <span class=" text-danger">@error('leader_name') {{ $message }} @enderror</span>
                         </div>
                         <div class=" form-group col-md-6">
                             <label>Leader Title<i class=" text-danger">*</i></label>
                                <input type="text" name="leader_title" class=" form-control" placeholder=" Leader Title" value="{{ old('leader_title') }}">
                                <span class=" text-danger "> @error('leader_title') {{ $message }} @enderror</span>
                        </div>
                    </div>

                    <div class=" row">
                        <div class=" form-group col-md-6">
                            <label>Leader Image<i class="text-danger">*</i> </label>
                            <input type="file" name="leader_image" class=" form-control">
                            <span class=" text-danger">@error('leader_image') {{ $message }} @enderror</span>
                        </div>

                        <div class=" form-group col-md-6">
                            <label>Order Number<i class="text-danger">*</i> </label>
                            <input type="number" name="order_number" class=" form-control" placeholder="Enter Order Number">
                        </div>

                    </div>

                <div class=" row">
                    <div class=" form-group col-md-6">
                        <label> Select Event<i class=" text-danger">*</i></label>
                       <select name="event" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select Event">
                           <option value=""> - - - Select Events - - - </option>
                             @foreach ($events as $event)
                             <option value="{{ $event->event_id }}">{{ $event->event_name }}</option>
                             @endforeach
                       </select>
                       <span class=" text-danger">@error('event'){{ $message }} @enderror</span>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Appriciation Word</label>
                        <textarea class="form-control" name="appriciation_word" placeholder="Leader Appriciation Word">{{ old('leader_word') }}</textarea>
                    </div>
                </div>
                </div>

            </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span class="ladda-label">Register Leader</span></button>
            </div>
        </form>
        </div>
    </div>
</div>

</x-app-layout>


