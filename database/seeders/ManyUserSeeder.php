<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ManyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = new User();
        $admin->name = "admin";
        $admin->username = "admin";
        $admin->password = Hash::make('admin');
        $admin->role = 'admin';
        $admin->save();

        for( $i = 0; $i < 10; $i++ ) {
            $user = new User();
            $user->name = 'user-'. $i;
            $user->username = 'username-'. $i;
            $user->password = Hash::make('password-'. $i);
            $user->role = 'operator';
            $user->save();
        }
    }
}
