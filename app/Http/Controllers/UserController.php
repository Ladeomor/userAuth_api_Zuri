<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use Auth;
use Session;


class UserController extends Controller
{
    public function register(Request $request){
        $validate = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required'
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validate->errors()
            ], 401);

        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)

        ]);
        return response()->json([
            'status' => true,
            'message' => 'User registration successful',
        ], 200);


    }
    public function login(Request $request){
        $validate = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validate->errors()
            ], 401);

        }
        if(!Auth::attempt($request->only(['email', 'password']))){
            return response()->json([
                'status' => false,
                'message' => 'Invalid Email or Password',
            ], 401);
        }

            $user = User::where('email', $request->email)->first();

            //store user email in session
            $userSession = Auth::user();
            Session::put('email', $userSession->email);

            return response()->json([
                'session' => $userSession->email,
                'status' => true,
                'message' => 'User Login Successful',
            ], 200);
        }

    public function update(Request $request, $id){
        $validate = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required'
        ]);

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'User details updated Successfully',
            'user' => $user,

        ],200);

    }    

    public function delete($id){
        $user = User::find($id);
        $user->delete();
        return response()->json([
            'message'=> 'User deleted successfully',

        ]);
    }

    
}
