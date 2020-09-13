<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoginTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * @test
     * A basic test example.
     *
     * @return void
     */
    public function after_user_login_can_not_visit_home_page_until_verified()
    {
        $this->loginUser();
        $this->get('/home')
        ->assertRedirect('/verify-otp');        
    }

    /**
     * @test
     */
    public function after_user_login_can_visit_home_page_if_verified()
    {
        $this->loginUser([
            'isVerified' => 1
        ]);
        $this->get('/home')
        ->assertStatus(200);
    }
}
