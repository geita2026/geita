<x-app-layout>
    <title>{{ env('APP_NAME') }} | JUDGING</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class=" icon-menu10 position-left"></i> Home</a>
                </li>
                <li class="active">View Judging</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- Content area -->
    <div class="content">

        <div class="panel panel-flat">
            <div class=" panel-body">
                <div class=" form-group col-md-12">
                    <p class="text-justify">
                        <b>Internal Judging</b><br>
                        It is an exercise that takes place during every Sabasaba exhibition period to
                        identify three winners from each category and overall winners from all participant
                        categories. The exercise is conducted by a panel of judges from various institutions
                        across Tanzania
                    </p>
                    <p>
                        <b class=" text-danger">Note!</b> Please select the category in which you would like to be
                        evaluated.
                    </p>
                </div>


                <table class="table datatable-save-state table-striped table-bordered  table-responsive">
                    <thead>

                        <tr>
                            <th class=" text-center text-bold">#</th>
                            <th class=" text-center text-bold">INSTITUTION / COMPANY NAME</th>
                            <th class=" text-center text-bold">Company Phone</th>
                            <th class=" text-center text-bold">JUDGING CATEGORY</th>
                            <th class=" text-center text-bold">JUDGING OPTION(s)</th>
                            <th class=" text-bold text-center">Saved At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($exhibitors as $item)
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $item->companyName }}</td>
                                <td>{{ $item->companyPhone }} / {{ $item->personalPhone }}</td>
                                <td>
                                    {{ $item->categoryName }}
                                </td>
                                <td class=" text-center">
                                    @if ($item->judging_status == 1)
                                        <b>Internal Judging</b>
                                    @endif
                                </td>
                                <td>
                                    {{ date('M d, Y', strtotime($item->served_at)) }}
                                </td>
                                <td>
                                    @if (0)
                                    <div class=" alert alert-danger">
                                        <b>Registration Has been closed!</b>
                                    </div>
                                        @else
                                        @if ($item->judging_status != 1)
                                        <a data-toggle="modal" data-target="#judging{{ $item->participation_id }}"
                                            class=" btn btn-primary">Confirm Perticipation in Judging<i
                                                class=" icon-arrow-right13"></i></a>
                                    @else
                                        <a data-toggle="modal" data-target="#judging{{ $item->participation_id }}"
                                            class=" btn btn-success">Change Confirmation <i
                                                class=" icon-arrow-right13"></i></a>
                                    @endif
                                    @endif

                                </td>
                            </tr>

                            <div id="judging{{ $item->participation_id }}" class="modal fade" aria-modal="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h5 class="modal-title">Confirm Perticipation in Judging Process</h5>
                                        </div>
                                        <form
                                            action="{{ route('exhibitor_judging.update', ['exhibitor_judging' => $item->participation_id]) }}"
                                            method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-body">
                                                <div class=" row form-group">
                                                    <div class=" col-md-6">
                                                        <div class="checkbox checkbox-switchery switchery-lg">
                                                            <label>
                                                                <input type="checkbox" name="internalJudging"
                                                                    class="switchery" value="1" required>
                                                                I confirm participation in Internal judging
                                                            </label>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class=" form-group col-md-12">
                                                    <label>Please indicate the location where the judges can find you
                                                        during the exhibition</label>
                                                    <textarea name="judging_location" class=" form-control"
                                                        placeholder="Write your Judging Location Here eg, Karume Hall (KRM-002)" required>{{ $item->judging_location }}</textarea>
                                                    <span class="text-danger"> @error('company_sector')
                                                            {{ $message }}
                                                        @enderror
                                                    </span>
                                                </div>
                                                <div class=" form-group col-md-12">
                                                    <label>Select Judging Category you want to be Judged<i
                                                            class=" text-danger">*</i></label>
                                                    <select name="judgingCategory" class=" form-control" required>
                                                        <option value=""> - - - Select judging Categories - - -
                                                        </option>
                                                        @foreach ($sectors as $sector)
                                                            <option value="{{ $sector->categoryId }}">
                                                                {{ $sector->categoryName }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="text-danger"> @error('judgingCategory')
                                                            {{ $message }}
                                                        @enderror
                                                    </span>
                                                </div>

                                            </div>
                                            <div class="modal-footer">

                                                <button type="submit" class="btn btn-primary">Confirm participation in
                                                    Judging <i class=" icon-arrow-right13"></i></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>

                </table>
            </div>
            <div class=" text-right p-20">
                {{ $exhibitors->links() }}
            </div>
        </div>
    </div>
</x-app-layout>


{{-- Preview modal --}}
<div id="addService" class="modal fade" aria-modal="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-center" id="h2">HOW YOUR PAVILION IS LOOKING</h5>
            </div>
            <div class="modal-body">

                <div class=" row ">

                    <form action="{{ route('virtual_services.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class=" col-md-12 row form-group">
                            <div class=" form-group">
                                <label>Service Name<i class=" text-danger">*</i></label>
                                <input name="service_name" class=" form-control" placeholder="Service Name"
                                    value="{{ old('service_name') }}">
                                <span class=" text-danger">
                                    @error('service_name')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class=" form-group">
                                <label>Service Description<i class=" text-danger">*</i></label>
                                <textarea name="service_descriptions" class=" form-control" rows="5" placeholder=" Service descriptions">{{ old('service_descriptions') }}</textarea>
                                <span class=" text-danger">
                                    @error('service_descriptions')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class="form-group">
                                <label>Service Image Preview<i class=" text-danger">*</i></label>
                                <input type="file" name="service_image_name" class="file-input"
                                    data-browse-class="btn btn-primary" data-remove-class="btn btn-default"
                                    accept="image/*">
                                <span class=" text-danger">
                                    @error('service_image_name')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </div>

                            <div class=" form-group text-right">
                                <button class=" btn btn-primary">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
{{-- Preview modal --}}

<script type="text/javascript" src="{{ asset('assets/js/plugins/uploaders/fileinput.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('assets/js/pages/uploader_bootstrap.js') }}"></script>
<!-- /theme JS files -->
