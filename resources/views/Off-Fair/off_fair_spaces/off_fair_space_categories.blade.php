<x-app-layout>
    <title>{{ env('APP_NAME') }} | Spaces</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
@include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i>OFF FAIR SPACE CATEGORIES</a></li>
            <li class="active">View All space Categories</li>
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
            <h6 class="panel-title text-bold">OFF FAIR SPACE CATEGORIES</h6>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                    <li><a data-action="reload"></a></li>
                    <li><a data-action="close"></a></li>
                </ul>
            </div>
        </div>
<div class=" panel-body table-responsive">

    {{-- validation messages --}}
    @if ($errors->any())
    @foreach ($errors->all() as $error)
    <span class="alert text-danger">{{ $error }} ,</span>
    @endforeach
    @endif
    {{-- //validation messages --}}

         <div class=" row form-group">
            
            <div class=" col-md-12 text-right">
                <button class=" btn btn-default border-blue" data-toggle="modal" data-target="#new_customer">Add New Space Category <i class=" icon-add"></i></button>
            </div>
         </div>

         {{-- user registration here --}}

         <div>

        {{-- New customer modal --}}
        {{-- loaded data from database --}}
        @foreach ($categories as $data)
        @endforeach
        {{-- /loaded data from database --}}
            <div id="new_customer" class="modal fade" aria-modal="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h4 class="modal-title text-bold text-center">OFF FAIR SPACE CATEGORIES</h5>
                            </div>
                            <div class="modal-body">
                                <div class="row form-group">
                                    <div class=" form-group">
                                        <code>Note!</code> fields with " <i class=" text-danger">*</i> " mark are required
                                    </div>
                                    <form action="{{ route('space_categories.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                          <div class=" form-group col-md-12">
                                              <label>Space Category Name<i class=" text-danger">*</i></label>
                                              <input type="text" name="space_category_name" class=" form-control" placeholder="Category Name" value="{{ old('category_name') }}" required>
                                           </div>
                                           <div class=" form-group col-md-12">
                                             <label>Space Category Cost<i class=" text-danger">*</i></label>
                                             <input type="number" min="0" name="category_cost" class=" form-control" placeholder="Category Cost" value="{{ old('category_cost') ?? 0 }}">
                                          </div>
                                          <div class=" form-group col-md-12">
                                             <label>Category Descriptions</label>
                                             <textarea name="category_descriptions" placeholder="Category Descriptions" class=" form-control"></textarea>
                                          </div>
                                          <div class=" form-group col-md-12">
                                            <label>Space Size / Ability</label>
                                            <input type="text" name="populationLimit" class=" form-control">
                                         </div>
                                          <div class=" form-group col-md-12">
                                            <label>Space Category Poster</label>
                                            <input type="file" name="categoryPoster" class=" form-control">
                                         </div>
                                          <div class=" modal-footer">
                                            <div class="  form-group text-right col-md-12">
                                                <button class=" btn btn-warning" data-dismiss="modal"> Close</button>
                                                <button type="submit" class="btn btn-primary border-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Register Off-Fair Customer</span></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {{-- // new customer modal --}}

            {{-- table for available customers in database--}}
            <div class=" form-group row">
                <div class=" col-md-12">
                    <table class=" table table-bordered table-striped datatable-save-state">
                        <thead>
                            <tr>
                                <th class=" text-bold">S/N</th>
                                <th class=" text-bold">Category Name</th>
                                <th class=" text-bold">Category Cost</th>
                                <th class=" text-bold">Category Descriptions</th>
                                <th class=" text-bold">Category Poster</th>
                                <th class=" text-bold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1; @endphp
                            @foreach ($categories as $item)
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $item->category_name }}</td>
                                <td>{{ number_format($item->space_category_cost,2) }}</td>
                                <td>{{ $item->category_descriptions }}</td>
                                <td class=" text-uppercase">
                                    <img src="{{ $item->categoryPoster }}" height="50" width="100">
                                </td>

                                {{-- update customer modal --}}
                                <div id="update_category{{ $item->space_category_id }}" class="modal fade" aria-modal="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">
                                                <h4 class="modal-title text-bold text-center">UPDATE SPACE CATEGORY DETAILS</h5>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row form-group">
                                                        <div class=" form-group">
                                                            <code>Note!</code> fields with " <i class=" text-danger">*</i> " mark are required
                                                        </div>
                                                        <form action="{{ route('space_categories.update',['space_category'=>$item->space_category_id]) }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PATCH')
                                                              <div class=" form-group col-md-12">
                                                                  <label>Space Category Name<i class=" text-danger">*</i></label>
                                                                  <input type="text" name="space_category_name" class=" form-control" placeholder="Category Name" value="{{ $item->category_name }}" required>
                                                               </div>
                                                               <div class=" form-group col-md-12">
                                                                 <label>Space Category Cost<i class=" text-danger">*</i></label>
                                                                 <input type="number" min="0" name="category_cost" class=" form-control" placeholder="Category Cost" value="{{ $item->category_cost ?? 0 }}">
                                                              </div>
                                                              <div class=" form-group col-md-12">
                                                                 <label>Category Descriptions</label>
                                                                 <textarea name="category_descriptions" placeholder="Category Descriptions" class=" form-control">{{ $item->category_descriptions }}</textarea>
                                                              </div>
                                                              
                                                             <div class=" form-group col-md-12">
                                                                <label>Space Size / Ability</label>
                                                                <input type="text" name="populationLimit" class=" form-control" value="{{ $item->populationLimit }}">
                                                             </div>
                                                              <div class=" form-group col-md-12">
                                                                <label>Space Category Poster</label>
                                                                <input type="file" name="categoryPoster" class=" form-control">
                                                             </div>

                                                              <div class=" modal-footer">
                                                                <div class="  form-group text-right col-md-12">
                                                                    <button class=" btn btn-warning" data-dismiss="modal"> Close Modal</button>
                                                                    <button type="submit" class="btn btn-primary border-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Register Off-Fair Customer</span></button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- /new customer modal --}}

                                    {{-- delete Customer Modal --}}
                                    <div id="delete_category{{ $item->space_category_id }}" class="modal fade" aria-modal="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">
                                                    <h4 class="modal-title text-bold text-center">ARE YOU SURE YOU WANT TO DELETE CUSTOMER DETAILS ? </h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="p-20">Note: This action will delete All <b>{{ $item->category_name }}'S</b> Information Permanently.</p>
                                                        <form action="{{ route('space_categories.destroy',['space_category'=>$item->space_category_id])}}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('DELETE')
                                                        </div>
                                                        <div class=" modal-footer">
                                                            <div class="  form-group text-right">
                                                                <button class=" btn btn-warning" data-dismiss="modal"> Close Modal</button>
                                                                <button type="submit" class="btn btn-danger btn-ladda btn-ladda-spinner" ><span class="ladda-label">Delete Category</span></button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    {{-- //delete customer modal --}}

                                <td class="text-center">
                                    <ul class="icons-list">
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                                <i class="icon-menu4"></i>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                               @can('Update Space Category')
                                            <li>
                                                <a data-toggle="modal" data-target="#update_category{{ $item->space_category_id }}" class=" text-primary"><i class=" icon-pencil7"></i> Edit Category Details</a>
                                            </li>
                                               @endcan

                                               @can('Delete Space Category')
                                                <li>
                                                        <a  data-toggle="modal" data-target="#delete_category{{ $item->space_category_id }}" class=" text-danger"><i class="icon-bin"></i> Delete Category Details</a>
                                                </li>
                                                @endcan
                                            </ul>
                                        </li>
                                    </ul>
                                </td>
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
</div>
{{-- //user registration  --}}

<div id="myData">

</div>
<script>

</script>
</x-app-layout>
