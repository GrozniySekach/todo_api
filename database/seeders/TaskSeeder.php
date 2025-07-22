<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $projects = Project::all();
        $tags = Tag::all();
        
        Task::factory(20)->create()->each(function ($task) use ($users, $projects, $tags) {
            // Привязываем к проекту (50% chance)
            if (rand(0, 1)) {
                $task->project()->associate($projects->random())->save();
            }
            
            // Добавляем соисполнителей
            $task->sharedUsers()->attach(
                $users->where('id', '!=', $task->author_id)
                    ->random(rand(0, 2))
                    ->pluck('id')
                    ->toArray()
            );
            
            // Добавляем теги
            $task->tags()->attach(
                $tags->random(rand(1, 3))->pluck('id')->toArray()
            );
        });
    }
}
