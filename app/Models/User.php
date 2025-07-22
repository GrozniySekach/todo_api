<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            //'password' => 'hashed',
        ];
    }

    // Связь один-к-одному с профилем
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    // Связь один-ко-многим с задачами (автор)
    public function tasks()
    {
        return $this->hasMany(Task::class, 'author_id');
    }

    // Связь многие-ко-многим с проектами
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user');
    }

    // Связь многие-ко-многим с задачами (соисполнители)
    public function sharedTasks()
    {
        return $this->belongsToMany(Task::class, 'task_user');
    }
}
