@foreach ($packageCategories as  $item)
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
                <li class="active">Update Package Group</li>
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
                <h4 class="panel-title text-bold">UPDATE PACKAGES GROUP</h4>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                        <li><a data-action="close"></a></li>
                    </ul>
                </div>
            </div>
            <div class=" panel-body table-responsive">
                <form action="{{ route('package_groups.update',['package_group'=>$item->packageGroupId]) }}" method="POST">
                    @csrf
                    @method('put')
                    <div class=" form-group col-md-12">
                        <label>Package Group Name<i class=" text-danger">*</i></label>
                        <input type="text" name="packageGroupName" class=" form-control" value="{{ $item->packageGroupName }}">
                        <span class=" text-danger">@error('packageGroupName')
                            {{ $messages }}
                        @enderror</span>
                    </div>

                    <div class=" form-group col-md-12">
                        <label>Background Color<i class=" text-danger">*</i></label>
                        <input type="color" name="backgroundColor" class=" form-control" value="{{ $item->backgroundColor }}">
                        <span class=" text-danger">@error('backgroundColor')
                            {{ $messages }}
                        @enderror</span>
                    </div>


                    <div class=" form-group col-md-12">
                        <label>Text Color<i class=" text-danger">*</i></label>
                        <input type="color" name="textColor" class=" form-control" value="{{ $item->textColor }}">
                        <span class=" text-danger">@error('textColor')
                            {{ $messages }}
                        @enderror</span>
                    </div>

                    <div class=" form-group col-md-12">
                        <label>Package Group Description<i class=" text-danger">*</i></label>
                        <textarea type="color" name="packageGroupDescription" class=" form-control" >{{ $item->packageGroupDescription }}</textarea>
                        <span class=" text-danger">@error('packageGroupDescription')
                            {{ $messages }}
                        @enderror</span>
                    </div>

                    <div class=" form-group col-md-12 text-right">
                        <button type="submit" class=" btn btn-primary ">Save Changes<i class=" icon-arrow-right13"></i></button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
@endforeach