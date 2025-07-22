<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'completed' => $this->completed,
            'author' => new UserResource($this->whenLoaded('author')),
            'project' => new ProjectResource($this->whenLoaded('project')),
            'shared_users' => UserResource::collection($this->whenLoaded('sharedUsers')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_deleted' => $this->trashed(),
            'deleted_at' => $this->when($this->trashed(), $this->deleted_at),
        ];
    }
}
