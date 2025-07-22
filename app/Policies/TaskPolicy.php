<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task)
{
    // Разрешаем просмотр если:
    // 1. Пользователь - автор ИЛИ
    // 2. Соисполнитель ИЛИ
    // 3. Задача удалена, но пользователь - автор
    return $task->author_id === $user->id 
           || $task->sharedUsers->contains($user)
           || ($task->trashed() && $task->author_id === $user->id);
}

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task)
    {
        return $task->author_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task)
    {
        return $task->author_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task)
    {
        return $task->author_id === $user->id;
    }
}
