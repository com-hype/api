<?php

namespace Database\Seeders;

use App\Models\Interest;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ProjectsSeeder extends Seeder
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
            $project = Project::create([
                'user_id' => $i + 1,
                'name' => $faker->company(),
                'title' => $faker->realText($maxNbChars = 20, $indexSize = 2),
                'description' => $faker->realText(),
                'avatar' => $faker->imageUrl(100, 100),
            ]);

            $project->crowdfunding()->create([
                'goal' => $faker->numberBetween(3000, 50000),
                'description' => $faker->realText(),
            ]);

            for ($j = 0; $j < rand(1, 9); $j++) {
                $project->images()->create([
                    'url' => $faker->imageUrl(300, 300),
                ]);
            }

            $interests = Interest::all()->random(4);
            foreach ($interests as $interest) {
                $project->categories()->attach($interest->id);
            }
        }
    }
}
