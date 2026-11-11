<?php

namespace App\Http\Controllers\Api\Posts;

use App\Http\Controllers\Controller;
use App\Http\Resources\Posts\CategoryResource;
use App\Services\Posts\PublishedPostService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function __construct(private readonly PublishedPostService $posts) {}

    // Lista as categorias com a quantidade de posts publicados.
    public function index(): AnonymousResourceCollection
    {
        return CategoryResource::collection($this->posts->categories());
    }
}
