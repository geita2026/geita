<title> Delegate | Profiles</title>
<style>
    .animate-charcter {
        text-transform: uppercase;
        text-align: center;
        background-image: linear-gradient(-225deg,
                #367a30 2%,
                #ffbb00 29%,
                #0084ff 67%,
                #000000 100%);
        background-size: auto auto;
        background-clip: border-box;
        background-size: 200% auto;
        color: #fff;
        background-clip: text;
        text-fill-color: transparent;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: textclip 5s linear infinite;

    }

    @keyframes textclip {
        to {
            background-position: 200% center;
        }

    }

    .zoom {
        transition: transform .5s;
    }

    .zoom:hover {
        -ms-transform: scale(1.02);
        /* IE 9 */
        -webkit-transform: scale(1.02);
        /* Safari 3-8 */
        transform: scale(1.02);
    }

    .details:hover {
        background-color: #39cc65;
    }
</style>
<x-guest-layout>

    <div class="panel panel-flat">
        <div class=" panel-body ">
            {{-- report points --}}
            <div class="col-md-12">


                @foreach ($events as $event)
                    <div style="background-image:url({{ $event->eventPoster }})" class=" p-20 form-group ">
                        <div class=" row text-center" style=" background-color:rgb(231, 235, 230); border-radius:1%;">
                            <div class=" col-md-1">
                                <img src="{{ asset('images/Tanzania-xl.gif') }}" height="50" width="50" /> <i
                                    class=" text-primary text-bold icon-collaboration"></i>
                                <img src="{{ asset('images/Indonesia-xl.gif') }}" height="50" width="50" />
                            </div>
                            <div class="eventHeader col-md-11 text-center text-primary text-bold animate-charcter"
                                style="font-size:6vmin;">
                                {{ $event->eventTitle }}
                            </div>

                        </div>
                    </div>

                    <div class=" form-group row p-5" style="border:1px solid rgb(214, 212, 212);">
                        <div class=" col-md-10">
                            <ul>
                                <li class=" icon-calendar"><i></i> <b class=" text-muted"
                                        style="font-size:1vmax;">FROM: {{ date('d', strtotime($event->startFrom)) }} -
                                        {{ date('d M, Y', strtotime($event->endTo)) }}</b>
                                </li>
                                <br><br>
                                <li class=" icon-pin mb-15"><i></i> <b class=" text-muted"
                                        style="font-size:1vmax;">JAKATA - INDONESIA.</b> </li>
                            </ul>
                        </div>
                       
                        <div class=" col-md-12" style="border-top: 1px dashed rgb(128, 211, 186);">
                            <h5 class=" text-justify" style="font-size:1.5vmax;">{{ $event->eventDescription }}.</h5>
                        </div>
                    </div>

                    
                @endforeach
                {{-- </div> --}}
                {{-- </div>
                        </div>
                    </div> --}}

                <div class=" form-group">
                    <div class="row" style="font-size:1vmax;">
                        @foreach ($participants as $item)
                            <div class="col-lg-4 col-md-6 " >

                                    <div class="panel panel-body zoom" style="border-radius:2%;border-top:5px solid rgb(74, 132, 187);">
                                        <div class="media">
                                            <div class="media-left">
                                                <a href="{{ $item->participantPhoto }}" data-popup="lightbox">
                                                    @if ($item->participantPhoto == null)
                                                        <img src="{{ asset('assets/images/placeholder.jpg') }}" class="img-circle img-lg"
                                                            alt="">
                                                            @else
                                                            <img src="{{ $item->participantPhoto }}" class="img-circle img-lg" alt="">
                                                    @endif
                                                </a>
                                            </div>
    
                                            <div class="media-body">
                                                <h6 class="media-heading text-uppercase">{{ $item->participantName }}</h6>
                                                <span class="text-muted">{{ $item->participantTitle }}</span>
    
                                            </div>
    
                                            
                                            <div class=" text-center">
                                                <b >{{ Illuminate\Support\Str::limit($item->participantCompany, 40) }}</b>
                                            </div>
                                            <div class=" text-center details">
                                                <a href="{{ route('delegate_profiles.show', ['delegate_profile' => $item->participantId]) }}"
                                                    class=" text-bold btn btn-block btn-default text-primary">View My Profile <i class=" icon-profile"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                
                            </div>
                        @endforeach
                    </div>

                    {{-- <div class=" p-2 text-right">
                        {{ $participants->links() }}
                    </div> --}}
                </div>
</x-guest-layout>
