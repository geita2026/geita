<?php

namespace App\Http\Controllers\users;
use App\Http\Controllers\Controller;
use App\Models\companies;
use App\Models\Companies\CompanyAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use App\Traits\NotificationTrait;

class usersController extends Controller
{
    use NotificationTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){

        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        if($request->user()->cannot('View Users',User::class))
        {
            return back()->with('fail',config('Messages.401'));
        }

        $users=User::leftJoin('roles','users.role','=','roles.id')
        ->orderBy('user_created_at','DESC')
        ->orderBy('first_name','ASC')
        ->paginate(25,['users.id','first_name','middle_name','last_name','name','email','user_uuid','user_ip_address','role','phone_number','account_status','account_type','myCompany']);

        if($request->userName!=Null)
        {
            $users=User::orderBy('account_status','DESC')->leftJoin('roles','users.role','=','roles.id')
            ->where('email','LIKE','%'.$request->userName.'%')
            ->orderBy('account_status','DESC')->orderBy('first_name','ASC')
            ->paginate(25,['users.id','first_name','middle_name','last_name','name','email','user_uuid','user_ip_address','role','phone_number','account_status','account_type','myCompany']);
        }

        if($request->user_type!=null){
            $users=User::orderBy('account_status','DESC')->leftJoin('roles','users.role','=','roles.id')
            ->where('account_type',$request->user_type)
            ->orderBy('account_status','DESC')->orderBy('first_name','ASC')
            ->paginate(25,['users.id','first_name','middle_name','last_name','name','email','user_uuid','user_ip_address','role','phone_number','account_status','account_type','myCompany']);
        }

       
        $roles=Role::get();
        $companies=companies::orderBy('companyName','ASC')->where('status',1)->get();

        return view('users.users',compact('users','roles','companies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if($request->user()->cannot('Create Users',User::class))
        {
            return back()->with('fail',config('Messages.401'));
        }
        $roles=DB::table('roles')->get();
        return view('users.add_user',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->user()->cannot('Create Users',User::class))
        {
            return back()->with('fail',config('Messages.401'));
        }

        #validating user inputs
        if (!empty($request->TSRN)) {
            $request->validate([
                'TSRN'=>'exists:companies,registrationID',
            ]);
        }
        $request->validate([
            'first_name'=>'required',
            'last_name'=>'required',
            'phone_number'=>'required|numeric|digits:12|unique:users,phone_number',
            'email'=>'required|email|unique:users,email',
            'profile_image'=>'mimes:jpg,png,jpeg',
            'signature_image'=>'mimes:jpg,png,jpeg',   
        ]);
        #end of validation
        try {
         $user_sign=Null; 
         $name=Null;
         $userCompany=Null;
         $accountType=1;
        if(!empty($request->signature_image))
        {
            $user_sign=Str::uuid().".".$request->signature_image->extension(); #if available crates new sign name
            $request->signature_image->move(public_path('signatures'), $user_sign); #move sign to storing folder
        }

        if(!empty($request->profile_image)) #check if profile image submitted
        {
            $name=time().".".$request->profile_image->extension(); #if available crates new image name
            $request->profile_image->move(public_path('profile_images'), $name); #move image to storing folder
        }

             if($request->TSRN){
                $userCompany=companies::where('registrationID',$request->TSRN)->first()->companyID;
                $accountType=2;
             }
             $user_uuid=Str::uuid(); #create user uuid
             $default_password=$request->phone_number;
             $user = User::create(['user_uuid'=>$user_uuid,
             'first_name'=>$request->first_name,
             'middle_name'=>$request->middle_name,
             'last_name'=>$request->last_name,
             'phone_number'=>$request->phone_number,
             'email'=>$request->email,
             'profile_image'=>$name,
             'user_signature'=>$user_sign,
             'role'=>$request->role,
             'account_type'=>$accountType,
             'password'=>hash::make($default_password),
             'myCompany'=>$userCompany,

            ]);
            $user->assignRole($request->role);//assign role to user if selected
         //    end of registration

             #if User creation EmailPasswordNotification with default password.
             if (config('global.APP_STATUS')==1) {
                $email=$request->email;  $first_name=$request->first_name; $last_name=$request->last_name;
                $this->EmailPasswordNotification($email,$first_name,$last_name,$default_password); // send email.
                $recipients=$request->phone_number;
                $cutomer_name=$first_name.' '.$last_name;
                $this->SendDefaultPassSMS($recipients,$default_password,$cutomer_name); // send Bulk sms
             }
             # end send default password;

            return redirect()->route('users.index')->with('success',"$request->first_name $request->last_name Informations registered successfully!");


        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','Default password Notification Failed, This might be due to internet problems.'.$th->getMessage());
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        //
        if($request->user()->cannot('View Users',User::class))
        {
            return back()->with('fail',config('Messages.401'));
        }
        else
        {
            return back()->with('fail','We did not understand your request!');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if($request->user()->cannot('Update Users',User::class))
        {
            return back()->with('fail',config('Messages.401'));
        }
        // return $id;
        // $users=DB::table('users')->leftJoin('roles','users.role','=','roles.id')->where('user_uuid',$id)->get();
        $users=User::LeftJoin('roles','users.role','=','roles.id')->where('users.id',$id)->get(['users.id','user_name','name','email','user_uuid','phone_number','role','user_signature']);
        $roles=DB::table('roles')->get();
        return view('users.edit_user',compact('users','roles'));
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
        if($request->user()->cannot('Update Users',User::class))
        {
            return back()->with('fail',config('Messages.401'));
        }
        #validating user inputs
        $request->validate([
            'first_name'=>'required',
            'last_name'=>'required',
            'phone_number'=>'required|numeric',
            'email'=>'required|email',
        ]);
        #end of validation
        try {
            $user = User::find($id);
            if (!empty($request->TSRN)) {
                $company=companies::where('registrationID',$request->TSRN)->first();
                  
                if ($company) { 
                  $exist=CompanyAdmin::where('companyId',$company->companyID)
                  ->where('adminId',$user->user_uuid);
                  
                  if (!$exist) {
                   CompanyAdmin::create([
                       'referenceId'=>Str::uuid(),
                       'adminId'=>$id,
                       'companyId'=>$company->companyID
                   ]);
                  }
                  }
               }

           User::find($id)->update([
           'first_name'=>$request->first_name,
            'middle_name'=>$request->middle_name,
            'last_name'=>$request->last_name,
            'phone_number'=>$request->phone_number,
            'role'=>$request->role_name,
            'account_status'=>1,
        ]);
            DB::table('model_has_roles')->where('model_id',$id)->delete();
            $name=Role::where('id',$request->role_name)->first()->name;
            $user->assignRole($name);

            return back()->with('success',config('Messages.200'));
        } catch (\Throwable $th) {
            return back()->with('fail',config('Messages.500').$th->getMessage());
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        if($request->user()->cannot('Delete Users',User::class))
        {
            return back()->with('fail',config('Messages.401'));
        }

        #------- DISABLE ACCOUNT -----#
        try {
            if($request->disable_ccount){
                //disable to disable your own account.
                if($id==Auth::user()->id)
                {
                    return back()->with('fail','Restricted! you can not disable or delete your own Account.');
                }
                //
                $user=User::find($id);
                $user->update(['account_status'=>0]);
                return back()->with('success','Account disabled successfully this terminate user from accessing system!');
            }
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with('fail','Something went wrong: '.$th->getMessage());
        }
        #------- END DISABLE ACCOUNT -----#



        #------- DELETE ACCOUNT -----#
       try {
        if($id==Auth::user()->id)
        {
            return back()->with('fail','Restricted! you can not disable or delete your own Account.');
        }
        //
        $user=User::find($id);
        $user->update(['account_status'=>0]); // sunspend Account.
        return redirect()->back()->with('success','Account Sunspended successfully!');
       } catch (\Throwable $th) {
        //throw $th;
        return redirect()->back()->with('fail','something went wrong!');
       }
       #------- END DELETE ACCOUNT ----- #

    }

 public function SendDefaultPassSMS($recipients,$default_password,$cutomer_name){
$sms_content="Dear , $cutomer_name
You have been registered to SAMS, bellow is your Login Credentials.
Username: Your registered email.
Password: $default_password.";

$datetime = date('Y-m-d H:i:s'); // date time

        $this->Notification($recipients,$sms_content,$datetime);
    }


}
