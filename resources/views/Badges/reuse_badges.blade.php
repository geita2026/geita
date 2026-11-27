<x-app-layout>
    <title>{{ config('app.name') }} | Reuse Badges</title>

    {{-- Page header --}}
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li>
                    <a href="{{ route('prepare_badges.index') }}">
                        <i class="icon-home2 position-left"></i>EXHIBITOR BADGES
                    </a>
                </li>
                <li class="active">Reuse badges</li>
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
            <div class="panel-heading">
                <h5 class="panel-title">Reusable Badges by Company</h5>
            </div>
            <div class="panel-body">

                @if(session('success'))
                    <div class="alert alert-success alert-styled-left">
                        <span class="text-semibold">Success!</span> {{ session('success') }}
                    </div>
                @endif

                @if(session('fail'))
                    <div class="alert alert-danger alert-styled-left">
                        <span class="text-semibold">Error!</span> {{ session('fail') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="width:60px;">#</th>
                                <th>Company Name</th>
                                <th style="width:180px;">Reusable Members</th>
                                <th style="width:150px;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($companies as $index => $company)
                                <tr>
                                    <td>{{ $companies->firstItem() + $index }}</td>
                                    <td>{{ $company->companyName }}</td>
                                    <td>
                                        <span class="label label-info label-rounded">
                                            {{ $reusableCounts[$company->companyID] ?? 0 }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('reuse_badges.show', $company->companyID) }}"
                                           class="btn btn-xs btn-primary">
                                            <i class="icon-eye"></i> View Members
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        <i class="icon-info22"></i> No reusable badges found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($companies->hasPages())
                    <div class="text-right">
                        {{ $companies->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>