<div class=" form-group col-md-12 p-10" style="border:1px solid rgb(199, 197, 197);">
    @if (Session::get('success'))
        <div class=" alert alert-success">
            {{ Session::get('success') }}
        </div>
    @elseif (Session::get('fail'))
        <div class=" alert alert-danger">
            {{ Session::get('fail') }}
        </div>
    @endif

    @php
        if (session('ticketRequest')) {
            $id = session('ticketRequest');
        } else {
            $id = date('siydmysyidmsyidms');
        }
    @endphp
    <div class=" col-md-12">
        <form action="{{ route('online_tickets.edit', ['online_ticket' => $id]) }}" method="POST">
            @csrf
            @method('GET')
          
                <input type="hidden" name="source" value="1">
                <div class=" row form-group p-10">
                    <div class=" row">
                        <div class="form-group ">
                            <label>Date of visit<i class=" text-danger">*</i>(Date You Plan to visit sabasaba)</label>
                            <select name="visitDate" class="select form-control" id="dateValues" required>
                                <option value="">- - - Select Date of Visit - - - </option>
                                <option value="28-06-2024">{{ date('l, M d, Y', strtotime('28-06-2024')) }}</option>
                                <option value="29-06-2024">{{ date('l, M d, Y', strtotime('29-06-2024')) }}</option>
                                <option value="30-06-2024">{{ date('l, M d, Y', strtotime('30-06-2024')) }}</option>
                                <option value="01-07-2024">{{ date('l, M d, Y', strtotime('01-07-2024')) }}</option>
                                <option value="02-07-2024">{{ date('l, M d, Y', strtotime('02-07-2024')) }}</option>
                                <option value="03-07-2024">{{ date('l, M d, Y', strtotime('03-07-2024')) }}</option>
                                <option value="04-07-2024">{{ date('l, M d, Y', strtotime('04-07-2024')) }}</option>
                                <option value="05-07-2024">{{ date('l, M d, Y', strtotime('05-07-2024')) }}</option>
                                <option value="06-07-2024">{{ date('l, M d, Y', strtotime('06-07-2024')) }}</option>
                                <option value="07-07-2024">{{ date('l, M d, Y', strtotime('07-07-2024')) }} (SabaSaba Day)
                                </option>
                                <option value="08-06-2024">{{ date('l, M d, Y', strtotime('08-07-2024')) }}</option>
                                <option value="09-06-2024">{{ date('l, M d, Y', strtotime('09-07-2024')) }}</option>
                                <option value="10-07-2024">{{ date('l, M d, Y', strtotime('10-07-2024')) }}</option>
                                <option value="11-07-2024">{{ date('l, M d, Y', strtotime('11-07-2024')) }}</option>
                                <option value="12-07-2024">{{ date('l, M d, Y', strtotime('12-07-2024')) }}</option>
                                <option value="13-07-2024">{{ date('l, M d, Y', strtotime('13-07-2024')) }}</option>
                            </select>
                            <span class=" text-danger">@error('visitDate') {{ $message }} @enderror</span>
                        </div>
                        <div class=" form-group " id="NormalDay">
                            <label>Select Ticket Type<i class=" text-danger">*</i></label>
                            <select name="ticketType" class=" select" id="ticketSelections">
                                <option value=""> - - - Select Ticket Type - - - </option>
                                @foreach ($packages as $item)
                                    <option value="{{ $item->vip_package_id }}">{{ $item->package_discriptions }}
                                        ({{ number_format($item->package_cost) }} {{ $item->currency }})
                                    </option>
                                @endforeach
                            </select>
                            <span class=" text-danger">@error('ticketType') {{ $message }} @enderror</span>
                        </div>
    
                        <div class=" form-group " id="SabaSabaDay">
                            <label>Select Ticket Type (This is sabasaba Day Ticket Cost 4,000)<i
                                    class=" text-danger">*</i></label>
                            <select name="sabaSabaTicket" class=" select" id="ticketSelections">
                                <option value=""> - - - Select Ticket - - -</option>
                                @foreach ($sabasabaTickets as $item)
                                    <option value="{{ $item->vip_package_id }}">{{ $item->package_discriptions }}
                                        ({{ number_format($item->package_cost) }} {{ $item->currency }})
                                    </option>
                                @endforeach
                            </select>
                            <span class=" text-danger">@error('sabaSabaTicket') {{ $message }} @enderror</span>
                        </div>
    
                        <div class=" form-group ">
                            <label>Quantity<i class=" text-danger">*</i></label>
                            <input type="number" name="ticketQuantity" required class=" form-control" value="1"
                                min="1" value="{{ old('ticketQuantity') }}">
                                <span class=" text-danger">@error('ticketQuantity') {{ $message }} @enderror</span>
                        </div>
                        <div class=" form-group " id="plateNumber">
                            <label>Car Plate Number<i class=" text-danger">*</i></label>
                            <input type="text" name="plateNumber" class=" form-control" placeholder="Car Plate Number" value="{{ old('plateNumber') }}">
                            <span class=" text-danger">@error('plateNumber') {{ $message }} @enderror</span>
                        </div>
    
                        @if (!session('item'))
                            <div class=" form-group ">
                                <label>Full Name<i class=" text-danger">*</i></label>
                                <input type="text" name="fullName" class="form-control"  value="{{ old('fullName') }}" placeholder="Full Name" required>
                                <span class=" text-danger">@error('fullName') {{ $message }} @enderror</span>
                            </div>
    
                            {{-- <div class=" form-group ">
                                <label>Email Address (Optional) <a class="icon-question3 text-primary" data-popup="popover"
                                        title="Why we need email ?" data-trigger="hover"
                                        data-content="May be used as an altenative for sending you control number and your ticket after payments!"></a></label>
                                <input type="text" name="emailAddress" class="form-control"
                                    placeholder="Email Adress">
                            </div> --}}
    
                            <div class=" form-group ">
                                <label class="">Phone Number<i class=" text-danger">*</i> <a
                                        class="icon-question3 text-primary" data-popup="popover"
                                        title="Why we need phone Number ?" data-trigger="hover"
                                        data-content="This is mandatory fiel and will be the primary contact information to be used  for sending you control number and your ticket after payments!"></a></label>
                                <input type="text" name="phoneNumber" class="form-control" value="{{ old('phoneNumber') ?? '255' }}"
                                    placeholder="Phone Number" required>
                                    <span class=" text-danger">@error('fullName') {{ $fullName }} @enderror</span>
                            </div>
                        @endif
    
                    </div>
                </div>
                <div class=" text-right">
                    <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner">
                        @if (session('item'))
                           Add More Ticket(s)
                            <i class=" icon-cart-add"></i>
                        @else
                            Add to Cart <i class=" icon-cart-add"></i>
                        @endif
                    </button>
                </div>
            
        </form>
    </div>

    {{-- <div class=" form-group">
        <p style="font-size: 2vmin" class=" "><b>NOTE!</b> Ensure your Phone Number and the email entered you have access to it
            because all communication will be made through your registered Phone Number and Email, including your ticket after receiving your
            payments. </p>
    </div> --}}


    {{-- <div class=" alert alert-danger">
        Booking process will be open on 25<sup>th</sup> June 2024. Three day before the event, keep in touch.
    </div> --}}

</div>
