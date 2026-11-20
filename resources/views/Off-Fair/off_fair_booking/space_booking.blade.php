<x-app-layout>
    <title>{{ env('APP_NAME') }} | Space Booking</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('off-fair-space-booking.index') }}"><i class="icon-home2 position-left"></i>BOOKED
                        SPACES</a></li>
                <li class="active">Make Space Booking</li>
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
            <div class=" panel-body ">
                <div class=" form-group col-md-12 bg-primary">
                    <h6 class=" text-bold text-center">LIST OF SPACES FOR BOOKING</h6>
                </div>

                <div class="category-content">
                    <div class="row row-condensed col-md-12">
                        @php
                            $i = 0;
                        @endphp
                        @foreach ($space_categories as $item)
                            {{-- Booking --}}
                            <div class="col-md-4 form-group zoom">
                                <form action="{{ route('off-fair-space-booking.index') }}" method="POST">
                                    @csrf
                                    @method('GET')
                                    <input type="hidden" name="space_category" value="{{ $item->space_category_id }}">

                                    <a href="#">
                                        <button type="submit"
                                            class="btn btn-block btn-float btn-float-lg  text-bold border-primary @if (session('space_category') == $item->space_category_id) bg-primary @endif">
                                            <i class=" icon icon-office"></i> <span
                                                class=" text-uppercase">{{ $item->category_name }}</span>
                                            <hr>
                                            <div style="text-align: left; font-size:90%">
                                                <p class=" text-capitalize">{{ $item->category_descriptions }}</p>
                                            </div>
                                        </button>
                                    </a>
                                </form>
                            </div>
                            {{-- /Booking --}}
                            @php
                                $i++;
                            @endphp
                        @endforeach
                        @if ($i == 0)
                            <div class=" alert alert-danger">
                                <h5>No space registered under this Event!</h5>
                            </div>
                        @endif
                    </div>
                </div>

                <div class=" form-group col-md-12">
                    <table class=" table table-bordered datatable-button-html5-columns">
                        <thead>
                            <tr>
                                <th>Space Name</th>
                                <th>Space Size</th>
                                <th>Space Cost</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($spaces as $value)
                            <tr>
                                <td>{{ $value->space_name }} <i class=" text-muted"> {{ $value->space_descriptions }} </i></td>
                                <td>{{ number_format($value->space_size ,1)}} MSQ / Unit</td>
                                <td>{{ number_format($value->space_cost ,1)}} TZS</td>
                                <td class=" text-center">
                                    <a href="{{ route('off-fair-space-booking.show',['off_fair_space_booking'=>$value->space_id]) }}" class=" btn btn-primary btn-rounded">Book Space <i class=" icon icon-arrow-right13"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
        {{-- //user registration  --}}
</x-app-layout>
