<x-app-layout>
    <title>{{ env('APP_NAME') }} | Permissions</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('permissions.index') }}"><i class="icon-home2 position-left"></i> PERMISSIONS</a></li>
                <li class="active">View All Permissions</li>
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
            <div class=" panel-body table-responsive">

                <span class=" text-danger">@error('permission_name') {{ $message }} @enderror</span>
                <div class=" col-md-12 table-header form-group">
                    <h4 class=" text-center">REGISTERED ROLE PERMISSIONS</h4>
                </div>
                <div class=" form-group text-right">
                    <button type="submit" class=" btn btn-success" data-toggle="modal" data-target="#add_role">Add permission <i class=" icon-plus2"></i></button>
                </div>
                <div class=" form-group col-md-12">
                    <table data-table-paginated class="table datatable-button-html5-columns table-responsive table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class=" text-bold">#</th>
                            <th class=" text-bold">PERMISSION NAME</th>
                            <th class=" text-bold text-center" >ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i=1;
                        @endphp
                        @foreach ($permissions as  $item)
                        <tr>
                            <td>{{ $i}}</td>
                            <td >{{ $item->name }}</td>
                            <td class=" text-center">
                                <a data-toggle="modal" data-target="#edit_permission{{ $item->id }}" class=" btn btn-primary"><i class=" icon-pencil"></i></a> ||
                                <a data-toggle="modal" data-target="#delete_permission{{ $item->id }}" class=" btn btn-danger"><i class=" icon-bin"></i></a>
                            
                            </td>
                            

                            {{-- edit role modal --}}
                            <div id="edit_permission{{ $item->id }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Edit user permissions.</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('permissions.update',['permission' => $item->id]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class=" form-group">
                                                    <label>permission name<i class=" text-danger">*</i></label>
                                                    <input type="text" name="permission_name" class=" form-control" placeholder=" Permission Name" value="{{ $item->name }}">
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
                            <!-- /edit role modal -->

                            {{-- delete modal --}}
                            <div id="delete_permission{{ $item->id }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-md">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Are you sure you whant to delete this permission ?</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('permissions.destroy', ['permission' => $item->id]) }}" method="POST">
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
                            {{-- DELETE --}}

                        </tr>
                        @php
                        $i++;
                        @endphp
                        @endforeach
                    </tbody>
                </table>
                </div>
                <div class=" form-group col-md-12 text-right">
                    {{ $permissions->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
    <!-- /dashboard content -->
    {{-- add role modal --}}
    <form data-action="{{ route('permissions.store') }}" method="POST" id="add_pamission" enctype="multipart/form-data">
        @csrf
        <div id="add_role" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-blue" >
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title">Add user permissions.</h5>
                    </div>
                    <div class="modal-body">

                        <div class="">
                            <div class=" form-group">
                                <label>permission name<i class=" text-danger">*</i></label>
                                <input type="text" id="permission_name" name="permission_name" class=" form-control" placeholder=" permission Name" value="{{ old('permission_name') }}">
                                <span class=" text-danger text-error permission_name_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner" ><span class="ladda-label">Add-permission</span></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{--  end  --}}
</x-app-layout>


<script>
    $(document).ready(function(){
        // fetchPermissions(); disabled from fetching data
        function fetchPermissions(){
            $.ajax({
                type:"GET",
                url:"/fetch-permissions",
                dataType:"json",
                success:function(response){
                    console.log(response.permissions);
                    $.each(response.permissions,function(key,item){
                        $('tbody').append('<tr>\
                            <td>'+item.updated_at+'</td>\
                            <td>'+item.name+'</td>\
                            <td>'+item.created_at+'</td>\
                            <td class="text-center">\
                                <ul class="icons-list">\
                                    <li class="dropdown">\
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">\
                                            <i class="icon-menu4"></i>\
                                        </a>\
                                        <ul class="dropdown-menu dropdown-menu-right">\
                                            <li>\
                                                <a  data-toggle="modal" data-target="#edit_permission'+item.id+'" class="text-primary">Edit Permission <i class=" icon-database-edit2"></i></a>\
                                            </li>\
                                            <li>\
                                                <a href=""  data-toggle="modal" data-target="#delete_permission'+item.id+'"  class="text-danger"> Delete Permission <i class=" icon-bin"></i></a>\
                                                <a   class="text-danger btn">  </a>\
                                            </li>\
                                        </ul>\
                                    </li>\
                                </ul>\
                            </td>\
                            </tr>');
                    });

                }
            })
        }

var form = '#add_pamission';
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
            $('#add_pamission').find('span.error-text').text('');
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
        }
        }
    });
});

});
</script>
