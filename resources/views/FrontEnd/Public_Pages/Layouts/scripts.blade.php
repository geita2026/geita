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


<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/widgets.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/effects.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/extensions/mousewheel.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/globalize/globalize.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/globalize/cultures/globalize.culture.de-DE.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery_ui/globalize/cultures/globalize.culture.ja-JP.js') }}"></script>


<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/wizards/stepy.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/select2.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jasny_bootstrap.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/validation/validate.min.js') }}"></script>


	<script type="text/javascript" src="{{ asset('assets/js/pages/wizard_stepy.js') }}"></script>

	<script type="text/javascript" src="{{ asset('assets/js/plugins/ui/ripple.min.js') }}"></script>
	<!-- /theme JS files -->


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

    <!-- Theme JS files -->
	
	
	<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/bootstrap_multiselect.js') }}"></script>

	<script type="text/javascript" src="{{ asset('assets/js/pages/form_multiselect.js') }}"></script>
	<!-- /theme JS files -->