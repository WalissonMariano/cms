<?php

namespace App\Services\Posts;

use App\Models\Posts\Category;
use App\Models\Posts\Post;
use App\Models\Posts\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PublishedPostService
{
    // Lista os posts publicados, dez por página.
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = $this->published()->with(['categories', 'tags', 'ogMedia']);
        $this->filterTaxonomy($query, $filters);

        return $query->orderByDesc('published_at')->orderByDesc('id')->paginate(10);
    }

    // Busca um post publicado pelo id.
    public function find(int $id): Post
    {
        return $this->failIfMissing($this->detail()->find($id));
    }

    // Busca um post publicado pelo slug.
    public function findBySlug(string $slug): Post
    {
        return $this->failIfMissing($this->detail()->where('slug', $slug)->first());
    }

    // Lista as categorias com a quantidade de posts publicados.
    public function categories(): Collection
    {
        return $this->taxonomy(Category::query());
    }

    // Lista as tags com a quantidade de posts publicados.
    public function tags(): Collection
    {
        return $this->taxonomy(Tag::query());
    }

    // Monta a consulta do post publicado com as relações do detalhe.
    private function detail(): Builder
    {
        return $this->published()->with(['categories', 'tags', 'ogMedia', 'user']);
    }

    // Restringe a consulta aos posts já publicados.
    private function published(): Builder
    {
        return $this->onlyPublished(Post::query());
    }

    // Aplica status publicado e data de publicação já vencida.
    private function onlyPublished(Builder $query): Builder
    {
        $table = $query->getModel()->getTable();

        return $query
            ->where("{$table}.status", Post::STATUS_PUBLISHED)
            ->whereNotNull("{$table}.published_at")
            ->where("{$table}.published_at", '<=', now());
    }

    // Filtra a listagem pelo slug da categoria e da tag.
    private function filterTaxonomy(Builder $query, array $filters): void
    {
        $category = $filters['category'] ?? null;
        $tag = $filters['tag'] ?? null;

        if (filled($category)) {
            $query->whereHas('categories', fn ($inner) => $inner->where('slug', $category));
        }

        if (filled($tag)) {
            $query->whereHas('tags', fn ($inner) => $inner->where('slug', $tag));
        }
    }

    // Ordena a taxonomia e conta só os posts publicados.
    private function taxonomy(Builder $query): Collection
    {
        return $query
            ->withCount(['posts as posts_count' => fn ($posts) => $this->onlyPublished($posts)])
            ->orderBy('name')
            ->get();
    }

    // Responde 404 quando o post não está publicado.
    private function failIfMissing(?Post $post): Post
    {
        if ($post === null) {
            abort(404, 'Post não encontrado.');
        }

        return $post;
    }
}
