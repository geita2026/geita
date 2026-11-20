@php
    $i=0;
@endphp
@foreach ($myCompany as $item)
@php
    $i=$item->isAcceptedTerms;
@endphp
@endforeach
    @if ($i == 0)
        <div class=" alert alert-warning">Welcome, <b>{{ Auth::user()->first_name }}
                {{ Auth::user()->last_name }}</b> To online Self-Service. To proceed with any of the online services you
            are
            required to Confirm your company information and update it carefully! to check your company information <a
                href="{{ url('profiles') }}">Click here</a>
        </div>
    @else
    <div class=" alert alert-success" >
        <p >Welcome <b>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</b> 
           Now you are eligible to access all online services provided.</p>
           
    </div>
    @endif


<div id="termsAndCondtion" class="modal fade" aria-modal="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-blue">
                <button type="button" class="close" data-dismiss="modal">&times;</button>

            </div>
            <div class="modal-body table-responsive">
                <iframe src="{{ env('APP_URL') }}/policies" width="900" height="400" frameborder="0"></iframe>
            </div>
            <form action="{{ route('companies.store') }}" method="POST">
                @csrf
                <input type="hidden" name="terms" value="1">
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger btn-ladda btn-ladda-spinner"><span
                            class="ladda-label">I Confirm that I agree Terms & Condition</span></button>
                </div>
            </form>
        </div>
    </div>
</div>
