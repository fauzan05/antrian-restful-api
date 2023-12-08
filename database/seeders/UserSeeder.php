<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User();
        $user->name = 'Fauzan';
        $user->username = 'fauzan123';
        $user->password = Hash::make('rahasia');
        $user->role = 'operator';
        $user->save();

        $user = new User();
        $user->name = 'Susi';
        $user->username = 'susi123';
        $user->password = Hash::make('rahasia');
        $user->role = 'operator';
        $user->save();

        $user = new User();
        $user->name = 'Rudi';
        $user->username = 'rudi123';
        $user->password = Hash::make('rahasia');
        $user->role = 'operator';
        $user->save();

        $user = new User();
        $user->name = 'Heri';
        $user->username = 'heri123';
        $user->password = Hash::make('rahasia');
        $user->role = 'operator';
        $user->save();

        $user = new User();
        $user->name = 'Bela';
        $user->username = 'bela123';
        $user->password = Hash::make('rahasia');
        $user->role = 'operator';
        $user->save();

        $user = new User();
        $user->name = 'Indri';
        $user->username = 'indri123';
        $user->password = Hash::make('rahasia');
        $user->role = 'operator';
        $user->save();

        $user = new User();
        $user->name = 'admin';
        $user->username = 'admin';
        $user->password = Hash::make('admin');
        $user->role = 'admin';
        $user->save();
    }
}
