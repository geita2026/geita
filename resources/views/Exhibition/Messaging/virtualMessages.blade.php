<x-app-layout>

    <title>{{ env('APP_NAME') }} | VirtualPavilion</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
    <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="#"><i class=" icon-envelop position-left"></i> MESSAGING</a></li>
            <li class="active">View All Messages</li>
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
        <div class=" col-md-12 form-group">
            <!-- Messages -->
    <div class="timeline-row">
        <div class="panel panel-flat timeline-content">
            <div class="panel-heading">
                @foreach ($company ?? $companies as $data)
                @endforeach
                <h6 class="panel-title"><b style=" font-size:90%" class=" text-primary">
                  <i class=" icon-envelop"></i>  {{ Illuminate\Support\Str::limit($data->companyName,40) }}
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

            <div class=" p-20">
                <input type="text" class="form-control" placeholder="Change user chating with . . . ."  id="ChartWith">
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

                   @if ($item->message_to==Auth::user()->uuid)
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
                                <form action="{{ route('messages.destroy',['message'=>$item->message_id]) }}" method="POST">
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
                <form data-action="{{ route('messages.store') }}" method="POST" id="SendMessage" enctype="multipart/form-data">
                    @csrf
                    <div class=" form-group">
                        <textarea name="message" class="form-control" rows="3" cols="1" placeholder="Enter your message...">{{ old('message') }}</textarea>
                        <span class="text-danger message_error"></span>
                    </div>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <div class="col-lg-10">
                                <input type="file" name="attachment_file" class="file-input" data-show-caption="false" data-show-upload="false">
                                <span class="text-danger attachment_file_error"></span>
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

</x-app-layout>


<script>
    $(document).ready(function(){
   var form = '#SendMessage';
   $(form).on('submit', function(event){
       event.preventDefault();
       var url = $(this).attr('data-action');
       $.ajax({
           url: url,
           method: 'POST',
           data: new FormData(this),
           dataType: 'JSON',
           contentType: false,
           cache: false,
           processData: false,
           beforeSend:function(){
               $('#SendMessage').find('span.text-danger').text('');
           },
           success:function(response)
           {
               if(response.status==0)
               {
                   $.each(response.error, function(prefix,val){
                       $(form).find('span.'+prefix+'_error').text(val[0]);
                   });
               }
               else
               {
                $(document).ready(function() {
               new PNotify({
               title: 'Congratulations!',
               text: response.success,
               icon: 'icon-info3',
               type: 'success'
               });

                });
               $('#add_role').modal('toggle');

               setTimeout(() => {
                   document.location.reload();
               },1000);



           }
           }
       });
   });

   });
   </script>

<!-- Theme JS files -->
<script type="text/javascript" src="{{ asset('assets/js/plugins/uploaders/fileinput.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/uploader_bootstrap.js') }}"></script>
<!-- /theme JS files -->

<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/widgets.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/effects.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/extensions/mousewheel.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/globalize/globalize.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/globalize/cultures/globalize.culture.de-DE.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/globalize/cultures/globalize.culture.ja-JP.js') }}"></script>

<script>
     var projects = [
       @foreach ($companies as $key)
       {
        companyID:"{{ $key->companyID }}",
        label: "{{ Illuminate\Support\Str::limit($key->companyName,40) }}",
        desc: "<i></i>",
       },
       @endforeach
    ];
     // Initialize autocomplet
     $("#ChartWith").autocomplete({
        minLength: 0,
        source: projects,
        focus: function( event, ui ) {
            $("#ChartWith").val(ui.item.label);
            return false;
        },
        a: function( event, ui ) {
            $("#ChartWith").val(ui.item.label);
            return false;
        }
    })
    .autocomplete("instance")._renderItem = function(ul, item) {
        // return $("<li class='bg-teal-400'>").append('<a href="/company_profiles/'+ item.companyID +'" class="btn border-white" style="text-align:left;">' +  "<span class=' text-bold' style='font-size:130%'>"+ item.label+ '</span>' + '<span class=" text-size-small"> <br>' + item.desc +'</span></li>' + '</a>').appendTo(ul);

        return $("<li class='bg-teal-400'>").append('<a href="/messages/'+ item.companyID +'" class="btn border-white" style="text-align:left;">' +  "<span class=' text-bold' style='font-size:150%'>"+ item.label+ '</span>' + '<span class=" text-size-small"> <br>' + item.desc +'</span></li>' + '</a>').appendTo(ul);
    };
</script>


<style>
    .aa { display: none; }
.show { display: block; }
.button { text-transform: uppercase; }
.button:hover { cursor: pointer; background-color: #fffff0; }
</style>


<script>

document.getElementById('test')="ok";

</script>


