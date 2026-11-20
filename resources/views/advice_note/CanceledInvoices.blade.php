<x-app-layout>
    <title>{{ env('APP_NAME') }} | Invoice</title>
   {{-- page header --}}
   <div class="page-header">
       <div class="page-header-content">
           @include('layouts.heading-elements')
       </div>
   <div class="breadcrumb-line breadcrumb-line-component">
       <ul class="breadcrumb">
           <li><a href="{{ route('adviceNotes.index') }}"><i class=" icon-file-spreadsheet position-left"></i> ADVICE NOTES</a></li>
           <li class="active">View All Canceled Invoices</li>
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
        <div class=" panel-body">
        <div class=" form-group mt-20 table-responsive">
            <div class=" col-md-12 table-header">
                <h4 class=" p-15 text-center text-default text-bold">LIST OF CANCELED INVOICES</h4>
            </div>
            <table class="table table-bordered datatable-button-html5-columns">
                <thead>
                    
                    <tr>
                        <th>#</th>
                        <th class=" text-bold text-center">INVOICE ID</th>
                        <th class=" text-bold text-center">CUSTOMER NAME</th>
                        <th class=" text-center text-bold">INVOICED AMOUNT</th>
                        <th class=" text-center text-bold">CANCELED ON</th>
                        <th class=" text-center text-bold"> CANCELED BY</th>
                        <th class=" text-center text-bold"> REASONS</th>
                        <th class=" text-center text-bold">INVOICE STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i=1; @endphp
                   @foreach ($advice_note as $key=>$item)
                   <tr>
                    <td>{{ $key+1 }}</td>
                    <td><a href="{{ route('adviceNotes.show',['adviceNote'=>$item->advice_note_id]) }}" class=" text-bold" title="Click here to view {{ $item->advice_note_id }} Advice Note details">{{ $item->advice_note_id }}</a></td>
                    <td>{{ $item->companyName }}</td>
                    <td class=" text-center">{{ number_format($item->total_amount,2) }} {{ $item->currency }}</td>
                   <td class=" text-right">{{ date('d M,Y h:i:s',strtotime($item->canceled_at)) }} </td>
                   <td>{{  $item->first_name }} {{  $item->last_name }}</td>
                   <td>{{ $item->reasons }}</td>


                   <td class=" text-center">
                    @if (Auth::user()->account_type==2 && $item->control_number!=Null)
                    <form action="{{ route('download_invoice') }}" method="POST" >
                        @csrf
                        <input type="hidden" name="id" value="{{ $item->advice_note_id }}">
                        @method('GET')
                        <button type="submit"  class=" btn btn-default border-primary text-primary text-bold" title="Download Invoice"><i class=" icon-download4"></i></button>
                    </form>
                    @else
                    <a href="{{ route('adviceNotes.show',['adviceNote'=>$item->advice_note_id]) }}" class=" text-bold btn btn-default text-primary" title="Click here to view {{ $item->advice_note_id }} Advice Note details">View Invoice <i class=" icon-file-empty2"></i></a>
                    @endif
                   </td>


                </tr>
                @php $i++; @endphp

                   @endforeach
                  
                </tbody>

            </table>
            
                <div class=" text-right">
                    {{ $advice_note->links() }}
                </div>
           

        </div>

            </div>
        </div>
    </div>
    <!-- /content area -->
    <!-- /main content -->
</x-app-layout>

<script>
    $(document).ready(function() {
        var form = '#searchCompany';
        $('#searchCompany').on('keyup', function() {
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
                success: function(response) {
                    var htmlView = '';
                    if (response.status == 1) {
                        if (response.data.length <= 0) {
                            htmlView = +`
                    <tr>
                        <td colspan="4">No data.</td>
                    </tr>`;
                        } else {
                            for (let i = 0; i < response.data.length; i++) {
                                htmlView += `
                        <tr>
                            <td>
                                <a href="{{ env('APP_URL') }}/adviceNotes/` + response.data[i].advice_note_id + `" class=" text-primary text-bold">` + response.data[i].advice_note_id +`</a>
                            </td>
                            <td class=" text-uppercase">
                                <a href="{{ env('APP_URL') }}/adviceNotes/` + response.data[i].advice_note_id + `" title="Click to update company">
                                    ` + response.data[i].companyName + `
                                </a>
                            </td>
                            <td>` + response.data[i].control_number + `</td>
                            <td> ` + new Intl.NumberFormat().format(response.data[i].total_amount) + " " + response.data[i].currency + ` </td>
                            <td class=" text-success text-bold">` + new Intl.NumberFormat().format(response.data[i].amount_paid)   + " " + response.data[i].currency + `</td>
                            <td class="text-warning text-bold">` + new Intl.NumberFormat().format((response.data[i].total_amount-response.data[i].amount_paid))  + " " + response.data[i].currency + `</td>
                            <td class="text-center">
                              <a href="{{ env('APP_URL') }}/adviceNotes/` + response.data[i].advice_note_id + `" class=" text-primary text-bold btn btn-block border-primary">View Invoice <i class=" icon-file-empty2"></i></a>
                            </td></tr>`;
                            }
                            $('tbody').html(htmlView);
                        }
                    }
                }
            });
        });

    })
</script>
