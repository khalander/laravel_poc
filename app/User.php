<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\OTPMail;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'isVerified'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getOtpKey()
    {
        return "otp_for_{$this->id}";
    }

    public function getOtp() 
    {
        return Cache::get($this->getOtpKey());
    }

    public function cacheTheOtp()
    {
        return Cache::set([$this->getOtpKey() => rand(1000000, 9999999)], now()->addMinutes(30));
    }

    public function sentOtp($via)
    {
        if ($via == 'email') {
            Mail::to($this->email)->send(new OTPMail($this->getOtp()));
        } else {

        }
        
    }
}
