<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VerifyTest extends TestCase
{

  //  use DatabaseTransactions;

    /**
     * @test
     */
    public function verify_submitted_otp()
    {
        $this->withExceptionHandling();
        $user = factory(User::class)->create();
        $this->actingAs($user);
        auth()->user()->cacheTheOtp();
        $otp = auth()->user()->getOtp();
        $this->post('/verify-otp', [auth()->user()->getOtpKey() => $otp])->assertStatus(200);
        $this->assertDatabaseHas('users', ['isVerified' => 1]);
    }

    /**
     * @test
     */
    public function user_can_see_verify_page()
    {
        $this->loginUser();
        $this->get('/verify-otp')
        ->assertStatus(200)
        ->assertSee('Enter Otp');
    }

    /**
     * @test
     */

     public function verify_invalid_otp()
     {
        $this->loginUser();
        $this->post('/verify-otp', [
            'otp' => 'invalid otp'
        ])
        ->assertSessionHasErrors();
     }

     /**
      * @test
      */
     public function verify_null_otp()
     {
        $this->withExceptionHandling(); 
        $this->loginUser();
        $this->post('/verify-otp', ['otp' => null])
        ->assertSessionHasErrors(['otp']);
     }
}
