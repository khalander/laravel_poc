<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use App\Mail\OTPMail;
use Psr\SimpleCache\CacheInterface;
use Illuminate\Support\Facades\Cache;

;

class EmailTest extends TestCase
{

    use DatabaseTransactions;
   
    /**
     * @test
     */
     public function an_otp_email_is_sent_when_user_login()
     {
        $this->withoutExceptionHandling();
        Mail::fake();
         $user = factory(User::class)->create([
             'isVerified' => 1
         ]);

        $this->post('/login', [
             'email' => $user->email,
             'password' => 'secret'
         ]);
         
         Mail::assertSent(OTPMail::class);
         
     }

     /**
      * @test
      */
    public function an_otp_email_will_not_sent_for_invalid_users()
    {
        $this->withExceptionHandling();
        Mail::fake();
        $this->post('/login', [
            'email' => 'test@123',
            'password' => 'serse'
        ]);
        Mail::assertNotSent(OTPMail::class);
    }
    
    /**
     * @test
     */
    public function an_otp_exits()
    {
        $user = factory(User::class)->create([
            'isVerified' => 1
        ]);
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret'
        ]);
        $this->assertNotNull($user->getOtp());

    }
    
}
