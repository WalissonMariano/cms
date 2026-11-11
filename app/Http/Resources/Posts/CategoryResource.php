<?php

namespace App\Http\Resources\Posts;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    // Monta a categoria exposta para o blog.
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'parent_id' => $this->parent_id,
            'posts_count' => $this->when(
                array_key_exists('posts_count', $this->resource->getAttributes()),
                $this->posts_count,
            ),
        ];
    }
}
