<x-app-layout>
    <title>{{ env('APP_NAME') }} | Event Programs</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('permissions.index') }}"><i class="icon-home2 position-left"></i> EVENT PROGRAMS</a></li>
                <li class="active">View All Programs</li>
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

                <div class="form-group">
                    <div class=" col-md-12 text-right">
                        <a data-toggle="modal" data-target="#AddProgram" class=" btn btn-default text-primary border-primary">Add Program <i class=" icon-plus2"></i></a>
                    </div>
                </div>

                <div class=" form-group">
                      {{-- Error or success messages --}}
                      @if ($errors->any())
                      @foreach ($errors->all() as $error)
                      <span class=" text-danger">{{ $error }} ,</span>
                      @endforeach
                      @endif
                      {{-- //error messages --}}
                </div>

                <div id="AddProgram" class="modal fade" aria-modal="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary">
                                <h5 class="modal-title text-bold text-center">
                                    CREATE EVENT PROGRAMS
                                </h5>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('event_programs.store')}}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class=" form-group">
                                        <label>Program Title<i class=" text-danger">*</i></label>
                                        <input type="text" name="program_title" class=" form-control" placeholder=" Enter program Title" value="{{ old('program_title') }}" required>
                                    </div>
                                    <div class=" form-group">
                                        <label>Program descriptions<i class=" text-danger">*</i></label>
                                        <textarea name="program_description" class=" form-control" placeholder="Program Descriptions" required>{{ old('program_description') }}</textarea>
                                    </div>
                                    <div class=" form-group">
                                        <label>Characters / Wahusika</label>
                                        <input type="text" name="characters" class=" form-control" placeholder="Characters" required>
                                    </div>

                                    <div class=" form-group">
                                        <label>Program Date <i class="text-danger">*</i></label>
                                        <input type="date" name="program_date" class=" form-control" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class=" form-group">
                                        <label>Program Poster <i class="text-danger">*</i></label>
                                        <input type="file" name="programPoster" class=" form-control" >
                                    </div>
                                    <div class=" text-right">
                                        <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                        <button type="submit" class="btn btn-primary ml-20" >SAVE</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


                <div class=" form-group col-md-12">
                    <table class="table datatable-save-state table-striped table-bordered  table-responsive">
                        <thead>
                            <tr class="table-header">
                                <th class=" text-center text-bold text-uppercase" colspan="6">{{ $eventName }} PROGRAMS</th>
                            </tr>
                            <tr>
                                <th class=" text-center text-bold">#</th>
                                <th class=" text-center text-bold">PROGRAM TITLE</th>
                                <th class=" text-center text-bold">PROGRAM DESCRIPTIONS</th>
                                <th class=" text-center text-bold">PROGRAM DATE</th>
                                <th class=" text-center text-bold">PROGRAM STATUS</th>
                                <th class=" text-center text-bold">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $i=1;
                            @endphp
                            @foreach ($programs as  $item)
                            <tr>
                                <td>{{ $i}}</td>
                                <td>{{ $item->program_title }}</td>
                                <td>{{ $item->program_description }}</td>
                                <td>{{ date('M d, Y',strtotime($item->program_date)) }}</td>
                                <td class=" text-center">
                                    @if ($item->share_program==1)
                                    <label class=" label label-success">PUBLISHED</label>
                                    @elseif ($item->share_program==0)
                                    <label class=" label label-warning">UNPUBLISHED</label>
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
                                                    <a href=""  data-toggle="modal" data-target="#UpdateProgram{{ $item->program_id }}"  class="text-primary"> Update Program <i class="icon-pencil"></i></a>
                                                </li>
                                                <li>
                                                    <a href=""  data-toggle="modal" data-target="#DeleteProgram{{ $item->program_id }}"  class="text-danger"> Delete Program <i class=" icon-bin"></i></a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </td>

                                {{-- delete modal --}}
                                <div id="UpdateProgram{{ $item->program_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">
                                             <h5 class="modal-title">UPDATE <b>{{ $item->program_title }}</b>.</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('event_programs.update', ['event_program' => $item->program_id]) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class=" form-group">
                                                        <label>Program Title<i class=" text-danger">*</i></label>
                                                        <input type="text" name="program_title" class=" form-control" placeholder=" Enter program Title" value="{{ $item->program_title }}" required>
                                                    </div>
                                                    <div class=" form-group">
                                                        <label>Program descriptions<i class=" text-danger">*</i></label>
                                                        <textarea name="program_description" class=" form-control" placeholder="Program Descriptions" required>{{ $item->program_description }}</textarea>
                                                    </div>
                                                    <div class=" form-group">
                                                        <label>Characters / Wahusika</label>
                                                        <input type="text" name="characters" class=" form-control" placeholder="Characters" value="{{ $item->characters }}" required>
                                                    </div>
                                                    <div class=" form-group">
                                                        <label>Program Date <i class="text-danger">*</i></label>
                                                        <input type="date" name="program_date" class=" form-control" value="{{ date('Y-m-d',strtotime($item->program_date)) }}" min="{{ date('Y-m-d') }}" required>
                                                    </div>
                                                    <div class=" form-group">
                                                        <label>Program Status</label>
                                                        <select name="share_program" class=" form-control">
                                                            <option value="1">PUBLISH PROGRAM</option>
                                                            <option value="0">UNPUBLISH PROGRAM</option>
                                                        </select>
                                                    </div>
                                                    <div class=" form-group">
                                                        <label>Program Poster <i class="text-danger">*</i></label>
                                                        <input type="file" name="programPoster" class=" form-control" >
                                                    </div>
                                                    <div class=" form-group">
                                                        <img src="{{ $item->program_file }}" height="50" width="100">
                                                    </div>

                                                    <div class=" text-right">
                                                        <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                                        <button type="submit" class="btn btn-primary ml-20" >Yes, Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- delete modal --}}
                                <div id="DeleteProgram{{ $item->program_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                             <h5 class="modal-title">Are you sure you whant to delete this program ?</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('event_programs.destroy', ['event_program' => $item->program_id]) }}" method="POST">
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
    <!-- /dashboard content -->
</x-app-layout>
