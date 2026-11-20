<x-app-layout>
    <title>{{ env('APP_NAME') }} | Company-Reports</title>
    {{-- page header --}}
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href=""><i class="icon-home2 position-left"></i> COMPANY DETAILS</a></li>
            <li class="active">View company details</li>
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
             <h6 class="panel-title text-thin text-bold"> COMPANY DETAILS </h6>
             <div class="heading-elements">
                 <ul class="icons-list">
                     <li><a data-action="collapse"></a></li>
                     <li><a data-action="reload"></a></li>
                     <li><a data-action="close"></a></li>
                 </ul>
             </div>
         </div>
 <div class=" panel-body table-responsive">

      <div class=" col-md-12 form-group">
          <a href="{{ route('exhibitors.index') }}" class=" btn btn-default"><i class=" icon-arrow-left12"></i> Back </a>
      </div>
      <div class=" col-md-12 p-20">
          @foreach ($companies as $data)

          @endforeach
        <div class="mapouter">
            <div class="gmap_canvas">
                <iframe src="https://maps.google.com/maps?q= {{ $data->companyName }}&amp;t=&amp;z=13&amp;ie=UTF8&amp;iwloc=&amp;output=embed" frameborder="0" scrolling="no" style="width: 100%; height: 400px;">
                </iframe><a href="https://fnftest.net" style="display:none">FNF Character Test</a>
                <a href="https://www.googlemapsiframegenerator.com" style="display:none">
                Google Maps Iframe Generator - Free Html Embed Code</a><style>.
                    gmap_canvas{overflow:hidden;background:none!important;height:400px;width:600px;}
                </style>
            </div>
        </div>
      </div>
      <div class=" col-md-12">
          @foreach ($companies as $item)
          <table class=" table table-bordered table-striped">
              <thead>
                  <tr><th colspan="2" class="text-center"><b class=" text-indigo">{{ $item->companyName }}{{ "'" }}S</b> PROFILE</th></tr>
                  <tr>
                      <th class=" text-pink text-bold text-center" colspan="2">COMPANY INFORMATIONS</th>
                  </tr>

                  <tr >
                    <td>Company Name: </td>
                    <td class=" text-bold">
                        <input type="text" name="company_name" class=" form-control"  value="{{ $item->companyName }}" disabled>
                        <span class="text-danger"> @error('company_name'){{ $message }}@enderror</span>
                    </td>
                </tr>

                <tr >
                    <td >Company Phone: </td>
                    <td class=" text-bold">
                        <a href="tel:{{ $item->companyPhone }}">
                            <input type="text" name="company_phone" class=" form-control " value="{{ $item->companyPhone }}" disabled>
                        </a>

                    </td>
                </tr>
                <tr >
                    <td >Company Email:</td>
                        <td class=" text-bold">
                            <a href="mailto:{{ $item->companyEmail }}"><input type="text" name="company_email" class=" form-control " value="{{ $item->companyEmail }}" disabled></a>
                        </td>
                </tr>
                <tr >
                    <td >Company Sector: </td>
                    <td class=" text-bold">
                        <div class=" form-group">
                            <input type="text" value="{{ $item->sectorName }}" class=" form-control" disabled>
                        </div>
                    </td>
                </tr>
                <tr >
                    <td >Company Category: </td>
                    <td class=" text-bold">
                        <input type="text" value="{{ $item->companyCategory }}" class=" form-control" disabled>
                    </td>
                </tr>
                <tr>
                    <td >Company Physical Address(Location):</td>
                    <td class=" text-bold">
                        <input type="text" name="company_location" class=" form-control "  value="{{ $item->companyLocation }}"  disabled>
                    </td>
                </tr>
                <tr>
                    <th colspan="2" class=" text-bold text-pink-800 text-center" >OTHER COMPANY DETAILS</th>
                </tr>
                    <tr>
                        <td>Business Registration Number (BRELA)</td>
                        <td class=" text-bold">
                            <input type="text" name="registrationNo" class=" form-control "  value="{{ $item->registrationNo }}" disabled>
                        </td>
                    </tr>
                    <tr>
                        <td>Company TIN number:</td>
                        <td class=" text-bold">
                            <input type="text" name="tinNo" class=" form-control "  value="{{ $item->tinNo }}" disabled>
                        </td>
                    </tr>
                    <tr>
                        <td>Company Telephone Number:</td>
                        <td class=" text-bold">
                            <input type="text" name="companyTele" class=" form-control input-sm"  value="{{ $item->companyTel }}" disabled>
                        </td>
                    </tr>
                    <tr>
                        <td>Company Address: </td>
                        <td class=" text-bold">
                            <input type="text" name="companyAddress" class=" form-control input-sm "  value="{{ $item->companyAddress }}" disabled>
                        </td>
                    </tr>
                    <tr>
                        <td>Company Website: </td>
                        <td class=" text-bold">
                            <a href="{{$item->website }}" target="_blank"><input type="text" name="website" class=" form-control input-sm"  value="{{ $item->website }}" disabled></a>
                        </td>
                    </tr>
                    {{--  contact person  --}}
                    <tr>
                        <td colspan="2" class=" text-bold text-pink-800 text-center">COMPANY CONTACT PERSON DETAILS</td>
                    </tr>

                    <tr>
                        <td>Contact Person Name: </td>
                        <td class=" text-bold">
                            <input type="text" name="personalName" class=" form-control input-sm "  value="{{ $item->personalName }}" disabled>
                        </td>
                    </tr>
                    <tr>
                        <td>Contact person Phone No:</td>
                        <td class=" text-bold">
                            <a href="tel:{{ $item->personalPhone }}"><input type="text" name="personalPhone" class=" form-control input-sm"  value="{{ $item->personalPhone }}" disabled></a>
                        </td>
                    </tr>
                    <tr>
                        <td>Contact Person Email: </td>
                        <td class=" text-bold">
                            <a href="mailto:{{ $item->personalEmail }}"><input type="text" name="personalEmail" class=" form-control input-sm "  value="{{ $item->personalEmail }}" disabled></a>
                        </td>
                    </tr>
                    <tr>
                        <td>Contact Person Title:</td>
                        <td class=" text-bold">
                            <input type="text" name="personalTitle" class=" form-control input-sm "  value="{{ $item->personalTitle }}" disabled>
                        </td>
                    </tr>

              </tbody>
          </table>
          @endforeach
      </div>
</div>
    </div>
    </div>
</div>
<!-- /content area -->
<!-- /main content -->
</x-app-layout>
<!-- Theme JS files -->
 <script type="text/javascript" src="{{ asset('assets/js/plugins/visualization/echarts/echarts.js') }}"></script>
 <script type="text/javascript" src="{{ asset('assets/js/charts/echarts/columns_waterfalls.js') }}"></script>
 <script type="text/javascript" src="{{ asset('assets/js/charts/echarts/timeline_option.js') }}"></script>
 <!-- /theme JS files -->

 <!-- Theme JS files -->
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>

	<script type="text/javascript" src="{{ asset('assets/js/core/app.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/maps/google/basic/basic.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/maps/google/basic/geolocation.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/maps/google/basic/coordinates.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/maps/google/basic/click_event.js') }}"></script>
	<!-- /theme JS files -->
