<x-app-layout>
    <title>{{ env('APP_NAME') }} | Ticket Bookings</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="#"><i class="icon-home2 position-left text-uppercase"></i>TICKET BOOKINGS</a></li>
            <li class="active">View All ticket Bookings</li>
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
     <div class=" form-group col-md-12 table-header">
        <h4 class="text-center text-bold">DATABASE MANAGEMENT </h4>
    </div>

   <div class=" row">

    @if (@session('fail'))
    <div class="alert alert-danger alert-bordered">
      <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
     {{ session('fail') }}
  </div>
    @endif

    <form action="{{ route('db_management.update',['db_management']) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class=" col-md-12 form-group">
            <label>SQL QUERY<i class=" text-danger">*</i></label>
            <textarea name="query_statement" class=" form-control" placeholder="Write sql query here . . ." >{{ $query }}</textarea>
            <span class=" text-danger">@error('query_statement') {{ $message }} @enderror</span>
            <input type="hidden" name="search" value="1">

        </div>
        <div class=" col-md-12 text-right form-group">
            <button class=" btn btn-primary">Excute Query</button>
        </div>
    </form>
   </div>

   <div class=" row">
<table border="1" cellspacing="0" cellpadding="3" style="width: 100%">
    <thead>
        <tr>
            <th colspan="100" class=" text-bold text-success">QUERY:: {{ $query }}</th>
        </tr>
        @php

           $columns = array();
            if(count($result) > 0){
                $columns = array_keys((Array)$result[0]);
                foreach ($columns as $key1 => $column_name) {
                    echo '<th>'.$column_name.'</th>';
                }
            }
        @endphp
    </thead>
    <tbody>
        @php
         if(count($result) > 0){
 foreach ($result as $ky=>$row){
echo '<tr>';
                foreach ($columns as $key1 => $column_name) {
                    echo '<td>'.$row->{$column_name}.'</td>';
                }
echo '</tr>';
            }
        }else{
            echo '<tr class=" alert alert-danger"><td class="p-20">No data found !!</td></tr>';

        }
        @endphp

    </tbody>
</table>
   </div>


</div>
            {{--  end here  --}}
</div>
</div>
<!-- /dashboard content -->
</x-app-layout>
