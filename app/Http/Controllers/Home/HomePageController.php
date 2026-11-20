<?php

namespace App\Http\Controllers\Home;
use App\Http\Controllers\Controller;
use App\Models\Advertisements\TextAdvertisement;
use App\Models\sponsorships\EventSponsor;
use Illuminate\Http\Request;
use App\Traits\VistorsTrait;
use App\Traits\BackUpTrait;

class HomePageController extends Controller
{
    use VistorsTrait;
    use BackUpTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sponsors = EventSponsor::inRandomOrder()->get();
        $slidings = TextAdvertisement::where('status',1)->orderBy('advertising_days','ASC')->get();
        // return view('test_page');

        return view('welcomeBackup', compact('sponsors','slidings'));
        // return view('welcome');
    }
    
    public function getAllTextAdvertisment(Request $request){
        // $sponsors = EventSponsor::inRandomOrder()->get();
        $sponsors=EventSponsor::all();
        return response()->json($sponsors,200);
    }
}
