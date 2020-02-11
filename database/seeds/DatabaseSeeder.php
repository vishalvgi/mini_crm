<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() {
        // $this->call(UsersTableSeeder::class);
        $email = 'admin@admin.com';
        if (User::where('email', $email)->count())
            return;
        $usr = new User();
        $usr->name = 'Administrator';
        $usr->email = $email;
        $usr->password = Hash::make('password');
        $usr->email_verified_at = date('Y-m-d H:i:s');
        $usr->save();
    }

}
