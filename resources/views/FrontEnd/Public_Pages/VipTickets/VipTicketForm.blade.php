<div class=" form-group ">
    <p style="font-size: 2.5vmin" class=" text-bold text-center text-primary">ONLINE VIP TICKET BOOKING FORM
    </p>
    @if (Session::get('success'))
        <div class=" alert alert-success">
            {{ Session::get('success') }}
        </div>
    @elseif (Session::get('fail'))
        <div class=" alert alert-danger">
            {{ Session::get('fail') }}
        </div>
    @endif

    <form action="{{ route('online_tickets.store') }}" method="POST">
        @csrf
        <div class=" registration-form">
            <div class=" row form-group">
                <input type="hidden" name="source" value="2">
                <div class=" form-group col-md-6">
                    <label>Full Name<i class=" text-danger">*</i></label>
                    <input type="text" name="fullName" class="form-control">
                </div>

                <div class=" form-group col-md-6">
                    <label>Email Address<i class=" text-danger">*</i></label>
                    <input type="text" name="emailAddress" class="form-control">
                </div>
                <div class=" form-group col-md-6">
                    <label class="">Phone Number<i class=" text-danger">*</i></label>
                    <input type="text" name="phoneNumber" class="form-control" value="255">
                </div>

                <div class="form-group col-md-6">
                    <label>Date of visit<i class=" text-danger">*</i>(Date You Plan your parking)</label>
                    <select name="visitDate" class=" form-control" id="dateValues" required>
                        <option value="">- - - Select Date of Visit - - - </option>
                        <option value="28-06-2024">{{ date('l, M d, Y',strtotime('28-06-2024')) }}</option>
                        <option value="29-06-2024">{{ date('l, M d, Y',strtotime('29-06-2024')) }}</option>
                        <option value="30-06-2024">{{ date('l, M d, Y',strtotime('30-06-2024')) }}</option>
                        <option value="01-07-2024">{{ date('l, M d, Y',strtotime('01-07-2024')) }}</option>
                        <option value="02-07-2024">{{ date('l, M d, Y',strtotime('02-07-2024')) }}</option>
                        <option value="03-07-2024">{{ date('l, M d, Y',strtotime('03-07-2024')) }}</option>
                        <option value="04-07-2024">{{ date('l, M d, Y',strtotime('04-07-2024')) }}</option>
                        <option value="05-07-2024">{{ date('l, M d, Y',strtotime('05-07-2024')) }}</option>
                        <option value="06-07-2024">{{ date('l, M d, Y',strtotime('06-07-2024')) }}</option>
                        <option value="07-07-2024">{{ date('l, M d, Y',strtotime('07-07-2024')) }} (SabaSaba Day)</option>
                        <option value="08-06-2024">{{ date('l, M d, Y',strtotime('08-07-2024')) }}</option>
                        <option value="09-06-2024">{{ date('l, M d, Y',strtotime('09-07-2024')) }}</option>
                        <option value="10-07-2024">{{ date('l, M d, Y',strtotime('10-07-2024')) }}</option>
                        <option value="11-07-2024">{{ date('l, M d, Y',strtotime('11-07-2024')) }}</option>
                        <option value="12-07-2024">{{ date('l, M d, Y',strtotime('12-07-2024')) }}</option>
                        <option value="13-07-2024">{{ date('l, M d, Y',strtotime('13-07-2024')) }}</option>
                    </select>
                </div>
                
                <div class=" form-group col-md-12">
                    <textarea name="comment" class=" form-control" placeholder="Enter any comment or consideration during your visit..."></textarea>
                </div>
                <div class=" text-right form-group col-md-12">
                    <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner" >Submit</button>
                </div>
            </div>

           
        </div>
    </form>

    <div class=" form-group">
        <p style="font-size: 2vmin" class=" "><b>NOTE!</b> Ensure your Phone Number and the email entered you have access to it
            because all communication will be made through your registered Phone Number and Email, including your ticket after receiving your
            payments. </p>
    </div>
   
</div>

