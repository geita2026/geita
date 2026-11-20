<x-app-layout>
    <title>{{ env('APP_NAME') }} | Associate Company</title>
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
        <ul class="breadcrumb">
            <li><a href="{{ route('companies.index') }}"><i class=" icon-office position-left"></i>UPDATE ASSOCIATE COMPANIES</a></li>
            <li class="active">Update associate companies</li>
        </ul>
        <ul class="breadcrumb-elements">
           @include('layouts.help')
        </ul>
    </div>
</div>

<div class="content">
    <div class="panel panel-white">

        <div class=" panel-body">

            <div class="form-group row">

                <div class=" col-md-6">
                    @foreach ($associates as $item)
                    <a href="{{ route('associate_companies.show',['associate_company'=>$item->associate_company]) }}" class=" btn border-primary"> <i class=" icon-arrow-left12"></i> Back</a>
                    @endforeach

                </div>

                <div class="col-md-6 text-right">

                    <ul class="icons-list">

                        <li class="dropdown">

                            <a href="#" class="dropdown-toggle text-primary text-bold btn btn-default border-success" data-toggle="dropdown">

                                Actions <i class="icon-menu4"></i>

                            </a>

                            <ul class="dropdown-menu dropdown-menu-right">

                                <li>

                                    <a onclick="#" class=" text-primary">Prview Details<i class=" icon-eye2"></i></a>

                                </li>

                                <li>

                                    <a   class=" text-danger"  title="Delete Company" data-toggle="modal" data-target="#delete_company"><i class=" icon-bin2"></i> Delete Company </a>

                                </li>


                            </ul>

                        </li>

                    </ul>

                </div>

            </div>



            @foreach ($associates as $item)

            <form  action="{{ route('companies.update',['company'=>$item->companyID]) }}" method="POST" class="stepy-clickable">

                @csrf

                @method('PATCH')
                <input type="hidden" name="associate_company" value="{{ $item->associate_company }}">
                @include('company.EditCompany')

            </form>



            <div id="delete_company" class="modal fade" aria-modal="true">

                <div class="modal-dialog modal-md">

                    <div class="modal-content">

                        <div class="modal-header bg-danger">

                            <h4 class="modal-title text-center text-uppercase">DELETE <b>{{ $item->companyName}}</b> Company</h5>

                            </div>

                            <div class=" modal-body">

                                <form action="{{ route('companies.destroy',['company'=>$item->companyID]) }}" method="post" id="company">

                                    @csrf

                                    @method('DELETE')

                                </div>

                                <div class=" modal-footer">

                                    <div class=" text-right">

                                        <button class=" btn btn-default border-warning text-warning" data-dismiss="modal"> Close</button>

                                        <button type="submit" class="btn btn-warning  btn-ladda btn-ladda-spinner" ><span class="ladda-label">Delete Company</span></button>

                                    </div>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>



                @endforeach

            </div>

        </div>
</div>
</x-app-layout>

<script>
    $("#country").change(function() {
        if ( $(this).val() == "Tanzania") { //for functional Halls

            $("#region").show();
        }
        else
        {
            $("#region").hide();

        }
});

</script>
