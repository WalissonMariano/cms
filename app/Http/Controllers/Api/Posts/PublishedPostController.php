<?php

namespace App\Http\Controllers\Api\Posts;

use App\Http\Controllers\Controller;
use App\Http\Resources\Posts\PostResource;
use App\Http\Resources\Posts\PostSummaryResource;
use App\Services\Posts\PublishedPostService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PublishedPostController extends Controller
{
    public function __construct(private readonly PublishedPostService $posts) {}

    // Lista os posts publicados.
    public function index(Request $request): AnonymousResourceCollection
    {
        $posts = $this->posts->paginate($request->only(['category', 'tag']));

        return PostSummaryResource::collection($posts);
    }

    // Exibe um post publicado pelo id.
    public function show(int $post): PostResource
    {
        return new PostResource($this->posts->find($post));
    }

    // Exibe um post publicado pelo slug.
    public function showBySlug(string $slug): PostResource
    {
        return new PostResource($this->posts->findBySlug($slug));
    }
}
