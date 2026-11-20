<x-app-layout>
    <title>{{ env('APP_NAME') }} | Company</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-title">

            </div>
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('companies.index') }}"><i class=" icon-office position-left"></i>COMPANNIES</a></li>
            <li class="active">company-profile</li>
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
        <div class="panel-heading">
            <h6 class="panel-title text-bold">COMPANIES MORE DETAILS</h6>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                    <li><a data-action="reload"></a></li>
                    <li><a data-action="close"></a></li>
                </ul>
            </div>
        </div>
<div class=" panel-body table-responsive">

    @if ($errors->any())
    @foreach ($errors->all() as $error)
    <span class=" text-danger"><li class=" alert">{{ $error }}</li></span>
    @endforeach
    @endif

  <div class=" panel-body">
    <div class=" form-group">
        <a href="{{ route('companies.index') }}" class=" btn btn-default text-primary"><i class=" icon-arrow-left22"></i> Back</a>
    </div>
         {{-- messages --}}
    <div class="form-group">
        @if (Session::get('success'))

        <div class=" alert alert-success alert-bordered alert-rounded">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
            <span class="text-semibold">Congratulations!</span> {{ session::get('success') }}
        </div>
        @elseif (Session::get('fail'))
        <div class=" alert alert-danger alert-bordered alert-rounded">
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
            <span class="text-semibold">Oops!</span> {{ session::get('fail') }}
        </div>
        @endif
     </div>
     {{-- //messages --}}

{{-- user registration here --}}




                <div class="row">
                    @foreach ($companies as $item)
                    <div class=" form-group text-right">
                        <a data-toggle="modal" data-target="#{{ $item->companyID }}" class=" btn btn-danger"><i class=" icon-bin"></i> Delete Company</a>
                    </div>
                    {{-- delete modal --}}
                    <div id="{{ $item->companyID }}" class="modal fade" aria-modal="true">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h5 class="modal-title">Are you sure you whant to delete <b><q>{{ $item->companyName }}</q></b> ?</h5>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('companies.destroy', ['company' => $item->companyID]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class=" text-right">
                                            <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                            <button type="submit" class="btn btn-danger ml-20" >Yes, Delete</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- delete modal --}}
                    <table class=" table table-striped table-bordered table-sm">
                        <thead>
                            <tr>
                                <th class=" text-bold text-center bg-success text-uppercase text-monospace" colspan="2"><h4>{{ $item->companyName }}@php echo " ' S" @endphp PROFILE</h4></th>
                            </tr>

                        </thead>
                        <tbody>
                            <form action="{{ route('companies.update',['company'=>$item->companyID]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                            <tr>
                                <td>Company Registration ID: </td>
                                <td class=" text-bold">
                                    <input type="text" class=" form-control" placeholder="Company Name" value="{{ $item->registrationID}}" readonly>
                                </td>
                            </tr>
                            <tr >
                                <td>Company Name: </td>
                                <td class=" text-bold">
                                    <input type="text" name="company_name" class=" form-control" placeholder="Company Name" value="{{ $item->companyName }}">
                                    <span class="text-danger"> @error('company_name'){{ $message }}@enderror</span>
                                </td>
                            </tr>

                            <tr >
                                <td >Company Phone: </td>
                                <td class=" text-bold">
                                    <input type="text" name="company_phone" class=" form-control " placeholder="Company Phone" value="{{ $item->companyPhone }}">
                                    <span class="text-danger"> @error('company_phone'){{ $message }}@enderror</span>
                                </td>
                            </tr>
                            <tr >
                                <td >Company Email:</td>
                                <td class=" text-bold"><input type="text" name="company_email" class=" form-control " placeholder=" Company Email" value="{{ $item->companyEmail }}">
                                    <span class="text-danger"> @error('company_email'){{ $message }}@enderror</span></td>
                            </tr>
                            <tr >
                                <td >Company Sector: </td>
                                <td class=" text-bold">
                                    <div class=" form-group">
                                        <select name="company_sector" class=" form-control" data-live-search="true" data-width="100%" placehoder="Company Sector">
                                            <option value="{{ $item->companySector }}">{{ $item->sectorName }}</option>
                                            <optgroup label="Company Sectors">
                                                @foreach ($sectors as $sector)
                                                <option value="{{ $sector->sectorID }}">{{ $sector->sectorName }}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <span class="text-danger"> @error('company_sector'){{ $message }}@enderror</span>
                                    </div>
                                </td>
                            </tr>
                            <tr >
                                <td >Company Category: </td>
                                <td class=" text-bold">
                                    <select name="company_category" class=" form-control" data-live-search="true" data-width="100%" placehoder="Select menu status">
                                        <option value="{{ $item->companyCategory }}">{{ $item->companyCategory }}</option>
                                        <optgroup label="Company Categories">
                                           <option value="Government Institution">Government Institution</option>
                                           <option value="Private Institution">Private Institution</option>
                                        </optgroup>
                                    </select>
                                    <span class="text-danger"> @error('company_category'){{ $message }}@enderror</span>
                                </td>
                            </tr>
                            <tr>
                                <td >Company Physical Address(Location):</td>
                                <td class=" text-bold">
                                    <input type="text" name="company_location" class=" form-control " placeholder=" Company Location" value="{{ $item->companyLocation }}">
                                </td>
                            </tr>
                            <tr>

                                <td colspan="2" class="text-right">

                                        <button class=" btn btn-primary btn-ladda btn-ladda-spinner">Save Company Primary Information Changes</button>

                                </td>
                            </tr>
                        </form>
                        </tbody>
                    </table>
                    <br>
                    <br>
                    {{--  <h6 class=" text-bold text-center p-10 bg-slate-600 ">MORE COMPANY DETAILS.</h6>  --}}

                    <table class=" table table-striped table-sm table-bordered">
                        <thead>
                            <tr>
                                <th colspan="2" class=" text-bold text-pink-800 text-center" >OTHER COMPANY DETAILS</th>
                            </tr>
                            <tbody>
                                <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                <tr>
                                    <td>Business Registration Number (BRELA)</td>
                                    <td class=" text-bold">
                                        <input type="text" name="registrationNo" class=" form-control " placeholder="Company registration Number" value="{{ $item->registrationNo }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Company TIN number:</td>
                                    <td class=" text-bold">
                                        <input type="text" name="tinNo" class=" form-control " placeholder="Company TIN number" value="{{ $item->tinNo }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Company Telephone Number:</td>
                                    <td class=" text-bold">
                                        <input type="text" name="companyTele" class=" form-control input-sm" placeholder=" Company Telephone Number" value="{{ $item->companyTel }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Company Address: </td>
                                    <td class=" text-bold">
                                        <input type="text" name="companyAddress" class=" form-control input-sm " placeholder="Company Address Eg, P.O.BOX 5402" value="{{ $item->companyAddress }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Company Website: </td>
                                    <td class=" text-bold">
                                        <input type="text" name="website" class=" form-control input-sm" placeholder="Company website" value="{{ $item->website }}">
                                    </td>
                                </tr>
                                {{--  contact person  --}}
                                <tr>
                                    <td colspan="2" class=" text-bold text-pink-800 text-center">COMPANY CONTACT PERSON DETAILS</td>
                                </tr>

                                <tr>
                                    <td>Contact Person Name: </td>
                                    <td class=" text-bold">
                                        <input type="text" name="personalName" class=" form-control input-sm " placeholder="Contact Person Name" value="{{ $item->personalName }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Contact person Phone No:</td>
                                    <td class=" text-bold">
                                        <input type="text" name="personalPhone" class=" form-control input-sm" placeholder="Contact Personal Phone Number" value="{{ $item->personalPhone }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Contact Person Email: </td>
                                    <td class=" text-bold">
                                        <input type="text" name="personalEmail" class=" form-control input-sm " placeholder="Contact Person Email" value="{{ $item->personalEmail }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Contact Person Title:</td>
                                    <td class=" text-bold">
                                        <input type="text" name="personalTitle" class=" form-control input-sm " placeholder="Contact Person Title" value="{{ $item->personalTitle }}">
                                    </td>
                                </tr>
                @endforeach
                <input type="hidden" name="from" value="company_details">
                <input type="hidden" name="companyID" value="{{ $item->companyID }}">
                <input type="hidden" name="companyDetailID" value="{{ $item->companyDetailID }}">
                <tr>
                    <td colspan="2">
                        <div class=" text-right">
                            <button type="submit" class="btn btn-primary  btn-ladda btn-ladda-spinner"><span class="ladda-label">Save-Changes</span></button>
                        </div>
                    </td>
                </tr>

            </form>

        </tbody>
    </thead>
</table>
        </div>
    </div>
</div>
</div>
{{-- //user registration  --}}
</x-app-layout>
