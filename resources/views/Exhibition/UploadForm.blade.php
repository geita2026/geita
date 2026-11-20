<x-app-layout>
    <title>{{ env('APP_NAME') }} | VirtualPavilion</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class=" icon-home position-left"></i> Home
                    </a></li>
                <li class="active">Confirm Participation</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- Content area -->
    <div class="content">

        <div class="panel panel-flat">
            <div class=" panel-body">
                <div class=" form-group row">
                    @foreach ($exhibitor as $item)
                    @if ($item->exhibitedProducts != null && $item->exhibitionSpace != null)  
                        <form action="{{ route('submit_form',['participationId'=>$item->participation_id]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="col-md-12 form-group">
                                <label>Signed Participation Form<i class=" text-danger">*</i></label>
                                <input type="file" name="participationForm" class="form-control" accept="pdf" required>
                                <span class=" text-danger">
                                    @error('participationForm')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class=" form-group col-md-12 text-right">
                                <br>
                                <button type="submit" class=" btn btn-primary">Upload Selected File<i class=" icon icon-arrow-right13"></i></button>
                            </div>
                        </form>
                    @endif

                    @if ($item->participationCommitimentFile!=null)
                    <div class=" col-md-12">
                        <h3 class=" text-center text-bold">UPLOADED PARTICIPATION FORM NO 2A</h3>
                    </div>
                        <div class=" text-center col-md-12 form-group table-responsive">
                            <iframe src="{{ $item->participationCommitimentFile }}" height="600px" width="1000px"></iframe>
                        </div>
                    @endif
                @endforeach
                </div>
                
            </div>
        </div>
    </div>

</x-app-layout>

<script type="text/javascript" src="{{ asset('assets/js/plugins/uploaders/fileinput.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('assets/js/pages/uploader_bootstrap.js') }}"></script>
<!-- /theme JS files -->
