<title> Delegate | Profiles</title>
<style>
    .animate-charcter {
        text-transform: uppercase;
        text-align: center;
        background-image: linear-gradient(-225deg,
                #3700ff 0%,
                #fbff03 20%,
                #199229 67%,
                #000000 100%);
        background-size: auto auto;
        background-clip: border-box;
        background-size: 200% auto;
        color: #e21d1d;
        background-clip: text;
        text-fill-color: transparent;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: textclip 5s linear infinite;
        /* display: inline-block; */
    }

    @keyframes textclip {
        to {
            background-position: 200% center;
        }
    }
    .details:hover{
        background-color: #39cc65;
    }
</style>
<x-guest-layout>

    <!-- Content area -->
    {{-- <div class="content"> --}}

        <div class="panel panel-flat row">
            <div class=" panel-body">
                {{-- report points --}}
                <div class=" table-responsive col-md-12">
                    <div class=" form-group">
                        <div class="row" >
                            <div class=" col-md-12 form-group">
                                <a href="{{ route('delegate_profiles.index') }}" class=" btn btn-default text-primary"><i class=" icon-arrow-left12"></i> Back</a>
                            </div>

                            @foreach ($participants as $item)
                                <div class=" col-md-12 form-group" style="border: 1px solid rgb(224, 223, 223);">
                                   <h2  class=" text-primary text-bold text-uppercase">{{ $item->participantName }}'S PROFILE</h2>
                                </div>

                                
                                <div class=" col-md-12" style="border: 1px solid rgb(238, 236, 236);">
                                    <div class="contact-details">
                                        <h6 class="text-semibold no-margin" >
                                            <p class="details" style="border-bottom: 1px solid rgb(238, 238, 236);"><b>Title:</b> {{ $item->participantTitle }}.</p>
                                            <p class="details" style="border-bottom: 1px solid rgb(238, 238, 236);"><b>Company:</b> {{ $item->participantCompany }}.</p>
                                            <p class="details" style="border-bottom: 1px solid rgb(238, 238, 236);"><b>Sector:</b> {{ $item->sector }}</p>
                                            <p class="details" style="border-bottom: 1px solid rgb(238, 238, 236);"><b>Email:</b> <a href="mailTo:{{ $item->participantEmail }}"> {{ $item->participantEmail }}</a>
                                            </p>
                                            <p class="details" style="border-bottom: 1px solid rgb(238, 238, 236);"><b>Mobile No:</b> <a href="tel:{{ $item->participantPhone }}"> {{ $item->participantPhone }}</a>
                                            </p>
                                            <p class="details" style="border-bottom: 1px solid rgb(238, 238, 236);"><b>Website: </b> <a href="{{ $item->participantWebsite }}" target="_newtab"> {{ $item->participantWebsite }}</a>
                                            </p>

                                        </h6>
                                        
                                        @if ($item->participantProfile!=Null)
                                        <p class=" text-justify p-5 details">
                                            <b class=" text-primary">Personal Profile:
                                            </b>{{ $item->participantProfile }}
                                        </p>

                                        <hr class=" divider">   
                                        @endif
                                        
                                        @if ($item->companyProfile!=Null)
                                        <p class=" text-justify p-5 form-group details">
                                            
                                            @if ($item->companyLogo != null)
                                                <img src="{{ $item->companyLogo }}" height="40" width="40"
                                                    style="border-radius:100%;border:1px solid blue;">
                                            @else
                                                <img src="{{ asset('assets/images/cover.jpg') }}" height="40"
                                                    width="40"
                                                    style="border-radius:100%;border:1px solid blue;">
                                            @endif
                                            <b class=" text-primary">
                                            Company Profile:
                                        </b>{{ $item->companyProfile }}
                                    </p> 
                                        @endif
                                        

                                    </div>
                                    <div class=" p-10">
                                        <b>{{ QrCode::size(80)->generate(env('APP_URL') . '/delegate_profiles/'.$item->participantId) }}</b>
                                    </div>
                                </div>
                               
                                

                            @endforeach
                        </div>




                    </div>
                    {{-- //end report point --}}
                </div>
            </div>
        </div>
     
</x-guest-layout>
