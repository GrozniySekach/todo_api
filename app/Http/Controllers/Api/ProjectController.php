<?php

namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controller;

class ProjectController extends Controller
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
        $projects = $request->user()->projects()->with(['tasks', 'users'])->get();
        return \App\Http\Resources\ProjectResource::collection($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $project = Project::create($request->validated());
        
        // Добавляем текущего пользователя как участника проекта
        $project->users()->attach($request->user()->id);
        
        return new \App\Http\Resources\ProjectResource($project->load('users'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        Gate::authorize('view', $project);
        return new \App\Http\Resources\ProjectResource($project->load(['tasks', 'users']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        Gate::authorize('update', $project);
        $project->update($request->validated());
        return new \App\Http\Resources\ProjectResource($project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        Gate::authorize('delete', $project);
        $project->delete();
        return response()->noContent();
    }

    public function addUser(Request $request, Project $project)
    {
        Gate::authorize('update', $project);
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
        
        $project->users()->syncWithoutDetaching([$request->user_id]);
        
        return new \App\Http\Resources\ProjectResource($project->load('users'));
    }

    public function removeUser(Request $request, Project $project)
    {
        Gate::authorize('update', $project);
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
        
        $project->users()->detach($request->user_id);
        
        return new \App\Http\Resources\ProjectResource($project->load('users'));
    }
}
