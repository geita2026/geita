<x-app-layout>
    <title>{{ env('APP_NAME') }} | Sponsorship</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> Home</a></li>
                <li class="active">View All Package Group</li>
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
                <h4 class="panel-title text-bold">REGISTERED PACKAGES GROUP LIST</h4>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                        <li><a data-action="close"></a></li>
                    </ul>
                </div>
            </div>
            <div class=" panel-body table-responsive">
                <table class="table datatable-save-state table-responsive table-bordered table-striped">
                    <thead >
                        <tr>
                            <th class=" text-bold">S/N</th>
                            <th class=" text-bold">Package Group Name</th>
                            <th class=" text-bold">Package Group Description</th>
                            <th class=" text-bold text-center" colspan="3">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i=1;
                        @endphp
                        @foreach ($packageCategories as  $item)
                        <tr>
                            <td>{{ $i}}</td>
                            
                            <td style="background-color: {{ $item->backgroundColor }}; color:{{ $item->textColor }}">
                                {{ $item->packageGroupName }}
                            </td>
                            <td>{{ $item->packageGroupDescription }}</td>
                           
                            <td class=" text-center">
                                <a href="{{ route('package_groups.edit',['package_group'=>$item->packageGroupId]) }}" class=" btn btn-primary">Edit <i class=" icon-pencil"></i></a>
                            </td>
                            <td>

                            </td>
                            <td class=" text-center">
                                <a class=" btn btn-danger">Delete <i class=" icon-bin"></i></a>
                            </td>
                            
                            
                           
                            
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
</x-app-layout>
