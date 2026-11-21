<div class="container" style="position: relative; z-index: 3100; padding-top: 10px;">
    @if (Session::get('success'))
        <div class="alert alert-brand-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i> {{ Session::get('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif (Session::get('fail'))
        <div class="alert alert-brand-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-triangle me-2"></i> {{ Session::get('fail') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif (Session::get('status'))
        <div class="alert alert-brand-info alert-dismissible fade show" role="alert">
            <i class="fa fa-info-circle me-2"></i> {{ Session::get('status') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>
