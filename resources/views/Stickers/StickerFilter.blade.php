   {{-- Badge Filter modal --}}
   <div id="filter_Stickers" class="modal fade" aria-modal="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title text-uppercase text-bold text-center">FILTER STICKERS By Any of bellow details</h5>
            </div>
            <div class="modal-body">
                <form action="{{ route('print_stickers.show',['print_stickers']) }}" method="POST">
                    @csrf
                    @method('GET')
                    <div class=" row">
                        <div class=" form-group col-md-6">
                            <label>Select Company</label>
                            <div class="multi-select-full">
                           <select name="company_id[]" class="multiselect-filtering" multiple="multiple"  data-width="100%" required>
                            <option value=""> - - - - Select Company - - - - - </option>
                                   @foreach ($companies as $company)
                                   <option value="{{ $company->companyID}}" class=" text-bold">{{ $company->companyName }}</option>
                                   @endforeach
                           </select>

                        </div>
                        </div>

                        <div class=" form-group col-md-6">
                            <label>Badge Status <i class=" text-danger">*</i></label>
                           <select name="sticker_status" class="bootstrap-select" data-live-search="true" data-width="100%">
                                <option value="0">UnPrinted Stickers</option>
                                <option value="1">Printed Stickers</option>
                                <option value="2">Blocked</option>
                           </select>
                        </div>

                       </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Load Stickers</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Badge filter modal -->


    <div class=" col-md-12">
        <a data-toggle="modal" data-target="#filter_Stickers" class=" btn btn-default border-primary">Filter Stickers<i class=" icon-filter3"></i></a>
    </div>





{{-- Print Badge Menu --}}

<div id="printStickers" class="modal fade" aria-modal="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-uppercase text-bold text-center">ATENSION <i class=" icon-warning"></i></h5>
            </div>
            <div class="modal-body">
                <p>
                    You are about to print All Stickers displayed in your screen take action to confirm them!
                </p>
                <p>
                    <a  class=" text-warning btn btn-default border-warning" data-dismiss="modal">Close</a>

                    <a href="{{ route('print_stickers.edit',['print_sticker']) }}" class=" text-primary btn btn-default border-primary ml-20" target="_tab">Confirm Printing</a>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Print Badge Menu -->
