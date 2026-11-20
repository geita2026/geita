<?php

namespace App\Http\Controllers\profiles;

use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\Companies\AssociateCompany;
use App\Models\sectors;
use App\Models\setting\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use Intervention\Image\Facades\Image;

class myprofileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {

        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        if ($request->user()->cannot('Manage Profile', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $user = User::find(Auth::user()->id);
        $role = $user->getRoleNames();
        

        $sectors = sectors::get();
        $countries = Country::get(['country_name']);
        $user = User::where('user_uuid', Auth::user()->user_uuid)->first();
        if ($user) {
            $regStatus = $user->regStatus;
            $cId = $user->myCompany;
        }
        $users = User::where('myCompany', $cId)
            ->where('user_uuid', '!=', Auth::user()->user_uuid)->get();
        $associateCompanies = companies::where('associate_company', Auth::user()->myCompany)
            ->count();

        $activities = DB::table('audits')
            ->where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('profile.my-profile', compact('role', 'sectors', 'countries', 'regStatus', 'users', 'associateCompanies', 'activities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('Change My Password', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }

        // return $request;
        $request->validate([
            'password' => 'required',
            'new_password' => ['required', Password::min(8)->mixedCase()->numbers()->uncompromised()],
            'confirmation_password' => 'required|same:new_password'
        ]);

        $values = $request->only('email', 'password');
        if (Auth::attempt($values)) {
            $password1 = Hash::make($request->new_password);
            $uid = Auth::user()->id;
            $user = User::find($uid);
            $updated = $user->update(['remember_token' => Str::random(60), 'password' => $password1, 'password_updated' => 1]);
            if ($updated) {
                return redirect()->back()->with('success', 'Password changed successfully!');
            } else {
                return redirect()->back()->withErrors('fail', 'Something went wrong!');
            }
        }
        return back()->withErrors([
            'password' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if ($request->user()->cannot('Change My Password', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $user = User::find(Auth::user()->id);
        $role = $user->getRoleNames();

        return view('auth.change-password', compact('role'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->user()->cannot('Manage Profile', Role::class)) {
            return back()->with('fail', config('Messages.401'));
        }
        $request->validate([
            'first_name'=>'required',
            'last_name'=>'required',
            'phone_number'=>'required|digits:12',
        ]);
        try {
       $user = User::where('user_uuid',$id)->first();
        if ($user) {
           $profileImage = $user->profile_image ?? Null;
           $userSignature=$user->user_signature ?? Null;
            if ($request->profile_image) {
                if ($request->userSignature) {
                    $validator=Validator::make($request->all(),[
                        'profile_image'=>'mimes:png,jpg,jpeg|max:2048'
                    ]);
                    if ($validator->fails()) {
                        return back()->with('fail',implode(',',$validator->getMessageBag()->all()));
                    }
                }
                File::delete(public_path('profile_images/') . $user->profile_image);
                $profileImage = 'personal_profile_'.Str::uuid(). $request->profile_image->extension();
                $request->profile_image->move(public_path('profile_images'),$profileImage);
                // $imageUrl=env('APP_URL').'/profile_images/'.$profileImage;
            }
            if ($request->userSignature) {
                $validator=Validator::make($request->all(),[
                    'userSignature'=>'mimes:png,jpg,jpeg|max:2048'
                ]);
                if ($validator->fails()) {
                    return back()->with('fail',implode(',',$validator->getMessageBag()->all()));
                }
                File::delete(public_path('signatures/') . $user->user_signature);
                $image = Image::make($request->userSignature)->resize(200, 200);
                $userSignature = 'signature_' .$id .'.'.  $request->userSignature->extension();
                $path = public_path('signatures/');
                $image->save($path . $userSignature);
            }
            $user->update([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'phone_number' => $request->phone_number,
                'profile_image' => $profileImage,
                'user_signature'=>$userSignature,
            ]);
            return back()->with('success', config('Messages.200'));
        }
        } catch (\Throwable $th) {
            return back()->with('fail', config('Messages.500'));
        }
    }
}
