<?php

use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserSeeder
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'id' => substr(hexdec(uniqid()), -6),
            'first_name' => 'Umar',
            'last_name' => 'Farouq',
            'other_name' => 'Yusuf',
            'email' => 'umar@app.com',
            'phone_number' => '08078780858',
            'status' => 'active',
            'verification_code' => mt_rand(100000, 999999),
            'email_verified_at' => time(),
            'password' => Hash::make('password')
        ]);
    }
}
