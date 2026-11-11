<?php

namespace App\Http\Resources\Posts;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    // Monta a tag exposta para o blog.
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'posts_count' => $this->when(
                array_key_exists('posts_count', $this->resource->getAttributes()),
                $this->posts_count,
            ),
        ];
    }
}
