<x-app-layout>
    <!-- dashboard -->
    <title>{{ env('APP_NAME') }} | Space Category</title>
    <div class="page-header">
        <div class="page-header-content">

            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home position-left"></i>Home</a></li>
                <li class="active">Space category registration</li>
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
                <h6 class="panel-title text-bold">REGISTERED SPACES CATEGORY</h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                        <li><a data-action="close"></a></li>
                    </ul>
                </div>
            </div>
            <div class=" panel-body">
                <div class=" form-group">
                    <form action="{{ route('spaceCategories.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class=" row">
                            <div class=" form-group col-md-6">
                                <label>Space Category Name<i class=" text-danger">*</i></label>
                                <input type="text" name="category_name" class=" form-control"
                                    placeholder="Category Name" value="{{ old('category_name') }}">
                                <span class="text-danger"> @error('category_name')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class=" form-group col-md-6">
                                <label>Cost per 1 msq<i class="text-danger">*</i></label>
                                <input type="text" name="cost_one_msq" class=" form-control"
                                    placeholder="Cost per 1msq" value="0">
                                <span class="text-danger"> @error('cost_one_msq')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>
                        <div class=" row">
                            <div class=" form-group col-md-6">
                                <label>Type Of Currency <i class="text-danger">*</i></label>
                                <select name="type_of_currency" id="currency" class=" form-control"
                                    >
                                    <option value="TZS">Tanzania shiling (TZS)</option>
                                        <option value="TZS"> Tanzania shiling (TZS)</option>
                                        <option value="USD">United Stade Dollar(USD) </option>
                                </select>
                                <span class="text-danger"> @error('type_of_currecy')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class=" form-group col-md-6">
                                <label>Space Category event <i class="text-danger">*</i></label>
                                <select name="event_name" class="bootstrap-select " data-live-search="true"
                                    data-width="100%">
                                    @foreach ($events as $event)
                                        <option value="{{ $event->event_id }}">{{ $event->event_name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger"> @error('event_name')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>
                        <div class=" row form-group">
                            <div class=" col-md-12">
                                <textarea name="category_descriptions" placeholder="Category descriptions here" class=" form-control"></textarea>
                            </div>
                        </div>
                        <div class=" row form-group">
                            <div class="  col-md-12">
                                <label>Space Category Poster<i class=" text-danger">*</i></label>
                                <input type="file" name="categoryPoster" class=" form-control"
                                    placeholder="Category Name" value="{{ old('category_name') }}">
                                <span class="text-danger"> @error('categoryPoster')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class=" col-md-6">
                                
                            </div>
                        </div>


                        <div class=" text-right col-md-12 form-group">
                            <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"><span
                                    class="ladda-label">Register Space Category</span></button>
                        </div>
                </div>
                </form>
            </div>

        </div>
    </div>
    </div>
</x-app-layout>
