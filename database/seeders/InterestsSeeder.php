<?php

namespace Database\Seeders;

use App\Models\Interest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InterestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $interests = [
            'technologie',
            'automobile',
            'cuisine',
            'sport',
            'animaux',
            'jeux vidéos',
            'e-sport',
            'musique',
            'environnement',
            'finance',
            'social',
            'immobilier',
            'crypto-monnaie',
            'biologie',
            'education',
        ];

        foreach ($interests as $interest) {
            Interest::create([
                'name' => $interest,
            ]);
        }
    }
}
