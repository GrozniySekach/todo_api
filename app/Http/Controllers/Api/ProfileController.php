<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $profiles = Profile::with('user')->get();
        return \App\Http\Resources\ProfileResource::collection($profiles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProfileRequest $request)
    {
        Log::debug('Creating profile:', $request->validated());

        try {
            $profile = Profile::create($request->validated());
            return new ProfileResource($profile);
        } catch (\Exception $e) {
            Log::error('Profile error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Profile creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $profile = Profile::findOrFail($id);
            return new \App\Http\Resources\ProfileResource($profile);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Profile not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $profile = Profile::findOrFail($id);

        // Получаем текущего пользователя
        $user = Auth::user();

        if ($user->id !== $profile->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $profile->update($request->all());
        return response()->json($profile);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $profile = Profile::findOrFail($id);

        // Альтернативный способ получения пользователя
        $currentUserId = Auth::id();

        if ($currentUserId !== $profile->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $profile->delete();
        return response()->noContent();
    }
}
