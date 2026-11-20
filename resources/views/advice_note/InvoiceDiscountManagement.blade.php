@foreach ($adviceNotes as $item)
    <title>{{ env('APP_NAME') }} | AdviceNotes</title>

    <x-app-layout>
        <style>
            .zoom {
                padding: 2%;
                transition: transform .5s;
                font-size: 20px;
            }

            .zoom:hover {
                -ms-transform: scale(1.1);
                /* IE 9 */
                -webkit-transform: scale(1.1);
                /* Safari 3-8 */
                transform: scale(1.1);
            }
        </style>
        {{-- page header --}}
        <div class="page-header">
            <div class="page-header-content">
                @include('layouts.heading-elements')
            </div>
            <div class="breadcrumb-line breadcrumb-line-component">
                <ul class="breadcrumb">
                    <li><a href="#"><i class=" icon-home position-left"></i>Home</a></li>
                    <li class="active">Discount dashboard</li>
                </ul>
                <ul class="breadcrumb-elements">
                    @include('layouts.help')
                </ul>
            </div>
        </div>
        {{--  <!-- Content area -->  --}}
        <div class="content">
            <div class="panel panel-white">
                <div class=" panel-body">
                    <h4 class="text-muted text-center text-uppercase"> YOU ARE APPLYING
                        DISCOUNT FOR <b class=" text-bold">{{ $item->companyName }}</b></h4>
                    <h3 class=" text-bold">TOTAL INVOICED AMMOUNT:
                        {{ number_format($item->total_amount, 2) }} {{ $item->currency }} [<a href="{{ route('adviceNotes.show',['adviceNote'=>$item->advice_note_id]) }}">{{ $item->advice_note_id }}</a>]
                    </h3>
                    @if ($item->discount == 0)
                        <form action="{{ route('apply_discount') }}" method="POST" enctype="multipart/form-data"
                            method="POST">
                            @csrf
                            <input type="hidden" name="adviceNoteId" value="{{ $item->advice_note_id }}">
                            <div class=" form-group">

                                <i class=" text-muted"><code>Note!</code> Discount amount must be
                                    specified in percent not otherwise.</i>
                            </div>
                            <div class=" row form-group">
                                <div class="col-md-12">
                                    <label>Discount Percent<i class=" text-danger">*</i></label>
                                    <input type="number" step="0.01" name="discount" class=" form-control"
                                        placeholder="Discount Amount" value="{{ old('discount') ?? $item->discount }}"
                                        min="0" required max="50"
                                        @if ($item->discount > 0) readonly @endif>
                                </div>

                            </div>
                            <div class=" row form-group">
                                <div class=" col-md-12">
                                    <label>Reasons for Discount<i class=" text-danger">*</i>
                                        <span>Include all permit</span></label>
                                    <textarea name="discount_reasons" class=" form-control" placeholder="Write your reasons here" required
                                        @if ($item->discount > 0) readonly @endif>{{ $item->discount_reasons }}</textarea>
                                </div>
                            </div>
                            <div class=" text-right">
                                @if ($item->discount <= 0)
                                    <button type="submit" class="btn btn-primary ml-20"> 
                                        Request Discount</button>
                                @endif
                            </div>
                        </form>
                    @else
                        <div class=" col-md-12 table-responsive">
                            <ul class="media-list chat-list content-group">
                                @foreach ($comments as $com)
                                    <li class="media">
                                        <div class="media-body">
                                            <div class="media-content">
                                                {{ $com->comment }}
                                            </div>
                                            <span class="media-annotation display-block mt-10"
                                                style="font-size: 10px"><i style="text-align: right">
                                                    {{ $com->first_name }} {{ $com->last_name }}
                                                    [{{ $com->name }}]</i>
                                                {{ date('M d, Y h:i:s', strtotime($com->commentDate)) }}
                                            </span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @if ($item->finally_assessed_by == null)
                            <form action="{{ route('approve_discount') }}" method="POST">
                                @csrf
                                <input type="hidden" name="adviceNoteId" value="{{ $item->advice_note_id }}">
                                <div class=" form-group col-md-4">
                                    <label>What is your decision about the description above? <i
                                            class=" text-danger">*</i></label>
                                    <select name="descision" class=" form-control">
                                        <option value="UNDER_REVIEW">STILL UNDER REVIEW</option>
                                        <option value="APPROVED">APPROVED</option>
                                    </select>
                                </div>
                                <div class=" form-group col-md-8">
                                    <label>What is your comment on your decision?<i class=" text-danger">*</i></label>
                                    <textarea name="comment" class=" form-control" placeholder="Type your coment here" required></textarea>
                                </div>
                                <div class=" form-group col-md-12 text-right">
                                    <button type="submit" class=" btn btn-primary">Save descision <i
                                            class=" icon icon-arrow-right13"></i></button>
                                </div>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        <!-- /main content -->
    </x-app-layout>
@endforeach


{{-- data-toggle="modal"
data-target="#Discount{{ $item->advice_note_id }}" --}}
