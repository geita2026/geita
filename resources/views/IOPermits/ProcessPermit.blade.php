<x-app-layout>
    <title>{{ env('APP_NAME') }} | Permit Processing</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> HOME</a></li>
                <li class="active">View Permits</li>
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
            <div class=" panel-body">
                @foreach ($permit as $item)
                    <div class="row">
                        <div class="col-md-10">
                            <p><b>Company Name: </b> {{ $item->companyName }}</p>
                            <p><b>Type Of Permit:</b> {{ $item->permit_type }}</p>
                            <p class=" text-justify"><b>Product List: </b>
                                {{ $item->itemList }}
                            </p>
                            <p><b>Permit Status: </b> {{ $item->permitStatus }}</p>
                           @if ($item->permitStatus=='APPROVED')
                                <p><b>Action: </b> <a target="_newtab" href="{{ route('download_permit', ['id' => $item->permit_id]) }}"
                                    class=" ml-20 text-primary">Print
                                    Permit <i class="icon-printer"></i>
                                </a></p>
                           @endif
                            <br><br>
                            @if ($item->permitStatus == 'UNDER_REVIEW' && Auth::user()->account_type == 1)
                                @include('IOPermits.PermitProcessingLogics')
                            @endif

                            <div class=" col-md-12 table-responsive">
                                <ul class="media-list chat-list content-group">
                                    @foreach ($comments as $com)
                                        <li class="media">
                                            <div class="media-body">
                                                <div class="media-content">
                                                    {{ $com->comment }}
                                                </div>
                                                <span class="media-annotation display-block mt-10"><a href="#"
                                                        class=" text-muted"><i class=" position-right "></i>
                                                        {{ $com->first_name }} {{ $com->last_name }}
                                                        [{{ $com->name }}]</a>
                                                    {{ date('M d, Y h:i:s', strtotime($com->commentDate)) }}
                                                </span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>


                        </div>
                        <div class=" col-md-2">
                            <button data-toggle="modal" data-target="#deletePermit{{ $item->permit_id }}"
                                class=" btn btn-danger">Delete Permit <i class=" icon-bin"></i></button>
                        </div>


                    </div>


                    <div id="deletePermit{{ $item->permit_id }}" class="modal fade" aria-modal="true">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h5 class="modal-title">Are you sure you whant to delete this Permit application ?</h5>
                                </div>
                                <div class="modal-body">
                                    <form
                                        action="{{ route('iopermits.destroy', ['iopermit' => $item->permit_id]) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class=" text-right">
                                            <button type="button" data-dismiss="modal"
                                                class=" btn btn-secondary">Close</button>
                                            <button type="submit" class="btn btn-danger ml-20">Yes, Delete</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach


            </div>
        </div>
    </div>
    <!-- /dashboard content -->
</x-app-layout>
