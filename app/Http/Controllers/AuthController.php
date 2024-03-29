<?php

namespace App\Http\Controllers;

use Hash;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    public function signup(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string',
        ]);
        $user = new User([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);
        $user->save();
        //
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();
        //
        return response()->json([
            'message' => 'Successfully created user!',
            'access_token' => $tokenResult->accessToken
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'       => 'required|string|email',
            'password'    => 'required|string',
            'remember_me' => 'boolean',
        ]);
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized'], 401);
        }
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();
        return response()->json([
            'message'      => 'Authenticated',
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse(
                $tokenResult->token->expires_at)
                    ->toDateTimeString(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 
            'Successfully logged out']);
    }

    public function update(Request $request){
        $user = User::find($request->user()->id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        if($request->input('password') != ''){
            $user->password = Hash::make( $request->input('password') );
        }
        
        $user->save();
        return $user;
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function checkToken(Request $request) {
        $user = User::find($request->user()->id);


        return response()->json([
            'message' => 'Valid',
        ], 202);
    }
}
