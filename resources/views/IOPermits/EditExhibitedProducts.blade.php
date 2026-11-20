<x-app-layout>
    <title>{{ env('APP_NAME') }} | Exhibited Products</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> HOME</a></li>
                <li class="active">Exhibited Products</li>
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
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class=" text-center">UPDATE EXHIBITED PRODUCTS</h3>
                            @foreach ($products as $pro)
                                <form action="{{ route('exhibited_product_changes') }}" method="POST">
                                    @csrf
                                    <div class=" col-md-12 form-group">

                                        <textarea class=" form-control" name="products">{{ $pro->product_list ?? 'Null' }}</textarea>
                                    </div>
                                    <input type="hidden" name="productId"
                                        value="{{ $pro->exhibition_product_id ?? 'Null' }}">
                                        <div class=" col-md-12 text-right">
                                          <a href="{{ route('iopermits.index') }}" class=" btn btn-default text-primary ml-20"><i class=" icon-arrow-left12"></i>Go to Applications</a>  <button type="submit" class=" btn btn-primary ml-20">Submit Changes<i class=" icon-arrow-right13"></i></button>
                                        </div>
                                </form>
                            @endforeach
                        </div>
                    </div>
            </div>
        </div>
    </div>
    <!-- /dashboard content -->
</x-app-layout>
