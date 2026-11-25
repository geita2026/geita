<x-app-layout>
    <title>{{ env('APP_NAME') }} | Pavilions</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home position-left"></i>Home</a></li>
                <li class="active">pavilion Category registration</li>
            </ul>

            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->

    <!-- Content area -->
    <div class="content">

        <div class="panel panel-flat">
            <div class=" panel-body">

                @foreach ($categories as $item)
                    <form action="{{ route('spaceCategories.update', ['spaceCategory' => $item->category_id]) }}"
                        method="POST">
                        @method('PUT')
                        @csrf
                        <div class=" row form-group">
                            <div class="  col-md-6">
                                <label>Space Category Name<i class=" text-danger">*</i></label>
                                <input type="text" name="category_name" class=" form-control input-sm"
                                    placeholder="Category Name" value="{{ $item->category_name }}">
                                <span class="text-danger"> @error('category_name')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class=" col-md-6" id="booths">
                                <label> Cost (per 1msq)<i class="text-danger">*</i></label>
                                <input type="text" name="cost_one_msq" class=" form-control input-sm"
                                    placeholder="Cost per one msq" value="{{ $item->cost_one_msq }}">
                                <span class="text-danger"> @error('cost_one_msq')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                        </div>
                        <div class=" row form-group">
                            <div class="  col-md-6">
                                <label>Type Of Currency <i class="text-danger">*</i></label>
                                <select name="type_of_currency" id="currency" class="bootstrap-select input-sm"
                                    data-live-search="true" data-width="100%" placehoder="Select department">
                                    <option value="{{ $item->type_of_currency }}">{{ $item->type_of_currency }}</option>
                                    <optgroup label="Type of Currency">
                                        <option value="TZS"> Tanzania shiling (TZS)</option>
                                        <option value="USD">United Stade Dollar(USD) </option>
                                    </optgroup>
                                </select>
                                <span class="text-danger"> @error('type_of_currecy')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>
                            <div class=" col-md-6">
                                <label>Space Category event <i class="text-danger">*</i></label>
                                <select name="event_name" class="bootstrap-select " data-live-search="true"
                                    data-width="100%" placehoder="Select Event Name">
                                    <option value="{{ $item->category_event }}">{{ $item->event_name }}</option>
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
                                <textarea name="category_descriptions" placeholder="Category descriptions here" class=" form-control">{{ $item->category_descriptions }}</textarea>
                            </div>
                        </div>

                        <div class=" row form-group">
                            <div class=" text-right col-md-12">
                                <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner"
                                    data-style="slide-down"><span class="ladda-label">Update Space Category</span></button>
                            </div>
                        </div>
                    </form>
                @endforeach
            </div>
        </div>
    </div>
    </div>
</x-app-layout>
