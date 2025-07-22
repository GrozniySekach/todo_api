<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\User;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        Project::factory(5)->create()->each(function ($project) use ($users) {
            $project->users()->attach(
                $users->random(rand(1, 3))->pluck('id')->toArray()
            );
        });
    }
}
