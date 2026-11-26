

<x-app-layout>
    <title>{{ env('APP_NAME') }} | Products</title>
    <div class="page-header">
      <div class="page-header-content">
       @include('layouts.heading-elements')
      </div>
      <div class="breadcrumb-line breadcrumb-line-component">
       <ul class="breadcrumb">
           <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i>EXHIBITOR PRODUCTS REPORTS</a></li>
           <li class="active">View Exhibitor products Reports</li>
       </ul>
       <ul class="breadcrumb-elements">
           @include('layouts.help')
       </ul>
   </div>
</div>

<div class=" content">
   <div class="panel panel-white">
       <div class="panel-heading">
           <h4 class="panel-title text-bold text-uppercase text-center">EXHIBITOR PRODUCTS REPORTS FOR THE <b class=" text-warning"><q>{{ $event_name }}</q></b></h6>
           <div class="heading-elements">
               <ul class="icons-list">
                   <li><a data-action="collapse"></a></li>
                   <li><a data-action="reload"></a></li>
                   <li><a data-action="close"></a></li>
               </ul>
           </div>
       </div>
       <div class=" panel-body">


        <div class=" row col-md-12">

            <form action="{{ route('exhibition_products.index') }}" method="POST">
                @csrf
                @method('GET')
                <div class=" row">
                <div class=" form-group col-md-10">
                 <select name="event_name" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select menu status">

                    <option value=""> - - - - Select Other Event - - - -</option>
                    @foreach ($events as $item)
                        <option value="{{ $item->event_id }}">{{ $item->event_name }}</option>
                        @endforeach
                 </select>
                </div>
                 <span class="text-danger"> @error('event_name'){{ $message }}@enderror</span>


                 <div class=" form-group col-md-2 text-right">
                    <button type="submit" class=" btn btn-primary border-blue btn-ladda btn-ladda-spinner">Load New Data <i class=" icon-database-refresh"></i></button>
                </div>
                </div>
            </form>

        </div>


<!-- Main charts -->
<div class="row">
    <div class="col-lg-7">

        <div class="panel panel-flat">
            <div class="panel-heading">
                <h6 class="panel-title text-bold text-uppercase text-center" style="border-bottom: 1px dotted blue;">EXHIBITED PRODUCTS as PER  {{ $event_name }}</h6>
            </div>

            <div class="panel-body">
               <table class=" datatable-button-html5-columns table table-bordered table-striped">
                <thead>
                    <tr class=" bg-teal-400">
                        <th class=" text-bold text-center">COMPANY NAME</th>
                        <th class=" text-bold text-center">LIST OF PRODUCTS</th>
                        <th class=" text-bold text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>

                    @php $total=0; @endphp
                @foreach($products as $value)
                <tr>
                    <td>{{ $value->companyName }}</td>
                    <td>{{ $value->product_list }}</td>

                    <td class="text-center">
                        <ul class="icons-list">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="icon-menu4"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                   @can('UPDATE_PRODUCT')
                                <li>
                                    <a  class=" text-primary"><i class=" icon-pencil7"></i> Edit Products</a>
                                </li>
                                   @endcan


                                    @can('DELETE_PRODUCT')
                                    <li>
                                        <a  data-toggle="modal" data-target="#delete_product{{ $value->exhibition_product_id}}" class=" text-danger"><i class="icon-bin"></i> Delete Products</a>
                                    </li>
                                    @endcan
                                </ul>
                            </li>
                        </ul>
                    </td>


                    {{-- delete products --}}
                    <div id="delete_product{{ $value->exhibition_product_id }}" class="modal fade" aria-modal="true">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h4 class="modal-title text-bold text-center text-uppercase">DELETE <q>{{ $value->companyName }}</q> Products</h5>
                                    </div>
                                    <div class=" modal-body">
                                        <form action="{{ route('exhibition_products.destroy',['exhibition_product'=>$value->exhibition_product_id]) }}" method="post" id="company">
                                            @csrf
                                            @method('DELETE')
                                        </div>
                                        <div class=" modal-footer">
                                            <div class=" text-right">
                                            <button class=" btn btn-default border-warning text-warning" data-dismiss="modal"> Close</button>
                                            <button type="submit" class="btn btn-warning  btn-ladda btn-ladda-spinner" ><span class="ladda-label">Delete Product</span></button>
                                            </div>
                                        </div>
                                    </form>
                            </div>
                        </div>
                    </div>

                    {{-- end --}}

                </tr>
               @endforeach
            {{-- <b class=" text-warning">Total Exhibitors: {{ $total }}</b> --}}
                </tbody>
               </table>
            </div>
        </div>

        {{-- another 7-col-md here --}}

    </div>



    <div class="col-md-5">

        col-5 info
     </div>

</div>
<!-- /main charts -->





       </div>
    </div>
</div>
</x-app-layout>







