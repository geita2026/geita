<div class="tab-pane fade" id="Notification">
    <div class="panel panel-flat">
        <div class=" col-md-12 table-header form-group">
            <h2 class=" text-center  text-bold">
                NOTIFICATIONS SETTING
            </h2>
        </div>
        <div class="panel-body">

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Notification Chanel</label>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="notification_channel" value="1" class="styled" @if($item->notification_channel ?? 0 == 1) checked="checked" @endif>
                                    Through Email Address
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" value="2" name="notification_channel" class="styled" @if($item->notification_channel ?? 0 ==2) checked="checked" @endif>
                                    Through Normal Messages
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" value="3" name="notification_channel" class="styled" @if($item->notification_channel ?? 0 ==3) checked="checked" @endif>
                                    Through Both Email & Normal SMS Channels
                                </label>
                            </div>
                        </div>
                        <div class=" form-group col-md-4">
                            <label>Printed Badge Notification<i class=" text-danger">*</i></label>
                            <input type="checkbox" name="print_notification" value="1" @if($item->print_notification ?? 0 ==1) checked="checked" @endif>
                        </div>
                    </div>
                </div>
        </div>
    </div>

</div>
