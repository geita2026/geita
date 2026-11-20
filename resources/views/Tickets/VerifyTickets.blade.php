<x-guest-layout>
    <div class="content">
        <div class="panel panel-white">
            <div class=" panel-body table-responsive">

                <form action="{{ route('verify.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class=" form-group">
                        <input type="text" name="ticketNo" placeholder="Enter Ticket No" class=" form-control" required>
                        <span class=" text-danger">@error('ticketNo') {{ $message }} @enderror</span>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner btn-block"><span
                                class="ladda-label">VERIFY ONLINE TICKET</span></button>
                    </div>
                </form>



            </div>
        </div>
    </div>
</x-guest-layout>
