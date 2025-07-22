<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = User::factory(10)->create([
            'password' => Hash::make('password'),
        ]);
        
        $users->each(function ($user) {
            Profile::factory()->create(['user_id' => $user->id]);
        });
        
        // Создаем тестового пользователя
        $testUser = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
        
        Profile::create([
            'user_id' => $testUser->id,
            'bio' => 'Test bio',
            'phone' => '1234567890',
            'address' => 'Test address',
        ]);
    }
}
