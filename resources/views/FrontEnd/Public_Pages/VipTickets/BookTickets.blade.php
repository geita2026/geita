
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{ env('APP_NAME') }} | VIP Tickets</title>
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/gif" sizes="16x16">
    @include('WelcomePageLayouts.welcomeCss')

</head>

<body>
	<!-- Page container -->
	<div class="page-container content">
		<!-- Page content -->
		<div class="page-content">

			<!-- Main content -->
			<div class="content-wrapper">
				<!-- Search field -->

            @include('WelcomePageLayouts.welcomeHeaderMenu')

            <div class=" col-md-12 form-group">
				<div class="panel">
                    <div class=" panel-body">

                        @foreach ($packages as $item)
                        <div class=" virtual-content col-md-12 form-group" style="color: {{ $item->package_color }}">
                           <div class=" row form-group col-md-12">
                            <div class="col-md-6">
                                <div class=" panel">
                                    <div class=" panel-heading text-bold text-uppercase" style=" border-bottom: 1px solid rgb(206, 203, 203);background-color: {{ $item->package_color }}">
                                            <h3 class=" text-center"><b>{{ $item->package_name }}  ({{ number_format($item->package_cost) }} {{ $item->currency }}) BENEFITS</b></h3>

                                    </div>
                                    <div class=" panel-body">
                                        @foreach ($benefits as $value)
                                        <div class=" "> <i class="icon-check2 text-bold" style="color: {{ $item->package_color }}"></i> {{ $value->benefit_name }}</div><br>
                                        @endforeach
                                    </div>
                                    <div class=" panel-footer">
                                        <div class=" text-center text-bold text-uppercase"><q style="color: {{ $item->package_color }}">Tanzania: Your Best Destination for Business and Investment</q></div>
                                    </div>
                                </div>
                            </div>
                            {{-- FORM --}}
                            <div class="col-md-6">
                                <div class=" panel">
                                    <div class=" panel-heading " style=" border-bottom: 1px solid rgb(206, 203, 203);background-color: {{ $item->package_color }}">
                                          <h3 class="text-bold text-uppercase text-center">BOOK {{ $item->package_name }}</h3>
                                          @if (@session('success'))
                                          <div class="alert alert-success alert-bordered">
                                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                                            <span class="text-semibold">Hellow {{ $name ?? '' }}</span> {{ session('success') }}
                                        </div>
                                          @endif
                                    </div>
                                    <div class=" panel-body">
                                        <p class=" text-muted">
                                            <i class=" icon-info22 text-primary"></i> After submiting the bellow information we will send to you, control number via phone number and email submitted, where you can make payments for the ticket booked.<br>
                                            <i class=" icon-ticket text-warning-800"></i> After making your payments you will receive, ticket through same email and phone number submited.
                                           </p>
                                        <form action="{{ route('online_tickets.update',['online_ticket'=>$item->vip_package_id]) }}" id="BookTicketForm" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class=" row form-group">
                                                <div class=" form-group col-md-6">
                                                    <label>Full name<i class=" text-danger">*</i></label>
                                                    <input type="text" name="customer_name" class=" form-control" id="name" placeholder="Full name" value="{{ old('customer_name') }}">
                                                    <span class=" text-danger">@error('customer_name') {{ $message }} @enderror</span>
                                                </div>
                                                <div class=" form-group col-md-6">
                                                    <label>Email Address<i class=" text-danger">*</i></label>
                                                    <input type="email" name="email_address" class=" form-control" id="email" placeholder="Email Address" value="{{ old('email_address') }}">
                                                    <span class=" text-danger">@error('email_address') {{ $message }} @enderror</span>
                                                </div>
                                                <div class=" form-group col-md-6">
                                                    <label>Phone Number<i class=" text-danger">*</i></label>
                                                    <input type="phone" name="phone_number" class=" form-control" id="phone" placeholder="255 xxx xxx xxx" value="{{ old('phone_number') }}">
                                                    <span class=" text-danger">@error('phone_number') {{ $message }} @enderror</span>
                                                </div>
                                                <div class=" form-group col-md-6">
                                                    <label>.</label>
                                                    <button class=" btn btn-block btn-primary">Request Control Number</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class=" panel-footer">
                                        <div class=" text-center text-bold text-uppercase"><q style="color: {{ $item->package_color }}">Tanzania: your Best Destination for Business and Investment</q></div>
                                    </div>
                                </div>
                            </div>
                           </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>



            <div class="panel col-md-12 form-group">
                <div class=" panel-heading text-bold">
                    <h4 class=" text-primary text-center text-bold text-uppercase">Did'nt get your  Ticket ?</h4>

                </div>

                <div class=" panel-body">
                    <form action="{{ route('online_tickets.store') }}" method="POST">
                        @csrf
                        <div class="form-group col-md-12">
                            <label class=" control-label col-lg-4 text-muted">Request ticket if you didn't get it after payments</label>
                            <div class="col-lg-8">
                                <div class="input-group">
                                    <input type="text" name="email_or_phone" class="form-control" placeholder="Enter Phone Number or Email Address">
                                    <span class="input-group-btn">
                                        <button type="submit" class="btn btn-primary" type="button">Request Ticket</button>
                                    </span>
                                </div>
                                <span class=" text-danger">@error('email_or_phone') {{ $message }} @enderror</span>
                            </div>
                        </div>
                    </form>


                    {{-- <li style="color: {{ $item->package_color }};font-size:1.3vmax" class=" icon-phone2"> +255 733 002 014</li> --}}

                    <br><br><br>
                    @if (session('fail'))
                    <div class=" form-group col-md-12">
                        <div class=" alert alert-warning">
                            <b>{{ session('fail') }}</b>
                        </div>
                    </div>

                    @endif


                </div>
            </div>




        </div>
		<!-- /page content -->
        @include('layouts.footer')

	</div>
	<!-- /page container -->

</body>
</html>
@include('FrontEnd.Public_Pages.Layouts.scripts')
{{--  --}}

