<?php

namespace Database\Seeders;

use App\Models\Interest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $user = User::create([
                'first_name' => $faker->firstName(),
                'last_name' => $faker->lastName(),
                'username' => $faker->userName(),
                'email' => $faker->email(),
                'status' => 'active',
                'type' => 'presenter',
                'birthdate' => $faker->date(),
                'password' => Hash::make($faker->password()),
            ]);
            $interests = Interest::all()->random(5);

            foreach ($interests as $interest) {
                $user->interests()->attach($interest->id);
            }
        }
    }
}
