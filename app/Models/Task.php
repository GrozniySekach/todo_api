<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'completed',
        'author_id',
        'project_id',
    ];
    protected $dates = ['deleted_at'];

    // Автор задачи
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Проект задачи
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Соисполнители
    public function sharedUsers()
    {
        return $this->belongsToMany(User::class, 'task_user');
    }

    // Теги
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'task_tag');
    }

    public function scopeWithTrashedData($query)
    {
        return $query->withTrashed();
    }
}
