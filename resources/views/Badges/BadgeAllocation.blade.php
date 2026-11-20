<x-app-layout>
    <title>{{ env('APP_NAME') }} | Badges</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i>Home </a></li>
                <li class="active">View All company allocations</li>
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

                {{-- Error or success messages --}}
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <span class=" text-danger">{{ $error }} ,</span>
                    @endforeach
                @endif
                <div class=" form-group table-responsive">
                    <table class="table datatable-save-state table-striped table-bordered  table-responsive">
                        <thead>
                            <tr>
                                <th class=" text-bold text-center">SN#</th>
                                <th class=" text-bold text-center">Company Name</th>
                                <th class=" text-bold text-center">Allocated Badges</th>
                                <th class=" text-bold text-center">Used Badges</th>
                                <th class=" text-bold text-center">Exhibition Year</th>
                                <th class=" text-bold text-center" colspan="2">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($allocations as $key => $item)
                                <tr>
                                    <td class=" text-center">
                                        {{ $key + 1 }}
                                    </td>
                                    <td class=" text-center">
                                        {{ $item->companyName }}
                                    </td>
                                    <td>{{ $item->badges_allocated + $item->free_badges }} </td>
                                    <td>{{ $item->badges_used }}</td>
                                    <td class=" text-center">{{ $item->event_name }}</td>
                                    <td class="text-center">
                                        @if ($item->event_id == session('liveEvent'))
                                            <button data-toggle="modal"
                                                data-target="#badgeAllocation{{ $item->badge_allocation_id }}"
                                                class=" btn btn-primary">Badge Management</button>
                                        @else
                                            <label class="bg-success p-5">ARCHIEVED</label>
                                        @endif
                                    </td>
                                </tr>
                                <div id="badgeAllocation{{ $item->badge_allocation_id }}" class="modal fade"
                                    aria-modal="true">
                                    <div class="modal-dialog modal-md">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">
                                                <button type="button" class="close"
                                                    data-dismiss="modal">&times;</button>
                                                <h5 class="modal-title">Note Changes to Badges Allocations</h5>
                                            </div>
                                            <div class="modal-body">
                                                <form
                                                    action="{{ route('badge_allocations.update', ['badge_allocation' => $item->badge_allocation_id]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    @foreach ($adviceNote as $value)
                                                        <div class=" form-group">
                                                            <label>Sponsorship Benefits
                                                                [{{ $value->advice_note_id }}]</label>
                                                            <input type="hidden" name="advice_note_id"
                                                                value="{{ $value->advice_note_id }}">
                                                            <textarea class="form-control" name="reasons" readonly>{{ $value->advice_note_description }}</textarea>
                                                        </div>
                                                    @endforeach
                                                    <div class=" form-group">
                                                        <label>Sponsorship badges Quantity<i
                                                                class=" text-danger">*</i></label>
                                                        <input type="number" name="free_badges" class=" form-control"
                                                            placeholder="Enter Allocation Number"
                                                            value="{{ $item->free_badges }}">
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-link"
                                                    data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save changes</button>
                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class=" form-group text-right">
                    {{ $allocations->links() }}
                </div>

            </div>
        </div>
    </div>
    <!-- /dashboard content -->

</x-app-layout>

<script type="text/javascript">
    $('#selectall').change(function() {
        if ($(this).prop('checked')) {
            $('input').prop('checked', true);
        } else {
            $('input').prop('checked', false);
        }
    });
    $('#selectall').trigger('change');
</script>
