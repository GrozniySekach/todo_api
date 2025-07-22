<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    // Связь один-ко-многим с задачами
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // Связь многие-ко-многим с пользователями
    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user');
    }
}
