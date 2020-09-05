<?php

namespace ET\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class OAuthController extends Controller
{

    /* 
        Note: This controll should at client application
    */
    public function redirect()
    {
        $queries = http_build_query([
            'client_id' => 3,
            'redirect_uri' => 'https://tm.dev/api/oauth/callback',
            'response_type' => 'code'
        ]);
        return redirect('http://127.0.0.1:8000/oauth/authorize?' . $queries);
    }

    /*
        curl request is not working
        1) store in OauthToken table
        2) create relation in user model
        3) Create Post API at server side
        4) Access it at client side by passing token as header

    */
    public function callback(Request $request)
    {      
        $http =  new Client([
                'base_uri' => 'http://127.0.0.1:8000/',
            ]);
        $response = $http->post('http://127.0.0.1:8000/oauth/token', [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => '3',
                    'client_secret' => 'HchnrES58Kj4fga02gfEK9xY8v4ZVki08ykHG0h6',
                    'redirect_uri' => 'https://tm.dev/api/oauth/callback',
                    'code' => $request->code             
                ],
            ]);
            $response = $response->json();

            $request->user()->token()->delete();
    
            $request->user()->token()->create([
                'access_token' => $response['access_token'],
                'expires_in' => $response['expires_in'],
                'refresh_token' => $response['refresh_token']
            ]);
    
            return redirect('/home');;
    }
}
