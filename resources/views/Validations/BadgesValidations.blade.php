<x-app-layout>
    <title>{{ env('APP_NAME') }} | Badges</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('permissions.index') }}"><i class="icon-home2 position-left"></i> EXHIBITION BADGES</a></li>
                <li class="active">View All exhibitor Badges</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->
    <script>
        function play() {
            alert('OK');
          var audio = new Audio('https://interactive-examples.mdn.mozilla.net/media/cc0-audio/t-rex-roar.mp3');
          audio.play();

        }
          </script>
    <!-- Content area -->
    <div class="content">
        <div class="panel panel-white">

            <div class=" panel-body table-responsive">

                <div class=" row form-group col-md-12">
                    <div class="col-md-6">
                        <video id="preview" class=" img-rounded col-md-12" style="width: 100%"></video>
                    </div>
                    <div class=" col-md-6">
                        <h3 class=" text-center text-bold">BADGE INFORMATIONS APPEARES BELLOW:</h3>
                        @php $i=0; @endphp
                       @foreach ($badges as $item)
                       <div class=" row com-md-12" style="border: 5px dashed rgb(127, 176, 190); padding: 1%">
                        <div class=" col-md-8">
                            <div class="text-uppercase text-center col-md-12 form-group" id="h3"><b class=" text-uppercase">{{ $item->companyName }}</b></div>
                            @if ($balance>0)
                            <div class="col-md-12 form-group text-center bg-warning p-10"><b class=" text-uppercase" >owed</b></div>
                            @else
                            <div class="col-md-12 form-group text-center bg-success p-10"><b class=" text-uppercase" >OK</b></div>
                            @endif

                        </div>
                        <div class=" col-md-4 form-group text-center">
                            <img src="{{ asset('Members/'.$item->member_image) }}" height="200" width="200" class=" img-rounded"><br><br>
                            <b class=" text-uppercase text-primary">{{ $item->first_name }} {{ $item->middle_name }} {{ $item->last_name }}</b>
                            <div class="col-md-12 form-group text-center" style="background-color: {{ $item->group_background_color }};color:{{ $item->text_color }}; text-align:center;padding:2%"><b class=" text-uppercase" >{{ $item->group_name }}</b></div>
                        </div>
                       </div>

                       @php $i++; @endphp
                       @endforeach

                       @if ($i==0)
                       <p class=" alert-danger p-20 col-md-12">This Badge does not exist in our database! <br>
                        (<i>{{ $id ?? '' }}</i>)<br><br>
                        <a href="#" class="btn btn-danger text-right">Report Badge</a>
                       </p>
                       @endif
                    </div>
                </div>


    {{-- add role modal --}}
    <form action="{{ route('print_badges.update',['print_badge']) }}" method="POST" id="ValidationForm" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div id="add_role" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-blue" >
                        <h5 id="h2" class="modal-title text-uppercase text-center text-bold">Add New User Member Badge.</h5>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="badge_id" id="badgeId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default border-warning" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Add Member</span></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{--  end  --}}
</x-app-layout>


    <script>
        $(function(){
        $('form').on('submit', function(){
            $('select').removeAttr('disabled');
        });
    });
    </script>
{{--  --}}


<script type="text/javascript">

let opts = {
  continuous: true,
  video: document.getElementById('preview'),
  mirror: false,
  captureImage: true,
  backgroundScan: false,
  refractoryPeriod: 5000,
  scanPeriod:1
};

    let scanner = new Instascan.Scanner(opts);
    scanner.addListener('scan',function(content){
        document.getElementById('badgeId').value=content;
        document.getElementById('ValidationForm').submit();
    });

    Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length > 0) {
    var selectedCam = cameras[0];
    $.each(cameras, (i, c) => {
        if (c.name.indexOf('back') != -1) {
            selectedCam = c;
            return false;
        }
    });

    scanner.start(selectedCam);
}
 else
{
    console.error('No cameras found.');
}
});




  </script>


