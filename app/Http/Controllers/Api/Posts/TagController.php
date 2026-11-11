<?php

namespace App\Http\Controllers\Api\Posts;

use App\Http\Controllers\Controller;
use App\Http\Resources\Posts\TagResource;
use App\Services\Posts\PublishedPostService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TagController extends Controller
{
    public function __construct(private readonly PublishedPostService $posts) {}

    // Lista as tags com a quantidade de posts publicados.
    public function index(): AnonymousResourceCollection
    {
        return TagResource::collection($this->posts->tags());
    }
}
