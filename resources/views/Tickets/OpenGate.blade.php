<x-guest-layout>
    <div class="content">
        <div class="panel panel-white">
            <div class=" panel-body table-responsive">

                <form action="{{ route('verify.update',['verify'=>1]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class=" form-group">
                        <input type="text" name="gateName" placeholder="Enter Gate Name" class=" form-control" value="{{ session('gateName') }}" required>
                        <span class=" text-danger">@error('gateName') {{ $message }} @enderror</span>
                    </div>
                    <div class=" form-group">
                        <input type="text" name="plateNumber" placeholder="Enter Plate Number" class=" form-control" required>
                        <span class=" text-danger">@error('plateNumber') {{ $message }} @enderror</span>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-ladda btn-ladda-spinner btn-block"><span
                                class="ladda-label">OPEN GATE {{ session('gateName') }}</span></button>
                    </div>
                </form>



            </div>
        </div>
    </div>
</x-guest-layout>
