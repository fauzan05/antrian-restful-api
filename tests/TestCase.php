<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        DB::delete("delete from queues");
        DB::delete("delete from services");
        DB::delete("delete from counters");
        DB::delete("delete from users");
        DB::delete("delete from personal_access_tokens");
    }
}
