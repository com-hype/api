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
                'avatar' => "https://picsum.photos/80",
                'type' => 'raise_funds',
            ]);
            $interests = Interest::all()->random(3);

            foreach ($interests as $interest) {
                $project->categories()->attach($interest->id);
            }
        }
    }
}
