
<x-app-layout>
    <title>{{ env('APP_NAME') }} | Generate Badges</title>

   <div class="page-header">
       <div class="page-header-content">
           @include('layouts.heading-elements')
       </div>
       <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('booking.index') }}"><i class="icon-stack-check position-left"></i>Generate Badges</a></li>
            <li class="active">Generate exhibition badges</li>
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
            <h3 class="panel-title text-bold text-uppercase">
                EXHIBITION BADGES GENERATION
            </h3>
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
            <div class=" col-md-6 form-group">
                <a href="{{ route('exhibitors_list.index') }}" class=" btn btn-default border-primary"><i class=" icon-arrow-left7"></i> Back</a>
            </div>



            <div class=" form-group col-md-6 text-right">
                <form action="{{ route('generate-badges.edit',['generate_badge']) }}" method="POST">
                    @csrf
                    @method('GET')
                    <input type="hidden" name="event_id" value="">
                    <button class="btn btn-default border-primary">Generate All Pending Badges</button>
                </form>
            </div>

           </div>

            <table class=" table table-bordered table-striped datatable-button-html5-columns">
                <thead>
                    <tr>
                        <th class=" text-bold text-center">#</th>
                        <th class=" text-bold text-center">COMPANY NAME</th>
                        <th class=" text-bold text-center">TOTAL Members</th>
                        <th class=" text-bold text-center">Generate Badge</th>
                    </tr>
                </thead>
                <tbody>
                     @php $i=1; @endphp
                  @foreach ($companies as $item)
                  @php $j=0;$a=0; @endphp
                  @foreach ($badges as $badge)
                  @if($badge->company_id==$item->companyID)
                  @php $j++; @endphp
                  @if($badge->badge_status==0)
                  @php $a++; @endphp
                  @endif
                  @endif
                  @endforeach

                  @if($j>0)
                  <tr>
                    <td>{{ $i }}</td>
                    <td>{{ $item->companyName }}</td>
                    <td class=" text-center text-bold">
                        {{ $j }}
                    </td>
                    <td class=" text-center">
                        @if ($a>0)
                        <form action="{{ route('generate-badges.store') }}" method="POST">
                            @csrf

                            <input type="hidden" name="company_id" value="{{ $item->companyID }}"/>
                            <button type="submit" class=" btn btn-warning ">Generate Badges</button>

                        </form>
                        @else
                        <form action="{{ route('prepare_badges.show',['prepare_badge'=>$item->companyID]) }}" method="POST">
                            @csrf
                            @method('GET')
                            <input type="hidden" name="company_id" value="{{ $item->companyID }}"/>
                            <button type="submit" class=" btn btn-success">View Badges</button>
                        </form>
                        @endif
                    </td>

                    <div id="update_company{{ $item->companyID }}" class="modal fade" aria-modal="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h4 class="modal-title text-bold text-center text-uppercase">UPDATE {{ $item->companyName }} DETAILS</h5>
                                    </div>
                                    <div class=" modal-body">
                                        <form action="{{ route('exhibitors_list.update',['exhibitors_list'=>$item->companyID]) }}" method="post" id="company">
                                            @csrf

                                        </div>
                                        <div class=" modal-footer">
                                            <div class=" text-right">
                                            <button class=" btn btn-default border-warning text-warning" data-dismiss="modal"> Close</button>
                                            <button type="submit" class="btn btn-primary  btn-ladda btn-ladda-spinner" ><span class="ladda-label">Update Company</span></button>
                                            </div>
                                        </div>
                                    </form>
                            </div>
                        </div>
                    </div>

                </tr>
                @php $i++; @endphp
                  @endif


                  @endforeach
                </tbody>
            </table>

        </div>
    </div>
 <!-- /content area -->
</div>
<!-- /main content -->
</div>
</div>
</x-app-layout>
