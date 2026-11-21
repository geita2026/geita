<div class="panel panel-white">
    <div class=" panel-heading text-center text-bold" style="background-color: rgb(230, 230, 236)">
        <h1>GEITA MINERAL EXHIBITION - {{ date('Y') }}</h1>
    </div>
    <div class=" panel-body">

        <div class="alert alert-warning alert-styled-right alert-bordered">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
            Dear <span class=" text-bold">{{ Auth::user()->first_name }}</span>
            We are reminding you, booking is all about 73% of space booking, please make you booking and pay for it as early as possible.  <a href="{{ route('space-categories.index') }}" class=" btn btn-default alert-link ml-20 text-bold">Book Now</a>
        </div>


        <div class="panel panel-flat">
            <div class="panel-heading">
                <h6 class="panel-title text-bold">Your Exhibition Space Informations</h6>
            </div>

            <div class="table-responsive">
                <table class="table table-lg text-nowrap">
                    <tbody>
                        <tr>
                            <td class="col-md-5">
                                <div class="media-left">
                                    <div id="campaigns-donut"></div>
                                </div>

                                <div class="media-left">
                                    <h5 class="text-semibold no-margin">KARUME HALL - KRM-003</h5>
                                    <ul class="list-inline list-inline-condensed no-margin">
                                        <li>
                                            <span class="status-mark border-success"></span>
                                        </li>
                                        <li>
                                            <span class="text-muted">9 M<sup>2</sup> Size,  Normal Stand</span>
                                        </li>
                                    </ul>
                                </div>
                            </td>

                            <td class="col-md-5">
                                <div class="media-left">
                                    <div id="campaign-status-pie"></div>
                                </div>

                                <div class="media-left">
                                    <h5 class="text-semibold no-margin">Payment Status</h5>
                                    <ul class="list-inline list-inline-condensed no-margin">
                                        <li>
                                            <span class="status-mark border-danger"></span>
                                        </li>
                                        <li>
                                            <span class="text-muted text-warning">In Progress</span>
                                        </li>
                                    </ul>
                                </div>
                            </td>

                            <td class="text-right col-md-2">
                                <a href="{{ route('my_invoices.index') }}" class="btn bg-indigo-300"> View Invoice <i class=" icon-arrow-right13"></i></a>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>


        <div class="panel panel-flat">
            <div class="panel-heading">
                <h6 class="panel-title text-bold">Other services</h6>
            </div>

            <div class="table-responsive">
                <table class="table table-lg text-nowrap">
                    <tbody>

                        <tr>
                            <div class=" alert-primary p-20">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
                                Dear <span class=" text-bold">{{ Auth::user()->first_name }}</span>
                                We did'nt found any additional service booked by you, kindly you can book them by once you want through the following link  <a href="#" class=" btn btn-default alert-link ml-20 text-bold">Book Now</a>
                            </div>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            @foreach ($myCompany as $item)
            <div id="UpdateMyCompany" class="modal fade" aria-modal="true" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title text-uppercase text-center">Update <b>{{ $item->companyName }}'s</b> Informations</h5>
                        </div>
                        <div class="modal-body">
                            <div class=" p-10">
                                <code>Note!</code> This stape is mondatory, for confirming and updating your information before proceeding with booking process.
                            </div>
                            <form  data-action="{{ route('update_my_company') }}" method="POST" id="update" class="stepy-clickable">
                                @csrf
                                <fieldset title="1">
                                    <input type="hidden" name="id" value="{{ $item->companyID }}">
                                    <legend class="text-semibold">PRIMARY INFORMATION</legend>
                                    <div class=" row">
                                        <div class=" row">
                                            <div class=" form-group col-md-6">
                                                <label>Name<i class=" text-danger">*</i></label>
                                                <input type="text" name="company_name" class=" form-control required" placeholder="Company Name" value="{{ $item->companyName }}">
                                                <span class="text-danger text-error company_name_error"></span>
                                            </div>
                                            <div class=" form-group col-md-6">
                                                <label>Email</label>
                                                <input type="text" name="company_email" class=" form-control" placeholder=" Company Email ie. remixkabandwa@gmail.com" value="{{$item->companyEmail }}">
                                                <span class="text-danger text-error company_email_error"></span>
                                            </div>
                                        </div>
                                        <div class=" row">
                                            <div class=" form-group col-md-6">
                                                <label>Phone<i class=" text-danger">*</i></label>
                                                <input type="text" name="company_phone" class="form-control" placeholder="255753427137" value="{{ $item->companyPhone }}" >
                                                <span class="text-danger text-error company_phone_error"></span>
                                            </div>
                                            <div class=" form-group col-md-6">
                                                <label>Location<i class=" text-danger">*</i></label>
                                                <input type="text" name="company_location" class=" form-control" placeholder=" Company Location" value="{{ $item->companyLocation }}">
                                                <span class="text-danger text-error company_location_error"></span>
                                            </div>
                                        </div>
                                        <div class=" row">
                                            <div class=" form-group col-md-6">
                                                <label>Sector<i class=" text-danger">*</i></label>
                                                <select name="company_sector" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select Company category">
                                                    <option value="{{ $item->companySector }}">{{ $item->sectorName }}</option>
                                                    <optgroup label="Company Sectors">
                                                        @foreach($sector_list as $sector)
                                                        <option value="{{ $sector->sectorID }}">{{ $sector->sectorName }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                </select>
                                                <span class="text-danger text-error company_sector_error"></span>
                                            </div>
                                            <div class=" form-group col-md-6">
                                                <label>Company Category<i class=" text-danger">*</i></label>
                                                <select name="company_category" class="bootstrap-select " data-live-search="true" data-width="100%" placehoder="Select Company category">
                                                    <option value="{{ $item->companyCategory }}">
                                                        @if($item->companyCategory==1) Government Institution @elseif ($item->companyCategory==3) SMEs @else Private Companies @endif
                                                    </option>
                                                        <option value="1">Government Institution</option>
                                                        <option value="2">Private Companies</option>
                                                        <option value="3">SMEs</option>
                                                </select>
                                                <span class="text-danger company_category_error"> </span>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset title="2">
                                    <legend class="text-semibold">REGISTRATION & CONTACTS</legend>
                                    <div class="row">
                                        <div class=" col-md-6">
                                            <label>Busines Registration Number</label>
                                            <div class="form-group">
                                                <input type="text" name="registrationNo" class="form-control " placeholder="999-999-999" value="{{ $item->registrationNo }}" data-mask="999-999-999">
                                            </div>
                                        </div>
                                        <div class=" col-md-6">
                                            <label>Taxpayer Identification Number (TIN)</label>
                                            <div class="form-group">
                                                <input type="text" name="tinNo" class=" form-control " placeholder="999-999-999" value="{{ $item->tinNo }}" data-mask="999-999-999">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class=" col-md-6">
                                            <label>Telephone</label>
                                            <div class="form-group">
                                                <input type="text" name="companyTele" class=" form-control input-sm" placeholder=" Company Telephone Number" value="{{ $item->companyTel }}">
                                            </div>
                                        </div>
                                        <div class=" col-md-6">
                                            <label>Address</label>
                                            <div class="form-group">
                                                <input type="text" name="companyAddress" class=" form-control input-sm " placeholder="Company Address Eg, P.O.BOX 5402" value="{{ $item->companyAddress }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class=" col-md-6">
                                            <label>Website</label>
                                            <div class="form-group">
                                                <input type="text" name="website" class=" form-control input-sm" placeholder="Company website" value="{{ $item->website }}">
                                            </div>
                                        </div>
                                        <div class=" col-md-6">
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset title="3">
                                    <legend class="text-semibold">CONTACT PERSONAL INFORMATION</legend>
                                    <div class=" row">
                                        <div class=" col-md-6">
                                            <label>Name: </label>
                                            <input type="text" name="personalName" class=" form-control input-sm " placeholder="Contact Person Name" value="{{ $item->personalName }}">
                                            <span class=" text-error text-danger personalName_error"></span>
                                        </div>
                                        <div class=" col-md-6">
                                            <div class=" form-group">
                                                <label>Phone No:</label>
                                                <input type="text" name="personalPhone" class=" form-control input-sm" placeholder="Contact Personal Phone Number" value="{{ $item->personalPhone }}">
                                                <span class=" text-error text-danger personalPhone_error"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=" row">
                                        <div class=" col-md-6">
                                            <div class=" form-group">
                                                <label>Email:</label>
                                                <input type="email" name="personalEmail" class=" form-control input-sm " placeholder="Contact Person Email" value="{{ $item->personalEmail }}">
                                            </div>
                                        </div>
                                        <div class=" col-md-6">
                                            <div class=" form-group">
                                                <label>Title:</label>
                                                <input type="text" name="personalTitle" class=" form-control input-sm " placeholder="Contact Person Title" value="{{ $item->personalTitle }}">
                                                <span class=" text-error text-danger personalTitle_error"></span>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner stepy-finish" > Update Compay Details</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
{{-- external users --}}
