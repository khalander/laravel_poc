<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    public function loginUser($args = [])
    {
        $user = factory(User::class)->create($args);
        $this->actingAs($user);
    }
}
 