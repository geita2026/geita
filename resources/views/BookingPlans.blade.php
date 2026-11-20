@foreach ($spaceCategories as $item)
<div class=" col-md-4 form-group">
    <div class=" card zoom bg-white" style="border:1px solid rgb(138, 172, 177);border-radius:1%">
        <div class=" card-header  p-10 " >
            <p class="text-center text-bold text-uppercase h4">{{ $item->category_name }}</p>
            
            <p class="h5">
                <div class="media">
                    <div class="media-link cursor-pointer" data-toggle="collapse" data-target="#{{ $item->category_id }}">
                        
                        <div class="media-body h5">
                            <a class="media-heading text-semibold text-bold">{{ Illuminate\Support\Str::limit($item->category_descriptions,60) }}</a>
                        </div>
                        <div class="media-right media-middle text-nowrap">
                            <a class=" icon-arrow-down12 display-block "></a>
                        </div>
                    </div>

                    <div class="collapse" id="{{ $item->category_id }}">
                        <div class="contact-details h5">
                            {{ $item->category_descriptions }}
                        </div>
                    </div>
                </div>
                
            </p>
        </div>
        <div class=" card-body h6">
            <p class="h5 text-center">
                @if ($item->type_of_currency=='USD')
                {{ $item->type_of_currency }} <b >{{ number_format(160,2) }}</b> (Normal Stand) / 1M<sup>2</sup> , <b>{{ number_format(210,2) }}</b> (Conner Stand) / 1M<sup>2</sup>  (VAT Exclusive)
                @else
                {{ $item->type_of_currency }} <b >{{ number_format($item->cost_one_msq,2) }}</b>  / 1M<sup>2</sup> (VAT Exclusive)
                @endif
                
            </p>
            <p class=" text-center">
                <a href="{{ route('booking.index') }}" class=" btn btn-block btn-warning">Add to Cart <i class=" icon-cart-add"></i></a>
            </p>
            <p>Top Service Available</p>
            <p>
                <p><i class=" icon-check text-success"></i> Free Table <a class=" text-primary icon-question3" 
                    data-popup="popover" title="Free Table" data-trigger="hover" 
                    data-content="Special Where exhibitors place their products"></a>
                </p>

                <p><i class=" icon-check text-success"></i> Free Two Chairs <a class=" text-primary icon-question3" 
                    data-popup="popover" title="Free Two Chairs" data-trigger="hover" 
                    data-content="No description"></a>
                </p>
                <p><i class="  icon-cross3 text-danger"></i> Extrabadges Badges (30,000 TZS)
                    <a class=" text-primary icon-question3" 
                    data-popup="popover" title="Extrabadges Badges" data-trigger="hover" 
                    data-content="No description"></a>
                </p>
                <p><i class=" icon-check text-success"></i> Free Electricity & Water
                    <a class=" text-primary icon-question3" 
                    data-popup="popover" title="Free Electricity & Water" data-trigger="hover" 
                    data-content="No description"></a>
                </p>
                <p><i class="  icon-cross3 text-danger"></i> Exhibition Badges (3,000 TZS)
                    <a class=" text-primary icon-question3" 
                    data-popup="popover" title="Exhibition Badges" data-trigger="hover" 
                    data-content="No description"></a>
                </p>
                <p><i class=" icon-check text-success"></i> Attend B2B and business forums
                    <a class=" text-primary icon-question3" 
                    data-popup="popover" title="Attend B2B and business forums" data-trigger="hover" 
                    data-content="No description"></a>
                </p>
                <p><i class="  icon-cross3 text-danger"></i> Sesonal Inside car Parking (500,000 TZS)
                    <a class=" text-primary icon-question3" 
                    data-popup="popover" title="Sesonal Inside car Parking" data-trigger="hover" 
                    data-content="No description"></a>
                </p>
                <p><i class=" icon-check text-success"></i> Exhibition Catalogue Apearence
                    <a class=" text-primary icon-question3" 
                    data-popup="popover" title="Exhibition Catalogue Apearence" data-trigger="hover" 
                    data-content="No description"></a>
                </p>
                <p><i class="  icon-cross3 text-danger"></i> Seasonal  Outside Car Parking (50,000 TZS)
                    <a class=" text-primary icon-question3" 
                    data-popup="popover" title="Seasonal  Outside Car Parking" data-trigger="hover" 
                    data-content="No description"></a>
                </p>
                <p><i class=" icon-check text-success"></i> Participation Certificate
                    <a class=" text-primary icon-question3" 
                    data-popup="popover" title="Participation Certificate" data-trigger="hover" 
                    data-content="No description"></a>
                </p>
                <p><i class=" icon-check text-success"></i> Security
                    <a class=" text-primary icon-question3" 
                    data-popup="popover" title="Security" data-trigger="hover" 
                    data-content="No description"></a>
                </p>
                <p><i class="  icon-cross3 text-danger"></i> An emergency (Contingency) Inside Car Parking (300,000 TZS)
                    <a class=" text-primary icon-question3" 
                    data-popup="popover" title="An emergency (Contingency) Inside Car Parking" data-trigger="hover" 
                    data-content="No description"></a>
                </p>
            </p>
        </div>
    </div>
</div>   
@endforeach
