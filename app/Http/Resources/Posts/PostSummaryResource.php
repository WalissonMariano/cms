<?php

namespace App\Http\Resources\Posts;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostSummaryResource extends JsonResource
{
    // Monta o resumo do post para a listagem.
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'published_at' => $this->published_at?->toIso8601String(),
            'cover' => $this->cover(),
            'categories' => CategoryResource::collection($this->categories),
            'tags' => TagResource::collection($this->tags),
        ];
    }

    // Monta a capa pública do post.
    private function cover(): ?array
    {
        $media = $this->ogMedia;

        if ($media === null) {
            return null;
        }

        return [
            'url' => url($media->url()),
            'alt' => $media->alt,
        ];
    }
}
