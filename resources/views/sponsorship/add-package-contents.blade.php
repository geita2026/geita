<x-app-layout>
    <title>{{ env('APP_NAME') }} | Sponsorship</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
         @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i>Home</a></li>
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
                    <a data-toggle="modal" data-target="#addContents" class=" btn btn-primary"> Add Benefit <i class=" icon-plus22"></i> </a>
                </div>
                {{--  retrieving data from sponsorship_packages table  --}}
                @foreach($packages as $package) @endforeach
                {{--  end here  --}}

                {{--  add contents  --}}
                <div id="addContents" class="modal fade" aria-modal="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary">
                                <h5 class="modal-title text-uppercase text-bold">ADD PACKAGE BENEFIT TO {{ $package->package_name }} Package.</h5>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('contents.store') }}" method="POST">
                                    @csrf
                                    <div class=" form-group row">
                                        <input type="hidden" name="package_id" value="{{ $package->package_id }}">
                                        <div class=" form-group col-md-8">
                                            <label>Benefits List<i class=" text-danger">*</i></label>
                                            <select name="benefitTitle" class=" bootstrap-select form-control">
                                                @foreach ($benefits as $item)
                                                <option value="{{ $item->benefitId }}">{{ $item->benefitTitle }}</option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger"> @error('benefitTitle'){{ $message }}@enderror</span>
                                         </div>
                                         <div class=" col-md-4">
                                            <label>Benefit Cost<i class=" text-danger">*</i></label>
                                            <input type="number" name="benefitCost" class=" form-control">
                                         </div>
                                         <div class=" col-md-12">
                                            <label>Benefit Description<i class=" text-danger">*</i></label>
                                            <textarea name="content_description" class=" form-control" placeholder="Type description Here . . ." required></textarea>
                                         </div>
                                       
                                   </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner">Save Benefits</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                {{--  add contents  --}}

                <table class="table datatable-button-html5-columns table-responsive table-bordered table-striped">
                    <thead>
                        <tr>
                            <th colspan="5" class=" text-bold text-uppercase text-center"><h4>{{ $package->package_name }} PACKAGE BENEFITS <i class=" icon-dots"></i> <b class=" text-primary">{{ number_format($package->package_amount,2) }} {{ $package->type_of_currency }}</b> </h4></th>
                        </tr>
                        <tr>
                            <th class=" text-bold">S/N</th>
                            <th class=" text-bold">Benefit Name</th>
                            <th class=" text-bold">Benefit Description</th>
                            <th class=" text-bold ">Benefit Cost</th>
                            <th class=" text-bold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i=1;
                        @endphp
                        @foreach ($contents as  $item)
                        <tr>
                            <td>{{ $i}}</td>
                            <td >{{ $item->benefitTitle }}</td>
                            <td>{{ $item->content_description }}</td>
                            <td class=" text-center">{{ number_format($item->contentCost,2) }} TZS</td>
                            <td class="text-center">
                                <a href="" data-toggle="modal" data-target="#delete{{ $item->package_content_id }}"  class=" btn btn-danger"><i class=" icon-bin"></i> Remove Benefit </a>
                            </td>

                           
                            <!-- /edit role modal -->
                            {{-- delete modal --}}
                            <div id="delete{{ $item->package_content_id }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <h5 class="modal-title">Are you sure you whant to Remove <b>{{ $item->benefitTitle }}</b> Item from Package ?</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('contents.destroy',['content'=>$item->package_content_id]) }}" method="POST">
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
