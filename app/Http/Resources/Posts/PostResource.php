<?php

namespace App\Http\Resources\Posts;

use Illuminate\Http\Request;

class PostResource extends PostSummaryResource
{
    // Monta o post completo para o blog.
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'content' => $this->absoluteContent(),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'author' => [
                'name' => $this->user?->name,
            ],
        ]);
    }

    // Troca os caminhos relativos das imagens por URL absoluta.
    private function absoluteContent(): ?string
    {
        if (blank($this->content)) {
            return $this->content;
        }

        $base = rtrim(url('/storage'), '/');

        return str_replace(
            ['src="/storage/', "src='/storage/"],
            ['src="'.$base.'/', "src='".$base.'/'],
            $this->content,
        );
    }
}
