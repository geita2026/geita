<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class userApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        try {
            $users=User::leftJoin('roles','users.role','=','roles.id')
            ->get(['users.id','first_name','email','phone_number','name','profile_image','user_status']);
        return response()->json([
            'status'=>true,
            'users'=>$users,
        ],200);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status'=>false,
                'message'=>$th->getMessage(),
            ],404);
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $request;
        try {
            $validateUser=Validator::make($request->all(),
            [
                'first_name'=>'required',
                'last_name'=>'required',
                'email'=>'required|email',
                'password'=>'required|alpha_num|min:8',
                'phone_number'=>'required|digits:12',
            ]);

            if($validateUser->fails())
            {
                return response()->json([
                    'status'=>false,
                    'message'=>'Validation error',
                    'errors'=>$validateUser->errors()->toArray()
                ],404);
            }

            $user=User::create([
                'first_name'=>$request->first_name,
                'middle_name'=>$request->middle_name,
                'last_name'=>$request->last_name,
                 'phone_number'=>$request->phone_number,
                 'email'=>$request->email,
                 'password'=>hash::make($request->password),
            ]);

            return response()->json(['status'=>true,'message'=>'user created successfull!','token'=>$user->createToken("API TOKEN")->plainTextToken],200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status'=>false,
                'message'=>$th->getMessage()
            ],500);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
     try {

        $exist=User::leftJoin('roles','users.role','=','roles.id')->where('users.id',$id)
        ->get(['users.id','user_name','email','phone_number','name','profile_image','user_status']);

        if(empty($exist))
        {
            return response()->json([
                'status'=>false,
                'message'=>"User Not found",
            ],404);
        }

        return response()->json([
            'status'=>true,
            'user'=>$exist,
        ],200);
     } catch (\Throwable $th) {
        //throw $th;
        return response()->json([
            'status'=>false,
            'message'=>$th->getMessage(),
        ],404);
     }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
        return 1;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //

        $exist=User::find($id);

        if(empty($exist))
        {
            return response()->json([
                'status'=>false,
                'message'=>'User not found!',
            ],404);
        }

        try {
            $exist->update($request->all());
            return response()->json([
            'status'=>true,
            'message'=>'User updated successfully!',
            'user'=>$exist
        ],200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status'=>false,
                'message'=>$th->getMessage(),
            ],404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        try {
            $exist=User::find($id);
        if(empty($exist))
        {
            return response()->json([
                'status'=>false,
                'message'=>'User Not Found',
            ],404);
        }
        $exist->update(['user_status'=>0]);
        return response()->json([
            'status'=>true,
            'message'=>'User deleted successfully!',
        ],200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status'=>false,
                'message'=>$th->getMessage(),
            ],404);
        }
    }
}
