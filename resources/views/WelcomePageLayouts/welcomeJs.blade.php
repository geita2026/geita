
<!-- Theme JS files -->
<script type="text/javascript" src="{{ asset('assets/js/plugins/ui/fab.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/ui/prism.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/extra_fab.js') }}"></script>

<script type="text/javascript" src="{{ asset('assets/js/plugins/loaders/pace.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/loaders/blockui.min.js') }}"></script>
<!-- /core JS files -->
<!-- Theme JS files -->
<script type="text/javascript" src="{{ asset('assets/js/core/app.js') }}"></script>

<script type="text/javascript" src="{{ asset('assets/js/plugins/buttons/spin.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/buttons/ladda.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/pages/components_buttons.js') }}"></script>
<!-- /theme JS files -->

<script>
$(document).ready(function(){
var form = '#CreateAccount';
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
            $('#CreateAccount').find('span.text-error').text('');
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
            $('#NewAccount').modal('toggle');
        }
        }
    });
});

$('#myForm').one('submit', function() {
    $(this).find('input[type="submit"]').attr('disabled', 'disabled');
});

});

$div.velocity('transition.slideUpIn', {
	stagger: 500
});
</script>



{{-- validate inputs --}}






<script>
    // Set the date we're counting down to
    var countDownDate = new Date("jun 28, 2024 00:00:00").getTime();

    // Update the count down every 1 second
    var x = setInterval(function() {

      // Get today's date and time
      var now = new Date().getTime();

      // Find the distance between now and the count down date
      var distance = countDownDate - now;

      // Time calculations for days, hours, minutes and seconds
      var days = Math.floor(distance / (1000 * 60 * 60 * 24));
      var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      var seconds = Math.floor((distance % (1000 * 60)) / 1000);

      // Output the result in an element with id="demo"
      document.getElementById("time_remained").innerHTML = days + " Days Remained";

      // If the count down is over, write some text
      if (distance < 0) {
        clearInterval(x);
        document.getElementById("time_remained").innerHTML = "EVENT OVER";
      }
    }, 1000);
    </script>


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


{{-- validate Inputs --}}
<script>
    $(function() {
    @if ($errors->any())
             $(document).ready(function() {
        new PNotify({
            title: 'Ooops!',
            text: '@foreach ($errors->all() as $error) {{ $error }} , @endforeach',
            icon: 'icon-warning2',
            type: 'error',
        });
        });
    @endif

    });
</script>

{{-- end --}}

    <script type="text/javascript" src="{{ asset('assets/js/plugins/notifications/pnotify.min.js') }}"></script>

    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
        }
    </script>

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>