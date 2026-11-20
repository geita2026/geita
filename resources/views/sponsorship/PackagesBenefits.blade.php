<x-app-layout>
    <title>{{ env('APP_NAME') }} | Sponsorship</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
         @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> Home</a></li>
                <li class="active">View package contents</li>
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
                @if ($errors->any())
                @foreach ($errors->all() as $error)
                <span class=" text-danger alert">{{ $error }} </span>
                @endforeach
                @endif

                <span class=" text-danger">@error('permission_name') {{ $message }} @enderror</span>
                <div class=" form-group col-md-12 text-right" >
                    <a data-toggle="modal" data-target="#addContents" class=" btn btn-success"> Add Benefit <i class=" icon-add"></i> </a>
                </div>
                
                {{--  add contents  --}}
                <div id="addContents" class="modal fade" aria-modal="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary">
                                <h5 class="modal-title text-uppercase text-bold">ADD NEW BENEFIT.</h5>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('sponsorship_benefits.store') }}" method="POST">
                                    @csrf
                                    <div class="">
                                       
                                        <div class=" form-group col-md-12">
                                            <label>Benefit Title<i class=" text-danger">*</i></label>
                                            <input type="text" name="benefitTitle" class=" form-control" placeholder="Benefit Title Here" >
                                            <span class="text-danger"> @error('benefitTitle'){{ $message }}@enderror</span>
                                         </div>
                                       <div class=" form-group col-md-12">
                                           <label>Benefit Description</label>
                                           <textarea name="benefitDescription" class=" form-control" placeholder="benefit Description"></textarea>
                                           <span class="text-danger"> @error('benefitDescription'){{ $message }}@enderror</span>
                                        </div>
                                   </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                {{--  add contents  --}}

                <table class="table datatable-button-html5-columns table-responsive table-bordered table-striped">
                    <thead>
                        <tr>
                            <th colspan="4" class=" text-bold text-uppercase text-center"> <h3>BENEFITS LIST</h3></th>
                        </tr>
                        <tr>
                            <th class=" text-bold">S/N</th>
                            <th class=" text-bold">Benefit Name</th>
                            <th class=" text-bold">Benefit Description</th>
                            
                            <th class=" text-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i=1;
                        @endphp
                        @foreach ($benefits as  $item)
                        <tr>
                            <td>{{ $i}}</td>
                            <td>{{ $item->benefitTitle }}</td>
                            <td>{{ $item->benefitDescription }}</td>
                           
                            <td class="text-center">
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu4"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li>
                                                <a  data-toggle="modal" data-target="#edit{{ $item->benefitId }}" class="text-primary"> <i class=" icon-database-edit2"></i> Edit Benefit</a>
                                            </li>
                                            <li>
                                                <a href="" data-toggle="modal" data-target="#delete{{ $item->benefitId }}"  class="text-danger"><i class=" icon-bin"></i> Delete Benefit </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </td>

                            {{-- edit role modal --}}
                            <div id="edit{{ $item->benefitId }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary">
                                            <h5 class="modal-title text-uppercase"> Update <b>{{ $item->benefitTitle }}</b> Benefit</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('sponsorship_benefits.update',['sponsorship_benefit'=>$item->benefitId]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="">
                                       
                                                    <div class=" form-group col-md-12">
                                                        <label>Benefit Title<i class=" text-danger">*</i></label>
                                                        <input type="text" name="benefitTitle" class=" form-control" placeholder="Benefit Title Here" value="{{ $item->benefitTitle }}">
                                                        <span class="text-danger"> @error('benefitTitle'){{ $message }}@enderror</span>
                                                     </div>
                                                    
            
                                                   <div class=" form-group col-md-12">
                                                       <label>Benefit Description</label>
                                                       <textarea name="benefitDescription" class=" form-control" placeholder="benefit Description">{{ $item->benefitDescription }}</textarea>
                                                       <span class="text-danger"> @error('benefitDescription'){{ $message }}@enderror</span>
                                                    </div>
                                               </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- /edit role modal -->
                            {{-- delete modal --}}
                            <div id="delete{{ $item->benefitId }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <h5 class="modal-title">Are you sure you whant to delete <b>{{ $item->benefitTitle }}</b> Benefit from Package ?</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('sponsorship_benefits.destroy',['sponsorship_benefit'=>$item->benefitId]) }}" method="POST">
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
</x-app-layout>
