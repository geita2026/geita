<x-app-layout>
    <title>{{ env('APP_NAME') }} | Products</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="#"><i class="icon-home2 position-left text-uppercase"></i>Products</a></li>
            <li class="active">View All products</li>
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

     @if ($errors->any())
     @foreach ($errors->all() as $error)
     <span class=" text-danger"><li>{{ $error }}</li></span>
     @endforeach
     @endif
        <div class=" form-group text-right">
        <button type="submit" class=" btn btn-success" data-toggle="modal" data-target="#add_role">Add Product <i class=" icon-add-to-list"></i></button>
        </div>
        <table class="table datatable-button-html5-columns table-striped table-bordered  table-responsive">
            <thead>
                <tr>
                    <th class=" text-bold text-center">#</th>
                    <th class="text-bold text-center">PRODUCT NAME</th>
                    <th class="text-bold text-center text-uppercase">PRODUCT DESCRIPTIONS</th>
                    <th class=" text-bold text-center">CREATED ON</th>
                    <th class="text-bold text-center">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @php $i=1; @endphp
                @foreach ($products as $item)
                <tr>
                    <td>{{ $i }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td class=" text-right">{{ $item->descriptions }}</td>
                    <td>{{ $item->product_created_at }}</td>

                    <td class="text-center">
                        <ul class="icons-list">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="icon-menu4"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a  data-toggle="modal" data-target="#edit{{ $item->product_id }}" class=" text-primary"><i class="icon-pencil7"></i> Edit Product</a>
                                    </li>

                                    @can('Delete Product')
                                    <li>
                                        <a  data-toggle="modal" data-target="#delete{{ $item->product_id }}" class="text-danger"> <i class=" icon-bin"></i> Delete Product</a>
                                    </li>
                                    @endcan
                                </ul>
                            </li>
                        </ul>
                    </td>

                    {{-- edit role modal --}}
                    <div id="edit{{ $item->product_id }}" class="modal fade" aria-modal="true" data-backdrop="static">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h5 class="modal-title">Update <q>{{ $item->product_name }}</q> DETAILS.</h5>
                                </div>
                                <form action="{{ route('products.update',['product'=>$item->product_id]) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class=" form-group col-md-12">
                                              <label>Product Name<i class=" text-danger">*</i></label>
                                                 <input type="text" name="product_name" class=" form-control" placeholder=" Product Name" value="{{ $item->product_name }}" required>
                                           </div>
                                            <div class=" form-group col-md-12">
                                               <label>Products Description</label>
                                                  <textarea class="form-control" name="descriptions" placeholder="Product description">{{ $item->descriptions }}</textarea>
                                            </div>
                                         </div>
                                    </div>

                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-default border-warning" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span class="ladda-label">Update Product</span></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- /edit role modal -->
                    {{-- delete modal --}}
                             <div id="delete{{ $item->product_id }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Are you sure you whant to delete <q>{{ $item->product_name }}</q> Product ?</h5>
                                        </div>

                                        <div class="modal-body">
                                            <form action="{{ route('products.destroy', ['product' => $item->product_id]) }}" method="POST">
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
                       @php $i++; @endphp
                        @endforeach
                    </tbody>

                </table>

            </div>
            {{--  end here  --}}

        </div>
    </div>
<!-- /dashboard content -->

{{-- add role modal --}}
<form action="{{ route('products.store') }}" method="POST">
    @csrf
<div id="add_role" class="modal fade" aria-modal="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-blue" >
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title text-center text-bold">ADD PRODUCT DETAILS</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class=" form-group col-md-12">
                      <label>Product Name<i class=" text-danger">*</i></label>
                         <input type="text" name="product_name" class=" form-control" placeholder=" Product Name" value="{{ old('product_name')}}" required>
                   </div>
                    <div class=" form-group col-md-12">
                       <label>Products Description</label>
                          <textarea class="form-control" name="descriptions" placeholder="Product description">{{ old('descriptions') }}</textarea>
                    </div>
                 </div>
            </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-default border-warning" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span class="ladda-label">Register Product</span></button>
            </div>
        </div>
    </div>
</div>
</form>
</x-app-layout>
