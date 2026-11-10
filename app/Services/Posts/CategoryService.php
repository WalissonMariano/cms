<?php

namespace App\Services\Posts;

use App\Models\Posts\Category;
use App\Services\HistoryService;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    public function __construct(
        private readonly SlugService $slugs,
        private readonly HistoryService $history,
    ) {}

    // Cadastra a categoria e registra o histórico.
    public function create(array $data): Category
    {
        $this->assertParent($data['parent_id'] ?? null, null);
        $category = Category::create($this->attributes($data));
        $this->history->recordCreated($category);

        return $category;
    }

    // Atualiza a categoria e registra o histórico.
    public function update(Category $category, array $data): Category
    {
        $this->assertParent($data['parent_id'] ?? null, $category);
        $category->update($this->attributes($data, $category));
        $this->history->recordUpdated($category);

        return $category;
    }

    // Exclui a categoria e registra o histórico.
    public function delete(Category $category): void
    {
        $this->history->recordDeleted($category);
        $category->delete();
    }

    // Monta os campos persistidos da categoria.
    private function attributes(array $data, ?Category $category = null): array
    {
        return [
            'name' => $data['name'],
            'slug' => $this->slugs->unique(($data['slug'] ?? null) ?: $data['name'], 'categories', $category?->id),
            'parent_id' => ($data['parent_id'] ?? null) ?: null,
            'description' => $data['description'] ?? null,
        ];
    }

    // Impede que a categoria seja pai dela mesma ou de um ancestral.
    private function assertParent(mixed $parentId, ?Category $category): void
    {
        $parentId = (int) $parentId;

        if ($parentId === 0 || ! $category) {
            return;
        }

        if ($parentId === $category->id || $this->isDescendant($category, $parentId)) {
            throw ValidationException::withMessages([
                'parent_id' => 'A categoria não pode ser pai dela mesma.',
            ]);
        }
    }

    // Verifica se o pai escolhido está abaixo da categoria.
    private function isDescendant(Category $category, int $parentId): bool
    {
        $parent = Category::query()->find($parentId);

        while ($parent?->parent_id) {
            if ($parent->parent_id === $category->id) {
                return true;
            }

            $parent = $parent->parent;
        }

        return false;
    }
}
