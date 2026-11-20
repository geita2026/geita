<x-app-layout>
    <title>{{ env('APP_NAME') }} | Expo2025</title>
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i>Home</a></li>
                <li class="active">List of registered participants</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>

    <div class=" content">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h4 class="panel-title text-bold text-uppercase text-center">LIST OF REGISTERED PARTCIPANTS FOR EXPO
                    2025</h6>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
            </div>
            <div class=" panel-body">

                <!-- Main charts -->
                <div class="row">
                    <div class="col-lg-12">

                        <div class="panel panel-flat">
                            <div class="panel-body">
                                <div class=" form-group text-warning">
                                    <h3>
                                        <b>Total Registered: </b> {{ number_format($registered) }} ||
                                        <b>Total Confirmed: </b> {{ number_format($confirmed) }}
                                    </h3>
                                </div>
                                <div class=" form-group">
                                    <form action="{{ route('expo_dashboard') }}" method="POST">
                                        @csrf
                                        @method('GET')
                                        <div class=" row">
                                            
                                            <div class=" col-md-8">
                                                <select name="status" class=" form-control" required>
                                                    <option value="">- - - Select Status - - - </option>
                                                    <option value="APPROVED"> CONFIRMED FOR PARTICIPATION</option>
                                                    <option value="UNAPPROVED"> UNCONFIRMED FOR PARTICIPATION</option>
                                                </select>
                                            </div>
                                            <div class=" col-md-4">
                                                <button class=" btn btn-primary">Fetch external registered Companies <i
                                                        class=" icon icon-search4"></i></button>
                                            </div>

                                        </div>
                                    </form>
                                </div>
                                <div class=" form-group">
                                    <table class=" datatable-button-html5-columns table table-bordered table-striped">
                                        <thead>
                                            <tr class=" bg-teal-400">
                                                <th>#</th>
                                                <th class=" text-bold text-center">COMPANY NAME</th>
                                                <th class=" text-bold text-center">Personal Name</th>
                                                <th class=" text-bold text-center">Personal Phone & Email</th>
                                                <th class=" text-bold text-center">Passport Number</th>
                                             
                                                <th class=" text-bold text-center">Program Of Interest</th>
                                                <th class=" text-bold text-center">Targeted companies/sector</th>
                                                {{-- <th class="text-bold text-center">Date Of Visit </th> --}}
                                                <th class=" text-bold text-center">Sex</th>
                                                <th class="text-bold text-center">Country</th>
                                                <th class=" text-bold text-center">Preview Details</th>
                                                <th class=" text-bold text-center">Confirm Participation</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $total=0; @endphp
                                            @foreach ($expoData as $key => $value)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $value->companyName }}</td>
                                                    <td>{{ $value->firstName }} -
                                                        {{ $value->lastName }} </td>
                                                    <td>{{ $value->phoneNumber }} - {{ $value->emailAddress }}</td>
                                                    <td>{{ $value->passportNumber }} </td>
                                                    <td>{{ $value->programOfInterest }}</td>
                                                    <td>{{ $value->anyOtherDescription }} </td>
                    
                                                    <td>{{ $value->sex }}</td>
                                                    
                                                    <td>{{ $value->country }}</td>

                                                    <td>
                                                        <a href="{{ route('preview_data', ['participantId' => $value->participantId]) }}"
                                                            class=" btn btn-primary">Preview <i
                                                                class=" icon icon-arrow-right13"></i></a>
                                                    </td>

                                                    <td>
                                                        @if ($value->participantStatus == 'APPROVED')
                                                            <a href="{{ route('expo.confirm_participation', ['participationId' => $value->participantId]) }}" class=" btn btn-success">Confirmed
                                                                Participation <i class=" icon icon-check2"></i></a>
                                                        @else
                                                            <a href="{{ route('expo.confirm_participation', ['participationId' => $value->participantId]) }}"
                                                                class=" btn btn-primary">Confirm Participation <i
                                                                    class=" icon icon-check2"></i></a>
                                                        @endif
                                                    </td>

                                                </tr>
                                            @endforeach
                                            {{-- <b class=" text-warning">Total Exhibitors: {{ $total }}</b> --}}
                                        </tbody>
                                    </table>
                                </div>
                                <div class=" form-group text-right">
                                    {{-- {{ $expoData->links() }} --}}
                                </div>


                            </div>
                        </div>

                        {{-- another 7-col-md here --}}

                    </div>
                </div>
                <!-- /main charts -->
            </div>
        </div>
    </div>
</x-app-layout>
