
<link rel="icon" href="{{ asset('images/nembo.png') }}" type="image/gif" sizes="16x16">
    <link href="{{ asset('assets/css/icons/icomoon/styles.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/core.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/components.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/colors.css') }}" rel="stylesheet" type="text/css">
    <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/gif" sizes="16x16">
<div class="content">

<div class="panel panel-flat">

    <div class=" panel-body">

        <div class=" col-md-12 form-group">

            <!-- Messages -->
    <div class="timeline-row">

        <div class="panel panel-flat timeline-content">
            <div class="panel-heading">
                @foreach ($company ?? $companies as $data)
                @endforeach
                <h6 class="panel-title"><b style=" font-size:90%" class=" text-primary">
                  <i class=" icon-envelop"></i>  {{ Illuminate\Support\Str::limit($data->companyName ?? 'Null',40) }}
                </b></h6>
                <div class="heading-elements form-group">
                    <ul class="icons-list">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="icon-circle-down2"></i>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-right">
                                <li><a href="#"><i class="icon-user-lock"></i> Hide user posts</a></li>
                                <li><a href="#"><i class="icon-user-block"></i> Block user</a></li>
                                <li><a href="#"><i class="icon-user-minus"></i> Unfollow user</a></li>
                                <li class="divider"></li>
                                <li><a href="#"><i class="icon-embed"></i> Embed post</a></li>
                                <li><a href="#"><i class="icon-blocked"></i> Report this post</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>

            </div>
            <div class="panel-body">
                <ul class="media-list chat-list content-group">
                    @foreach ($messages as $item)
                    <li class="media reversed">
                        <div class="media-body">
                            @if ($item->message_file!=null)
                            <iframe src="{{ asset('VirtualImages/'.$item->message_file) }}" style="width: 100%;" height="400" width="700"></iframe><br>
                            @endif
                            <div class="media-content">{{ $item->message }}</div>
                            {{-- {{ $item->message_file }} --}}

                            <span class="media-annotation display-block mt-10">{{ date('H:i a',strtotime($item->message_sent_on)) }}<a href="#" data-toggle="modal" data-target="#DeleteMessage{{ $item->message_id }}"><i class=" icon-bin position-right text-muted text-danger"></i></a></span>
                        </div>


                        <div class="media-right">
                            <a href="{{ asset('assets/images/placeholder.jpg') }}">
                                <img src="{{ asset('assets/images/placeholder.jpg') }}" class="img-circle" alt="">
                            </a>
                        </div>
                    </li>

                   @if ($item->message_to==$item->message_from)
                   <li class="media">
                    <div class="media-left">
                        <a href="{{ asset('assets/images/placeholder.jpg') }}">
                            <img src="{{ asset('assets/images/placeholder.jpg') }}" class="img-circle" alt="">
                        </a>
                    </div>

                    <div class="media-body">
                        <div class="media-content">{{ $item->message }}</div>
                        <span class="media-annotation display-block mt-10">{{ date('D, H:i a',strtotime($item->message_sent_on)) }} <a href="#"><i class=" icon-reply position-right text-muted"></i></a>
                        </span>
                    </div>
                    </li>
                   @endif

                    <!-- Delete Message Model -->
                    <div id="DeleteMessage{{ $item->message_id }}" class="modal fade">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <form action="{{ route('chart_session.destroy',['chart_session'=>$item->message_id]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete this message  Message ?</p>
                                        <b><code>Note!</code> This action can not be reversed!</b>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-danger btn-ladda btn-ladda-spinner">Delete Message</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- End Delete Model message -->

                    @endforeach
                </ul>

                {{-- Form to send message --}}
                <form action="{{ route('chart_session.store') }}" method="POST" id="SendMessage" enctype="multipart/form-data">
                    @csrf
                    <div class=" form-group">
                        <textarea name="message" class="form-control" rows="3" cols="1" placeholder="Enter your message...">{{ old('message') }}</textarea>
                        <span class="text-danger"> @error('message') {{ $message }} @enderror </span>
                    </div>
                <div class="row">
                    <input type="hidden" name="sendMessage" value="sendMessage">
                    <input type="hidden" name="message_to" value="{{ $item->message_to ?? '' }}">
                    <input type="hidden" name="message_from" value="{{ $item->message_from ?? '' }}">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <div class="col-lg-10">
                                <input type="file" name="attachment_file" class="file-input" data-show-caption="false" data-show-upload="false">
                                <span class="text-danger">@error('attachment_file') {{ $message }} @enderror</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6 text-right">
                        <button type="submit" class="btn bg-teal-400 btn-labeled btn-labeled-right "><b><i class="icon-circle-right2"></i></b> Send</button>
                    </div>
                    {{-- btn-ladda btn-ladda-spinner --}}
                </div>
                </form>
                {{-- End form to send message --}}

            </div>
        </div>
    </div>
    <!-- /messages -->
        </div>
    </div>
</div>
</div>

<!-- Core JS files -->
<script type="text/javascript" src="{{ asset('assets/js/plugins/loaders/pace.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/loaders/blockui.min.js') }}"></script>
<!-- /core JS files -->
  <!-- Theme JS files -->
  <script type="text/javascript" src="{{ asset('assets/js/core/app.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/js/plugins/ui/ripple.min.js') }}"></script>

 {{-- spine button script  datatables--}}
 <script type="text/javascript" src="{{ asset('assets/js/plugins/buttons/spin.min.js') }}"></script>
 <script type="text/javascript" src="{{ asset('assets/js/plugins/buttons/ladda.min.js') }}"></script>
 <script type="text/javascript" src="{{ asset('assets/js/pages/components_buttons.js') }}"></script>

{{--  datatable  --}}
 <script type="text/javascript" src="{{ asset('assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/select2.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/plugins/tables/datatables/extensions/buttons.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/pages/datatables_extension_buttons_html5.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/pages/components_popups.js') }}"></script>

<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/bootstrap_select.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/form_bootstrap_select.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/datatables_basic.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/datatables_api.js') }}"></script>
    {{--  end datatables  --}}
 <!-- /spine button script -->


<script type="text/javascript">

    $(document).ready(function () {

        window.setTimeout(function() {
            $().fadeTo(1000, 0).slideUp(1000, function(){
                $(this).remove();
            });
        }, 6000);

    });
</script>
<script type="text/javascript" src="{{ asset('assets/js/pages/layout_fixed_native.js') }}"></script>

<script type="text/javascript" src="{{ asset('assets/js/plugins/ui/fab.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/ui/prism.min.js') }}"></script>
{{-- <script type="text/javascript" src="{{ asset('assets/js/pages/extra_fab.js') }}"></script> --}}

<script type="text/javascript" src="{{ asset('assets/js/plugins/notifications/bootbox.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/notifications/sweet_alert.min.js') }}"></script>


<script>
    $(function() {
    @if(Session::get('success'))
             $(document).ready(function() {
        new PNotify({
            title: 'OK',
            text: '{{ session::get("success") }}',
            icon: 'icon-check',
            type: 'success'
        });
        });
    @endif

    @if(Session::get('fail'))
             $(document).ready(function() {
        new PNotify({
            title: 'Ooops!',
            text: '{{ session::get("fail") }}',
            icon: 'icon-x',
            type: 'error',
        });
        });
    @endif

    });
    </script>
    <script type="text/javascript" src="{{ asset('assets/js/plugins/notifications/pnotify.min.js') }}"></script>


