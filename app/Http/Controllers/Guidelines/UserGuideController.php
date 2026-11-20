<?php

namespace App\Http\Controllers\Guidelines;

use App\Http\Controllers\Controller;

class UserGuideController extends Controller
{
  public function userGuide(){

    return view('Support.user-manual');
  }
}
