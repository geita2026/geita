@foreach ($eventType as $item)
<x-app-layout>
    <title>{{ env('APP_NAME') }} | Applications</title>
    <!-- dashboard -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="#"><i class="icon-home2 position-left"></i> ePermit </a></li>
                <li class="active">Edit event type</li>
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
                <div class=" col-md-12 table-header form-group">
                    <h4 class=" text-center">Edit Trade Promotion Event Types</h4>
                </div> 
                <div class=" form-group col-md-12">
                    <form action="{{ route('event_types.update',['event_type'=>$item->eventTypeId]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class=" row form-group">
                            <div class=" form-group col-md-6">
                                <label>Event Type Name<i class=" text-danger">*</i></label>
                                <input type="text" name="promotionEventTypeName" class=" form-control" value="{{ $item->promotionEventTypeName }}">
                            </div>
                            <div class=" form-group col-md-6">
                                <label>event Type Poster</label>
                                <input type="file"  name="eventTypePoster" class=" form-control" >
                            </div>
                        </div>

                        <div class=" row form-group">
                            <div class=" form-group col-md-6">
                                <label>event Type Amount In Usd<i class=" text-danger">*</i></label>
                                <input type="number" name="eventTypeAmountUsd" class=" form-control" value="{{ $item->eventTypeAmountUsd }}">
                            </div>
                            <div class=" form-group col-md-6">
                                <label>event Type Amount In TZS<i class=" text-danger">*</i></label>
                                <input type="number"  name="eventTypeAmountTzs" class=" form-control" value="{{ $item->eventTypeAmountTzs }}">
                            </div>
                        </div>

                        <div class=" row form-group">
                            <div class=" form-group col-md-12">
                                <label>Event Type Description<i class=" text-danger">*</i></label>
                                <textarea  name="promotionEventTypeDescription" class=" form-control" >{{ $item->promotionEventTypeDescription }}</textarea>
                            </div>
                        </div>
                        <div class=" text-right form-group">
                            <button class=" btn btn-primary btn-ladda btn-ladda-spinner">Save Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
@endforeach