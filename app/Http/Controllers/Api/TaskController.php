<?php

namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controller;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->user()->tasks()->with(['author', 'project', 'sharedUsers', 'tags']);

        // Фильтрация по проекту
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Фильтрация по тегам
        if ($request->has('tags')) {
            $tags = explode(',', $request->tags);
            $query->whereHas('tags', function ($q) use ($tags) {
                $q->whereIn('name', $tags);
            });
        }

        // Поиск по названию/описанию
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $tasks = $query->get();

        return \App\Http\Resources\TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'author_id' => $request->user()->id,
            'project_id' => $request->project_id,
        ]);

        // Добавляем теги
        if ($request->has('tags')) {
            $tagIds = [];
            foreach ($request->tags as $tagName) {
                $tag = \App\Models\Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
            $task->tags()->sync($tagIds);
        }

        // Добавляем соисполнителей
        if ($request->has('shared_users')) {
            $task->sharedUsers()->sync($request->shared_users);
        }

        return new \App\Http\Resources\TaskResource($task->load(['author', 'project', 'sharedUsers', 'tags']));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $task = Task::find($id);

        if (!$task) {
            $trashed = Task::onlyTrashed()->find($id);
            if ($trashed) {
                return response()->json([
                    'message' => 'Task was deleted',
                    'deleted_at' => $trashed->deleted_at
                ], 410);
            }
            abort(404, 'Task not found');
        }

        Gate::authorize('view', $task);
        return new TaskResource($task);
    }

    public function showDeleted($id)
    {
        try {
            $task = Task::onlyTrashed()->findOrFail($id);
            Gate::authorize('view', $task);

            return response()->json([
                'data' => new TaskResource($task),
                'meta' => [
                    'deleted' => true,
                    'deleted_at' => $task->deleted_at->toDateTimeString()
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Task not found or not deleted',
                'available_deleted' => Task::onlyTrashed()->pluck('id')
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        Gate::authorize('update', $task);

        $task->update($request->only(['title', 'description', 'completed', 'project_id']));

        // Обновляем теги
        if ($request->has('tags')) {
            $tagIds = [];
            foreach ($request->tags as $tagName) {
                $tag = \App\Models\Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
            $task->tags()->sync($tagIds);
        }

        // Обновляем соисполнителей
        if ($request->has('shared_users')) {
            $task->sharedUsers()->sync($request->shared_users);
        }

        return new \App\Http\Resources\TaskResource($task->load(['author', 'project', 'sharedUsers', 'tags']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);
        $task->delete();
        return response()->noContent();
    }

    public function restore($id)
    {
        $task = Task::onlyTrashed()->findOrFail($id);
        Gate::authorize('restore', $task);

        $task->restore();
        return new \App\Http\Resources\TaskResource($task);
    }

    public function share(Request $request, Task $task)
    {
        Gate::authorize('update', $task);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $task->sharedUsers()->syncWithoutDetaching([$request->user_id]);

        return new \App\Http\Resources\TaskResource($task->load('sharedUsers'));
    }

    public function unshare(Request $request, Task $task)
    {
        Gate::authorize('update', $task);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $task->sharedUsers()->detach($request->user_id);

        return new \App\Http\Resources\TaskResource($task->load('sharedUsers'));
    }
}
