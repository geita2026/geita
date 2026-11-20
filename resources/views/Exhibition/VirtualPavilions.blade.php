<x-app-layout>
    <title>{{ env('APP_NAME') }} | VirtualPavilion Settings</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('virtual_pavilions.index') }}"><i class=" icon-menu10 position-left"></i> VIRTUAL
                        PAVILIONS</a></li>
                <li class="active">View All Virtual Pavilions</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <div class="content">
        <div class="panel panel-flat">
            <div class=" panel-body">

                <div class=" col-md-12 table-header form-group">
                    <h2 class=" text-center">VIRTUAL PAVILION SETTINGS</h2>
                </div>

                @if (Auth::user()->account_type == 1)
                    {{-- INTERNAL USERS --}}
                    <div class=" col-md-12">
                        <table class="table datatable-save-state table-striped table-bordered  table-responsive">
                            <thead>
                                <tr>
                                    <th class=" text-center text-bold">#</th>
                                    <th class=" text-center text-bold">PAVILION NAME</th>
                                    <th class=" text-center text-bold">PAVILION SETTING</th>
                                    <th class=" text-center text-bold">SERVICES</th>
                                    <th class=" text-center text-bold"> PAVILION STATUS</th>
                                    <th class=" text-center text-bold">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 1;
                                @endphp
                                @foreach ($virtual_pavilions as $item)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td class=" text-uppercase">{{ $item->companyName }}'S Pavilion</td>
                                        <td class=" text-center">
                                            <a href="{{ route('virtual_pavilions.edit', ['virtual_pavilion' => $item->participation_id]) }}"
                                                class=" text-primary btn btn-default">Pavilion Details <i
                                                    class=" icon-gear"></i></a>
                                        </td>
                                        <td class=" text-center">
                                            <a href="{{ route('virtual_services.show', ['virtual_service' => $item->participation_id]) }}"
                                                class=" text-primary btn btn-default">Services <i
                                                    class=" icon-gear"></i></a>
                                        </td>
                                        <td class=" text-center">
                                            @if ($item->visibility_status == 1)
                                                <label class=" label label-success"
                                                    title="Can be seen by site visitors">ACTIVE PAVILION</label>
                                            @else
                                                <label class=" label label-warning"
                                                    title="Can note be seen by site visitors">INACTIVE PAVILION</label>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <ul class="icons-list">
                                                <li class="dropdown">
                                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                        <i class="icon-menu4"></i>
                                                    </a>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        @can('Update Menu')
                                                            <li>
                                                                <a href="{{ route('virtual_pavilions.edit', ['virtual_pavilion' => $item->participation_id]) }}"
                                                                    class=" text-primary"><i
                                                                        class="icon-arrow-right13"></i>Virtual Pavilion
                                                                    Details</a>
                                                            </li>
                                                        @endcan
                                                        @can('Update Menu')
                                                            <li>
                                                                <a href="{{ route('virtual_services.show', ['virtual_service' => $item->participation_id]) }}"
                                                                    class=" text-primary"><i
                                                                        class=" icon-arrow-right13"></i>Virtual Service List
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('Delete Virtual Pavilion')
                                                            <li>
                                                                <a data-toggle="modal"
                                                                    data-target="#PavilionStatus{{ $item->participation_id }}"
                                                                    class=" text-danger"><i class="icon-arrow-right13"></i>
                                                                    Deactivate Virtual Pavilion</a>
                                                            </li>
                                                        @endcan
                                                    </ul>
                                                </li>
                                            </ul>
                                        </td>

                                        {{-- delete modal --}}
                                        <div id="PavilionStatus{{ $item->participation_id }}" class="modal fade"
                                            aria-modal="true">
                                            <div class="modal-dialog modal-md">
                                                <div class="modal-content">
                                                    <div
                                                        class="modal-header @if ($item->visibility_status == 1) bg-danger @else bg-success @endif">
                                                        <h5 class="modal-title">Are you sure you whant to change
                                                            Pavilion status ?</h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form
                                                            action="{{ route('virtual_pavilions.destroy', ['virtual_pavilion' => $item->participation_id]) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <div class=" row">
                                                                <div class=" form-group col-md-12">
                                                                    @if ($item->visibility_status == 1)
                                                                        <b class=" text-danger">Note!</b> This process
                                                                        'll make pavilion invisible from virtual
                                                                        pavilion List!
                                                                    @else
                                                                        <b class=" text-danger">Note!</b> This process
                                                                        'll make pavilion available in virtual pavilion
                                                                        List!
                                                                    @endif

                                                                </div>
                                                                <div class="  col-md-12 text-right">
                                                                    <button type="button" data-dismiss="modal"
                                                                        class=" btn btn-secondary">Close</button>
                                                                    <button type="submit"
                                                                        class="btn btn-danger ml-20">Yes, Change
                                                                        Status</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- delete modal --}}
                                    </tr>
                                    @php
                                        $i++;
                                    @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- END INTERNAL USERS --}}

                    {{-- EXTERNAL USERS --}}
                @elseif (Auth::user()->account_type == 2)
                    @foreach ($virtual_pavilions as $item)
                        <div class="col-md-12">
                            <div class=" bg-info-400 col-md-12 p-20 form-group text-center">
                                <b class=" text-uppercase" style="font-size: 2vmin">{{ $item->companyName }}'S
                                    PAVILION</b>
                            </div>

                            <div class=" row form-group col-md-12 text-center">
                                <div class=" col-md-6">
                                    <a href="{{ route('virtual_services.show', ['virtual_service' => $item->participation_id]) }}"
                                        class=" btn btn-default zoom border-primary text-primary p-20">Exhibited
                                        Products /
                                        Services <i class=" icon-list2"></i></a>
                                </div>

                                <div class=" col-md-6 ">
                                    <a href="{{ route('virtual_pavilions.edit', ['virtual_pavilion' => $item->participation_id]) }}"
                                        class=" btn btn-default zoom border-primary text-primary p-20 ml-20">Pavilion
                                        Setup <i class="icon-gear"></i></a>
                                </div>

                            </div>
                            
                            <div class="  col-md-12 form-group alert alert-info">
                                <p class=" ml-10">
                                    Copy the link bellow and share your pavilion with your customer to notify them your
                                    services and products exhibited in Dar es salaam International Trade Fair - 2024.
                                </p>
                                <br>
                                <div class=" col-md-10">
                                    <input type="pavilion_link" class=" form-control"
                                        value="{{ env('APP_URL') }}/discover_exhibitor/{{ $item->exhibitor_id }}"
                                        disabled id="CopyLink">
                                    {{-- <a data-toggle="modal" data-target="#SharePavilion{{ $item->participation_id }}" class=" btn btn-default border-primary zoom text-primary"><i class=" icon-share3"></i></a> --}}
                                </div>
                                <div class=" col-md-2 form-group">
                                    <button type="button" class="btn btn-default" onclick="myFunction()"><span
                                            class="ladda-label">Copy Link <i class=" icon-copy"></i></span></button>
                                </div>
                               
                            </div>

                            <div class=" col-md-12 form-group text-right">
                                <a href="{{ env('APP_URL') }}/discover_exhibitor/{{ $item->exhibitor_id }}"
                                    class=" btn btn-default text-primary zoom" target="_newtab">Go to my pavilion <i
                                        class=" icon-arrow-right13"></i></a>
                            </div>
                        </div>

                        {{-- Share pavilion --}}
                        <div id="SharePavilion{{ $item->participation_id }}" class="modal fade" aria-modal="true">
                            <div class="modal-dialog modal-md">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <input type="pavilion_link" class=" form-control"
                                            value="{{ env('APP_URL') }}/discover_exhibitor/{{ $item->exhibitor_id }}"
                                            readonly id="CopyLink">

                                        <br>
                                        <p class=" text-small text-muted">
                                            Copy and share link for this Pavilion to Make the world know this pavilion
                                            too!<br>
                                            <b><i class=" icon-quotes-left"></i>TanTrade is foostering Business<i
                                                    class=" icon-quotes-right"></i></b>
                                        </p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-link"
                                            data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary" onclick="myFunction()"><span
                                                class="ladda-label">Copy Link <i
                                                    class=" icon-copy"></i></span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Share pavilion --}}

                        <div class=" col-md-12">
                            @if ($item->visibility_status == 1)
                                <span>Pavilion is seen virtualy, world wide. share it to make people know what you offer
                                    in DITF</span>
                            @elseif ($item->visibility_status == 0)
                            @endif
                        </div>


                        {{-- delete modal --}}
                        <div id="PavilionStatus{{ $item->participation_id }}" class="modal fade" aria-modal="true">
                            <div class="modal-dialog modal-md">
                                <div class="modal-content">
                                    <div
                                        class="modal-header @if ($item->visibility_status == 1) bg-danger @else bg-success @endif">
                                        <h5 class="modal-title">Are you sure you whant to change Pavilion status ?</h5>
                                    </div>
                                    <div class="modal-body">
                                        <form
                                            action="{{ route('virtual_pavilions.destroy', ['virtual_pavilion' => $item->participation_id]) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class=" row">
                                                <div class=" form-group col-md-12">
                                                    @if ($item->visibility_status == 1)
                                                        <b class=" text-danger">Note!</b> This process 'll make
                                                        pavilion invisible from virtual pavilion List!
                                                    @else
                                                        <b class=" text-danger">Note!</b> This process 'll make
                                                        pavilion available in virtual pavilion List!
                                                    @endif

                                                </div>
                                                <div class="  col-md-12 text-right">
                                                    <button type="button" data-dismiss="modal"
                                                        class=" btn btn-secondary">Close</button>
                                                    <button type="submit"
                                                        class="btn @if ($item->visibility_status == 1) bg-danger @else bg-success @endif
                                                        ml-20">Yes,
                                                        Change Status</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- delete modal --}}
                    @endforeach
                @endif

                {{-- END EXTERNAL USERS --}}
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function myFunction() {
        // Get the text field
        var copyText = document.getElementById("CopyLink");

        // Select the text field
        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices

        // Copy the text inside the text field
        navigator.clipboard.writeText(copyText.value);

        // Alert the copied text
        $(document).ready(function() {
            new PNotify({
                title: 'OK',
                text: 'Link copied, Paste where you want to share...!',
                icon: 'icon-check',
                type: 'success'
            });
        });
        //End alert
        $('#SharePavilion').modal('toggle');
    }
</script>
