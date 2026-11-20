<div class="tab-pane fade in active" id="Default">
    <div class="panel panel-flat">
        <div class=" col-md-12 table-header form-group">
            <h2 class=" text-center  text-bold">
                SYSTEM DEFAULT SETTING
            </h2>
        </div>
        <div class="panel-body">
                <div class="form-group">
                     <div class=" row form-group">
                        <div class="col-md-6">
                            <label>System Title</label>
                            <input type="text" name="system_title" rows="4" class=" form-control" placeholder="Enter system title" value="{{ $item->system_title ?? '' }}">
                        </div>

                        <div class="col-md-6">
                            <label>System Version</label>
                            <input type="text" name="system_version" class="form-control" value="{{ $item->system_version ?? '' }}" placeholder="Enter system Current Version">
                        </div>

                     </div>

                </div>
        </div>
    </div>

</div>
