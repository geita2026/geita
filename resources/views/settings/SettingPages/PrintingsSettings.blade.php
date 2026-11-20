<div class="tab-pane fade" id="Printings">
    <div class="panel panel-flat">
        <div class=" col-md-12 table-header form-group">
            <h2 class=" text-center  text-bold">
               PRINTING SETTINGS <i class=" icon-printer2"></i>
            </h2>
        </div>
        <div class="panel-body">
                <div class="form-group">
                     <div class=" row form-group">
                       <div class=" row form-group">
                        <div class="checkbox checkbox-switchery switchery-lg col-md-6">
                            <label>
                                <input type="checkbox" name="self_printing" class="switchery" @if($item->self_printing ?? 0==1) checked @endif value="1">
                                Allow Self Printing
                            </label>
                        </div>
                       </div>
                        <div class=" form-group col-md-4">
                            <label>Badging Event<i class=" text-danger">*</i></label>
                            <select name="badging_event" class=" form-control">

                                @foreach ($events as $event)
                                <option value="{{ $event->event_id }}">{{ $event->event_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class=" form-group col-md-4">
                            <label>Active Event<i class=" text-danger">*</i></label>
                            <select name="active_event" class=" form-control">
                                @foreach ($events as $event)
                                @if ($event->event_id==$item->badging_event)
                                <option value="{{ $event->event_id }}">{{ $event->event_name }}</option>
                                @else
                                <option value="{{ $event->event_id }}">{{ $event->event_name }}</option>
                                @endif

                                @endforeach
                            </select>
                        </div>
                     </div>
                </div>

                <div class=" form-group row">
                    <div class=" table-header text-center">
                        <h4>BADGE SETTING</h4>
                    </div>
                    
                    <div class=" form-group col-md-4">
                        <label>Badge Title<i class=" text-danger">*</i></label>
                        <input type="text" name="" class=" form-control">
                    </div>
                    <div class=" form-group col-md-4">
                        <label>Valid Date<i class=" text-danger">*</i></label>
                        <input type="text" name="" class=" form-control">
                    </div>
                </div>
        </div>
    </div>
</div>
