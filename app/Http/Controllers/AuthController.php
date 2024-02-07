<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use  App\Models\User;
use  App\Models\Users;

class AuthController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'refresh', 'logout']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {

        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 200);
        }

        return $this->respondWithToken($token);
    }

    public function register(Request $request)
    {
        $user = User::where("email", $request->email)->first();

        if ($user) {
            return response()->json([
                'success' => false,
                'message' => 'Email sudah digunakan',
            ], 200);
        }
        $data = [
            "name" => $request->name,
            "email" => $request->email,
            "password" => app("hash")->make($request->password),
            "cart_data" => 1,
            "date" => date("Y-m-d")
        ];

        $insert = User::create($data);
        if ($insert) {
            $credentials = $request->only(["email", "password"]);
            $token = Auth::attempt($credentials);
            return response()->json([
                'success' => true,
                'message' => 'Register berhasil',
                'token' => $token
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => "problem with server",
            ], 500);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => auth()->user(),
            'expires_in' => auth()->factory()->getTTL() * 60 * 24
        ]);
    }
}
