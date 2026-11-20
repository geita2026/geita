<x-app-layout>
    <title>{{ env('APP_NAME') }} | eTAC Dashboard</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> TRANSACTIONS</a></li>
                <li class="active">View All eTac Dashboard</li>
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
            <div class=" panel-body table-responsive">

                <span class=" text-danger">@error('posName') {{ $message }} @enderror</span>
                <div class=" col-md-12  form-group">
                    <h2 class=" text-center text-bold"> eTAC DASHBOARD</h2>
                </div>
                <div class="category-content">
                    <div class="row row-condensed">
                        <div class=" col-md-12">
                            <h3 style="background-color: burlywood" class=" text-center zoom p-20 text-bold">{{ date('M d, Y') }} SUMMARY GATE COLLECTIONS</h3>
                        </div>
                        <a>
                            <div class="col-md-6 form-group zoom">
                                <button type="submit"
                                    class="btn btn-block btn-float btn-float-lg text-uppercase text-bold border-primary"
                                    style="border:5px solid rgb(69, 150, 182);">
                                    <i>{{ number_format($exactlyCollected) }}</i>
                                    <span>Total Today Gate Collection</span>
                                </button>
                            </div>

                             
                            <div class="col-md-6 form-group zoom">
                                <button type="submit"
                                    class="btn btn-block btn-float btn-float-lg text-uppercase text-bold border-primary"
                                    style="border:5px solid rgb(69, 150, 182);">
                                    <i>{{ number_format($overallDemagedTickets) }}</i>
                                    <span>OVERALLTotal Demaged Tickets</span>
                                </button>
                            </div>
                        </a>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /dashboard content -->
    {{-- add role modal --}}
    <form action="{{ route('pos.store') }}" method="POST"  enctype="multipart/form-data">
        @csrf
        <div id="addPos" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-blue" >
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title">Add user POS.</h5>
                    </div>
                    <div class="modal-body">

                        <div class="">
                            <div class=" form-group">
                                <label>POS Name<i class=" text-danger">*</i></label>
                                <input type="text" id="posName" name="posName" class=" form-control" placeholder=" permission Name" value="{{ old('posName') }}" required>
                            </div>
                            <div class=" form-group">
                                <label>POS Name<i class=" text-danger">*</i></label>
                                <textarea name="posDescription" class=" form-control" placeholder="Pos Descriptions"></textarea>
                            </div>
                            <div class=" form-group">
                                <label>POS STATUS<i class=" text-danger">*</i></label>
                                <select name="posStatus" class=" form-control">
                                    <option value="1">ACTIVE</option>
                                    <option value="0">INACTIVE</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Add POS</span></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{--  end  --}}
</x-app-layout>



