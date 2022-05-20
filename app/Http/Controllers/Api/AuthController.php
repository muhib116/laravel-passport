<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;




class AuthController extends Controller
{
    public function index()
    {
        $user = User::all();
        if($user)
        {
            return response()->json([
                'Message' => 'User\'s data',
                'Data' => $user
            ]);
        }

        return response()->json([
            'Message' => 'Users not found!'
        ]);
    }


    public function login(Request $request)
    {
        $validation = Validator($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validation->fails())
        {
            return response()->json([
                'status' => false,
                'Message' => 'Validation Error!',
                'Error'   => $validation->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');
        if(Auth::attempt($credentials))
        {
            $user = Auth::user();
            $data['name'] = $user->name;
            $data['access_token'] = $user->createToken('accessToken')->accessToken;

            return response()->json([
                'message'=>'You are successfully loged in',
                'data'   => $data
            ]);
        }else{
            return response()->json([
                'message' => 'Credentials Missmach'
            ]);
        }
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name"     => "required|min:4",
            "email"    => "required|email|unique:users",
            "password" => "required|min:6",
        ]);

        if($validator->fails()){
            return response()->json([
                'msg' => "Validation Error!",
                'Errors' => $validator->errors()
            ], 422);
        }


        try{
            $user = User::create([
                "name"     => $request->name,
                "email"    => $request->email,
                "password" => \Hash::make($request->password)
            ]);

            
            return response()->json([
                'message' => 'User created successfully!',
                'user' => $user
            ]);

        } catch(Exception $e)
        {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }

    public function show($id)
    {
        $user = User::find($id);

        if($user)
        {
            return response()->json([
                'Message' => 'User data',
                'Data' => [
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ]);
        }

        return response()->json([
            'Message' => 'User not found!'
        ]);
    }

    public function logout(Request $request)
    {
        auth()->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully Logout!'
        ]);
    }
}
