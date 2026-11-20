<x-app-layout>




    <title>{{ env('APP_NAME') }} | Companies</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('companies.index') }}"><i class=" icon-office position-left"></i>COMPANIES</a></li>
                <li class="active">View All Companies</li>
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

            <div class="panel-body table-responsive">
                <span class=" text-danger">
                    @error('company_file')
                        {{ $message }}
                    @enderror
                </span>
                <div class=" row">
                    <div class=" col-md-12 form-group table-header">
                        <h4 class=" text-center">LIST OF REGISTERED COMPANIES</h4>
                    </div>

                    <div class=" form-group col-md-12">
                        <a href="{{ route('stickers.index') }}" class=" btn btn-default text-primary"><i class=" icon-arrow-left12"></i> Back</a>
                    </div>
                   
                </div>
                <div class="form-group">
                    <table class=" table table-bordered table-striped">
                        <thead>

                            <tr>
                                <th colspan="7">

                                    <div class=" form-group">
                                        <form data-action="{{ route('search_companies') }}" id="searcheEvent"
                                            method="POST">
                                            @csrf
                                            <div class="form-group has-feedback">
                                                <input type="text" id="search" name="search"
                                                    class="form-control input-sm"
                                                    placeholder="Search company from  {{ number_format($total_companies) }} companies"
                                                    id="search">
                                                <div class="form-control-feedback">
                                                    <i class=" icon-search4"></i>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th class=" text-bold text-center">#</th>
                                <th class=" text-bold text-center">TSRN</th>
                                <th class=" text-bold text-center">COMPANY NAME</th>
                                <th class=" text-bold text-center">COMPANY PHONE</th>
                                <th class=" text-bold text-center">COMPANY EMAIL</th>
                                <th class=" text-bold text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1; @endphp
                            @foreach ($companies as $item)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $item->registrationID }}</td>
                                    <td class=" text-uppercase"><a
                                            href="{{ env('APP_URL') }}/stickers/{{ $item->companyID }}"
                                            title="Click to update company">{{ $item->companyName }}</a></td>
                                    <td>{{ $item->companyPhone }}</td>
                                    <td>{{ $item->companyEmail }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('stickers.show', ['sticker' => $item->companyID]) }}"
                                            class=" btn btn-primary"> Go to stickers <i class=" icon-arrow-right13"></i></a>
                                       
                                    </td>
                                </tr>
                                @php $i++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                   
                </div>
                <div class=" mb-10 text-right form-group">
                    {{ $companies->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
<script>
    $(document).ready(function() {
        var form = '#searcheEvent';
        $('#searcheEvent').on('keyup', function() {
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
                            <td>` + (i + 1) + `</td>
                            <td>` + response.data[i].registrationID + `</td>
                            <td class=" text-uppercase">
                                <a href="{{ env('APP_URL') }}/stickers/` + response.data[i].companyID+`" title="Click to update company">
                                    ` + response.data[i].companyName + `
                                </a>
                            </td>
                            <td>` + response.data[i].companyName + `</td>
                            <td>` + response.data[i].companyPhone + `</td>
                            <td>` + response.data[i].companyEmail + `</td>
                            <td class="text-center">
                                <a href="{{ env('APP_URL') }}/stickers/` + response.data[i].companyID + `" class=" btn btn-primary">Go to Stickers <i class=" icon-arrow-right13"></i></a>
                            </td>

                            </tr>`;
                            }
                            $('tbody').html(htmlView);
                        }
                    }
                }
            });
        });

    });
</script>

