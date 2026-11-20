
<x-app-layout>
    <title>{{ env('APP_NAME') }} | Booking</title>
    <style>
        .zoom{
            padding: 2%;
            transition: transform .5s;
            font-size: 20px;
        }
        .zoom:hover {
          -ms-transform: scale(1.2); /* IE 9 */
          -webkit-transform: scale(1.2); /* Safari 3-8 */
          transform: scale(1.2);
        }
        </style>


    {{-- page header --}}
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>

    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('booking.index') }}"><i class="icon-stack-check position-left"></i>BOOKING</a></li>
            <li class="active">View space available for booking</li>
        </ul>
        <ul class="breadcrumb-elements">
            @include('layouts.help')
        </ul>
    </div>

 </div>
 {{--  <!-- /page header -->  --}}

 {{--  <!-- Content area -->  --}}
 <div class="content">
     <div class="panel panel-white">
         <div class="panel-heading">
             <h4 class="panel-title text-bold text-uppercase">

               SPACE CATEGORIES
                 </h4>
             <div class="heading-elements">
                 <ul class="icons-list">
                     <li><a data-action="collapse"></a></li>
                     <li><a data-action="reload"></a></li>
                     <li><a data-action="close"></a></li>
                 </ul>
             </div>
         </div>

         <div class=" panel-body">

             <div class=" row">
                <div class=" col-md-2">
                    <h3>
                        <a href="{{ route('space-categories.index') }}" class=" btn btn-default border-primary text-primary"> <i class=" icon-backward2"></i> Back</a>
                    </h3>
                </div>
                <div class=" col-md-10">
                 <h3 class=" text-uppercase text-center text-bold">The {{ $event_name }} SPACE CATEGORIES.</h3>
                </div>
             </div>



             {{--  //end error messages  --}}
            <div class="category-content">
                <div class="row row-condensed">
                    @foreach ($space_categories as $item)
                    <form action="{{ route('space-list.show',['space_list'=>$item->category_id]) }}" method="POST">
                        @csrf
                        @method('GET')
                    {{-- Booking --}}
                        <a>
                            <input type="hidden" name="event_id" value="{{ $event_id }}">
                            <div class="col-md-4 form-group zoom">
                                <button type="submit" class="btn btn-block btn-float btn-float-lg text-uppercase text-bold border-primary {{$item->background_color}}"> <i class="{{ $item->category_icon }}"></i> <span>{{ $item->category_name }}</span>
                                    <hr>
                                <div style="text-align: left">
                                <p>
                                    {{-- description here --}}
                                </p>
                                </div>
                                </button>
                            </div>
                        </a>
                    {{-- /Booking --}}
                    </form>
                    @endforeach
                </div>
            </div>


        </div>
  {{-- end booking logics --}}

 </div>
 <!-- /main content -->
 </div>
 </div>
 </x-app-layout>

