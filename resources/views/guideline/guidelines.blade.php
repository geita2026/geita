<x-app-layout>
    <title>{{ env('APP_NAME') }} | GUIDELINES</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i>GUIDELINES</a></li>
                <li class="active">View All guidelines</li>
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
            <div class="panel-heading">
                <h6 class="panel-title text-bold">REGISTERED GUIDELINES</h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                        <li><a data-action="close"></a></li>
                    </ul>
                </div>
            </div>
            <div class=" panel-body table-responsive">
                @if ($errors->any())
                @foreach ($errors->all() as $error)
                <span class=" text-danger alert">{{ $error }} ,</span>
                @endforeach
                @endif
                <span class=" text-danger">@error('permission_name') {{ $message }} @enderror</span>
                <div class=" form-group text-right">
                    <a data-toggle="modal" data-target="#NewGuideline"  class=" btn btn-default text-primary"><i class=" icon-plus2"></i> Add Guidelines </a>
                </div>


                <div class=" form-group">
                    <table class="table table-sm datatable-button-html5-columns table-bordered table-striped table-responsive">
                        <thead>
                            <tr style="background-color: rgb(219, 215, 215);">
                                <th colspan="5" class=" text-center text-bold">
                                    <h4>LIST OF REGISTERED EXHIBITION GUIDELINES</h4>
                                </th>
                            </tr>
                            <tr>
                                <th class=" text-bold text-center">#</th>
                                <th class=" text-bold text-center">GUIDLINE TITLE</th>
                                <th class=" text-bold text-center">GUIDELINE DESCRIPTIONS</th>
                                <th class=" text-bold text-center">SubGuidlines</th>
                                <th class=" text-bold text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $i=1;
                            @endphp
                            @foreach ($guidelines as  $item)
                            <tr>

                                <td>{{ $i }}</td>
                                <td class=" text-bold">{{ $item->guideline_title }}</td>
                                <td>{{ $item->guideline_descriptions }} </td>
                                <td class=" text-center"><a href="{{ route('ditf_guidelines.show',['ditf_guideline'=>$item->guideline_id]) }}" class=" btn btn-default text-primary"> SubGuidelines . . .<i class=" icon-arrow-right13"></i></a></td>

                                <td class="text-center">
                                    <ul class="icons-list">
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                <i class="icon-menu4"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li>
                                                    <a  data-toggle="modal" data-target="#edit{{ $item->guideline_id }}" class="text-primary"> <i class=" icon-database-edit2"></i> Edit Guidelines</a>
                                                </li>
                                                <li>
                                                    <a href="" data-toggle="modal" data-target="#delete{{ $item->guideline_id }}"  class="text-danger"> Delete Guidelines <i class=" icon-bin"></i></a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </td>

                                {{-- edit role modal --}}
                                <div id="edit{{ $item->guideline_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title text-uppercase"><b>{{ $item->Guidelines_name }}</b></h5>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('ditf_guidelines.update',['ditf_guideline'=>$item->guideline_id]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')

                                                        <div class=" row">
                                                            <div class=" form-group col-md-6">
                                                                <label>Guidelines Title<i class=" text-danger">*</i></label>
                                                                <input type="text" name="guideline_title" class=" form-control" placeholder=" Guideline Title" value="{{ $item->guideline_title }}">
                                                                <span class="text-danger"> @error('guideline_title'){{ $message }}@enderror</span>
                                                             </div>

                                                               <div class=" form-group col-md-6">
                                                                   <label>Guidelines Descriptions</label>
                                                                   <textarea name="guideline_descriptions" class=" form-control" placeholder="Guidelines Descriptions">{{ $item->guideline_descriptions }}</textarea>
                                                                   <span class="text-danger"> @error('guideline_descriptions'){{ $message }}@enderror</span>
                                                             </div>
                                                        </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"> Update Guidlines</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- /edit role modal -->

                                {{-- delete modal --}}
                                <div id="delete{{ $item->guideline_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <h5 class="modal-title">Are you sure you whant to delete <b>{{ $item->guideline_title }}</b> Sponsorship Guidelines ?</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('ditf_guidelines.destroy',['ditf_guideline'=>$item->guideline_id]) }}" method="POST">
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
                                {{-- delete modal --}}
                            </tr>
                            @php
                            $i++;
                            @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>


            </div>
        </div>
    </div>
</x-app-layout>


 {{-- Add new Guidlines --}}
 <div id="NewGuideline" class="modal fade" aria-modal="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title text-uppercase text-center text-bold"> REGISTER NEW EXHIBITION GUIDELINE </h5>
            </div>
            <div class="modal-body">
                <form action="{{ route('ditf_guidelines.store') }}" method="POST">
                    @csrf
                        <div class=" row">
                            <div class=" form-group col-md-12">
                                <label>Guidelines Title<i class=" text-danger">*</i></label>
                                <input type="text" name="guideline_title" class=" form-control" placeholder=" Guideline Title" value="{{ old('guideline_title') }}">
                                <span class="text-danger"> @error('guideline_title'){{ $message }}@enderror</span>
                             </div>

                               <div class=" form-group col-md-12">
                                   <label>Guidelines Descriptions</label>
                                   <textarea name="guideline_descriptions" class=" form-control" placeholder="Guidelines Descriptions">{{ old('guideline_descriptions') }}</textarea>
                                   <span class="text-danger"> @error('guideline_descriptions'){{ $message }}@enderror</span>
                             </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"> Save Guideline</button>
                </div>
            </form>
        </div>
    </div>
</div>
