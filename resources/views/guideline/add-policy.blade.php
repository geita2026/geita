<x-app-layout>
    {{-- page header --}}
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-title">

            </div>
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i>POLICIES</a></li>
            <li class="active"> add policies</li>
        </ul>

        <ul class="breadcrumb-elements">
         @include('layouts.help')
        </ul>
    </div>
    </div>
 <!-- /page header -->

 <!-- Content area -->
 <div class="content">





    <!-- Summernote editor -->
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h5 class="panel-title">Summernote editor</h5>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                    <li><a data-action="reload"></a></li>
                    <li><a data-action="close"></a></li>
                </ul>
            </div>
        </div>

        <div class="panel-body">


                <form action="{{ route('policies.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        @foreach ($content as $data)
                        <input type="hidden" name="id" value="{{ $data->policy_id }}">
                        @endforeach
                        <textarea name="policy_content" cols="18" rows="18" class="wysihtml5 wysihtml5-default form-control" placeholder="Enter text here ..." @can('Create Policy') @else disabled @endcan >
                            @foreach ($content as $data)
                            {{ $data->policy_content }}
                            @endforeach

                        </textarea>
                        </div>

                        @can('Create Policy')
                        <div class="text-right">

                            <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner">Save Changes</button>
                        </div>
                        @else @endcan


                    </form>

        </div>
    </div>
    <!-- /summernote editor -->




     <div class="panel panel-white">
         <div class="panel-heading">
             <h6 class="panel-title text-thin text-bold text-uppercase">ADD POLICY</h6>
             <div class="heading-elements">
                 <ul class="icons-list">
                     <li><a data-action="collapse"></a></li>
                     <li><a data-action="reload"></a></li>
                     <li><a data-action="close"></a></li>
                 </ul>
             </div>
         </div>
 <div class=" panel-body table-responsive">

         <div class=" p-10 alert-success" style="font-size:10px">
            <ul>
             <p><i class=" icon-info22"></i> You are reminded to save any changes you made before you leave the page, otherwise you will loose any changes made.</p>
            </ul>
         </div>

         {{--  policies  --}}
         <div class=" mt-10">
            <form action="{{ route('policies.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    @foreach ($content as $data)
                    <input type="hidden" name="id" value="{{ $data->policy_id }}">
                    @endforeach
                    <textarea name="policy_content" cols="18" rows="18" class="wysihtml5 wysihtml5-default form-control" placeholder="Enter text here ..." @can('Create Policy') @else disabled @endcan >
                        @foreach ($content as $data)
                        {{ $data->policy_content }}
                        @endforeach

                    </textarea>
                    </div>

                    @can('Create Policy')
                    <div class="text-right">

                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner">Save Changes</button>
                    </div>
                    @else @endcan


                </form>


         </div>
         {{--  end policies  --}}
        </div>
    </div>
</div>
<!-- /content area -->
<!-- /main content -->
</x-app-layout>


<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/editors/wysihtml5/wysihtml5.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/editors/wysihtml5/toolbar.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/editors/wysihtml5/parsers.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/editors/wysihtml5/locales/bootstrap-wysihtml5.ua-UA.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/notifications/jgrowl.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/editor_wysihtml5.js') }}"></script>
<!-- /theme JS files -->

{{-- <script type="text/javascript" src="{{ asset('assets/js/plugins/editors/summernote/summernote.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/editor_summernote.js') }}"></script> --}}
