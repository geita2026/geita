
<x-app-layout>
    <title>{{ env('APP_NAME') }} | Incomes</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> INCOME</a></li>
                <li class="active">View All Income</li>
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
            <div class="panel-heading">
                <h6 class="panel-title text-bold">REGISTERED INCOME</h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                        <li><a data-action="close"></a></li>
                    </ul>
                </div>
            </div>
            <div class=" panel-body table-responsive">
                <div class=" form-group text-right">
                    <button type="submit" class=" btn btn-success" data-toggle="modal" data-target="#income">Add Income <i class=" icon-add"></i></button>
                </div>
                <table class="table datatable-button-html5-columns table-responsive table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class=" text-bold">#</th>
                            <th class=" text-bold">GFS CODE</th>
                            <th class=" text-bold">INCOME NAME</th>
                            <th class=" text-bold">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>

                        @php
                        $i=1;
                        @endphp
                        @foreach ($incomes as  $item)
                        <tr>
                            <td>{{ $i}}</td>
                            <td>{{ $item->gfs_code }}</td>
                            <td>{{ $item->income_name }}</td>
                            <td class="text-center">
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-menu4"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li>
                                                <a  data-toggle="modal" data-target="#edit_income{{ $item->gfs_code }}" class="text-primary">Edit Income <i class=" icon-database-edit2"></i></a>
                                            </li>
                                            <li>
                                                <a href=""  data-toggle="modal" data-target="#delete_permission{{ $item->gfs_code }}"  class="text-danger"> Delete Income <i class=" icon-bin"></i></a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </td>

                            {{-- edit role modal --}}
                            <div id="edit_income{{ $item->gfs_code }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title text-bold text-uppercase">EDIT INCOME DETATAILS</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('income_list.update',['income_list' => $item->gfs_code]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="row">
                                                    <div class="col-md-6 form-group">
                                                        <label>Income GFS code<i class=" text-danger">*</i></label>
                                                        <input type="text"  name="gfs_code" class=" form-control" placeholder="Income GFS Code" value="{{ $item->gfs_code }}">
                                                        <span class=" text-danger text-error gfs_code_error"></span>
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label>Income Name<i class=" text-danger">*</i></label>
                                                        <input type="text" name="income_name" class=" form-control" placeholder="Income Name" value="{{ $item->income_name }}">
                                                        <span class=" text-danger text-error income_name_error"></span>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class=" form-group col-md-6">
                                                        <label>is space fee ?</label>
                                                        <select name="is_space_fee" class=" form-control">
                                                            <option value="{{ $item->is_space_fee }}">@if ($item->is_space_fee==0) NO @else YES @endif</option>
                                                            <option value="0">NO</option>
                                                            <option value="1">YES</option>
                                                        </select>
                                                    </div>
                                                    <div class=" form-group col-md-6">
                                                        <label>INCOME MAPPER</label>
                                                        <select name="is_joint_payment" class=" form-control">
                                                            @if ($item->is_joint_payment==0) 
                                                            <option value="0">DITF COLLECTIONS</option>
                                                             @elseif ($item->is_joint_payment==1)
                                                             <option value="1">OFF FAIR COLLECTION</option>
                                                             @elseif ($item->is_joint_payment==2)
                                                             <option value="2">FUNCTIONAL HALLS COLLECTIONS</option>
                                                              @endif
                                                            <option value="{{ $item->is_joint_payment }}"></option>
                                                            <option value="0">DITF COLLECTIONS</option>
                                                            <option value="2">FUNCTIONAL HALLS COLLECTIONS</option>
                                                            <option value="1">OFF FAIR COLLECTION</option>
                                                            
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class=" row">
                                                    <div class="col-md-12 form-group">
                                                        <label>Income Descriptions<i class=" text-danger">*</i></label>
                                                        <textarea name="description" class=" form-control" placeholder="Income descriptions">{{ $item->description }}</textarea>
                                                        <span class=" text-danger text-error description_error"></span>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                             <div id="delete_permission{{ $item->gfs_code }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Are you sure you whant to delete {{ $item->income_name }} ?</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('income_list.destroy', ['income_list' => $item->gfs_code]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <div class=" text-right">
                                                    <button type="button" data-dismiss="modal" class=" btn btn-secondary">Close</button>
                                                    <button type="submit" class="btn btn-danger ml-20" >Yes, Delete</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </tr>
                        @php
                        $i++;
                        @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- /dashboard content -->
    {{-- add role modal --}}

        <div id="income" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-blue" >
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title text-uppercase">Add New Income</h5>
                    </div>
                    <div class="modal-body">
                        <form data-action="{{ route('add_income') }}" method="POST" id="add_income" enctype="multipart/form-data">
                            @csrf
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Income GFS code<i class=" text-danger">*</i></label>
                                <input type="text"  name="gfs_code" class=" form-control" placeholder="Income GFS Code" value="{{ old('gfs_code') }}">
                                <span class=" text-danger error-text gfs_code_error"></span>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Income Name<i class=" text-danger">*</i></label>
                                <input type="text" name="income_name" class=" form-control" placeholder="Income Name" value="{{ old('income_name') }}">
                                <span class=" text-danger text-error income_name_error"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class=" form-group col-md-6">
                                <label>is space fee ?</label>
                                <select name="is_space_fee" class=" form-control">
                                    <option value="0">NO</option>
                                    <option value="1">YES</option>
                                </select>
                            </div>

                            <div class=" form-group col-md-6">
                                <label>Is Off Fair Service</label>
                                <select name="is_joint_payment" class=" form-control">
                                    <option value="0">NO</option>
                                    <option value="1">YES</option>
                                </select>
                            </div>
                        </div>
                        <div class=" row">
                            <div class="col-md-12 form-group">
                                <label>Income Descriptions<i class=" text-danger">*</i></label>
                                <textarea name="description" class=" form-control" placeholder="Income descriptions"></textarea>
                                <span class=" text-danger text-error description_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span class="ladda-label">Add Income</span></button>
                    </div>
                </form>
                </div>
            </div>
        </div>

    {{--  end  --}}
</x-app-layout>


<script>
    $(document).ready(function(){
        var form = '#add_income';
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
                    $('#add_income').find('span.error-text').text('');
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
                        $('#income').modal('toggle');
                        $('table').load(document.URL +  ' table'); // reload table
                    }
                }
            });
        });
    });
</script>
