<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use App\User;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Client;
class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login_with_jwt()
    {  
        $credentials = request(['email', 'password']);

        if (!$token = JWTAuth::claims(['name' => 'Mufeez'])->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    // using oAuth
    public function login() 
    {
        $credentials = request(['email', 'password']);
        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
       
       // not working: Since the PHP built-in server is single threaded, requesting another url on your server will halt first request and it gets timed out.
        // $http =  new Client([
        //     'base_uri' => 'http://127.0.0.1:8001/',
        // ]);
        // $response = $http->post(url('oauth/token'), [
        //     'form_params' => [
        //         'grant_type' => 'password',
        //         'client_id' => '2',
        //         'client_secret' => 'TJjFShveL3MAsesU0joxhBDX0fT2qnLVg9ptt21X',
        //         'username' =>  $credentials['email'],
        //         'password' => $credentials['password']               
        //     ],
        // ]);
        return response()->json([
            'access_token' => $user->createToken('Auth Token')->accessToken,
            'token_type' => 'bearer'           
        ]);
        //                 
        //return json_decode((string) $response->getBody(), true);
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
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    public function payload() 
    {
        return auth()->payload();
    }
}
