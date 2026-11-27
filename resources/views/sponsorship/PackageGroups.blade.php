<x-app-layout>
    <title>{{ env('APP_NAME') }} | Package Groups</title>

    {{-- ===== PAGE HEADER ===== --}}
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> Home</a></li>
                <li><a href="{{ route('package_groups.index') }}"><i class="icon-stack position-left"></i>Package Groups</a></li>
                <li class="active">View All Package Groups</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>

    {{-- ===== CONTENT ===== --}}
    <div class="content">

        {{-- Success / Error messages --}}
        @if (session('success'))
            <div class="alert alert-success alert-styled-left alert-arrow-left">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                <i class="icon-checkmark-circle position-left"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('fail'))
            <div class="alert alert-danger alert-styled-left alert-arrow-left">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                <i class="icon-warning position-left"></i> {{ session('fail') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-styled-left alert-arrow-left">
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ===== TOOLBAR CARD (Search + Add Button) ===== --}}
        <div class="panel panel-white">
            <div class="panel-body">
                <div class="row" style="display:flex; align-items:center; flex-wrap:wrap; gap:10px;">

                    {{-- Search form (inline) --}}
                    <div class="col-md-8" style="flex:1 1 600px;">
                        <form action="{{ route('package_groups.index') }}" method="GET"
                              style="display:flex; align-items:center; flex-wrap:nowrap; gap:8px; margin:0;">
                            <input type="search" id="group-search" name="search"
                                   class="form-control"
                                   style="flex:1 1 260px; min-width:220px;"
                                   placeholder="Search package group by name or description"
                                   value="{{ request('search') }}">

                            <button type="submit" class="btn btn-primary" style="flex:0 0 auto; white-space:nowrap;">
                                <i class="icon-search4"></i> Search
                            </button>

                            @if (request()->filled('search'))
                                <a href="{{ route('package_groups.index') }}" class="btn btn-default"
                                   style="flex:0 0 auto; white-space:nowrap;">Clear</a>
                            @endif
                        </form>
                    </div>

                    {{-- Add Package Group button --}}
                    <div class="col-md-4 text-right" style="flex:0 0 auto; margin-left:auto;">
                        <a href="{{ route('package_groups.create') }}" class="btn btn-primary" style="white-space:nowrap;">
                            <i class="icon-plus-circle2"></i> Add Package Group
                        </a>
                    </div>

                </div>
            </div>
        </div>

        {{-- ===== PACKAGE GROUPS TABLE CARD ===== --}}
        <div class="panel panel-white">
            <div class="panel-heading">
                <h6 class="panel-title text-uppercase text-bold">
                    <i class="icon-stack position-left"></i> Registered Package Groups
                </h6>
                <div class="heading-elements">
                    <span class="label label-info label-rounded">{{ $packageCategories->count() }} groups</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover datatable-save-state">
                    <thead>
                        <tr class="text-bold text-uppercase">
                            <th style="width:50px">#</th>
                            <th>Package Group Name</th>
                            <th>Description</th>
                            <th class="text-center" style="width:100px">Preview</th>
                            <th class="text-center" style="width:180px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($packageCategories as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>

                                <td class="text-bold">
                                    {{ $item->packageGroupName }}
                                </td>

                                <td>
                                    {{ Str::limit($item->packageGroupDescription, 120, '...') }}
                                </td>

                                {{-- Color preview --}}
                                <td class="text-center">
                                    <span class="label label-block"
                                          style="background-color: {{ $item->backgroundColor ?? '#cccccc' }};
                                                 color: {{ $item->textColor ?? '#000000' }};
                                                 padding: 6px 10px;">
                                        {{ $item->packageGroupName }}
                                    </span>
                                </td>

                                {{-- Actions --}}
                                <td class="text-center">
                                    <a href="{{ route('package_groups.edit', ['package_group' => $item->packageGroupId]) }}"
                                       class="btn btn-primary btn-xs">
                                        <i class="icon-pencil7"></i> Edit
                                    </a>

                                    <a href="#" data-toggle="modal"
                                       data-target="#deleteGroup{{ $item->packageGroupId }}"
                                       class="btn btn-danger btn-xs">
                                        <i class="icon-bin"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    <i class="icon-info22 position-left"></i>
                                    No package groups found.
                                    <a href="{{ route('package_groups.create') }}" class="text-primary text-bold">
                                        Add the first one
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- DELETE MODALS — NJE YA TABLE --}}
    {{-- ============================================================ --}}
    @foreach ($packageCategories as $item)
        <div id="deleteGroup{{ $item->packageGroupId }}" class="modal fade" aria-modal="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title text-uppercase">
                            Delete <b>{{ $item->packageGroupName }}</b>?
                        </h5>
                    </div>
                    <div class="modal-body">
                        <p>
                            <code>Note!:</code> This action cannot be undone.
                            If this group is used by any sponsorship package, deletion will be blocked.
                        </p>
                        <form action="{{ route('package_groups.destroy', ['package_group' => $item->packageGroupId]) }}"
                              method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="text-right">
                                <button type="button" data-dismiss="modal" class="btn btn-default">Cancel</button>
                                <button type="submit" class="btn btn-danger ml-20">Yes, Delete</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

</x-app-layout>