<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
   public function testCurrentUser()
   {
     $user = User::where('name', 'fauzan')->first();
     var_dump($user);
     self::assertTrue(true);
   }
}
