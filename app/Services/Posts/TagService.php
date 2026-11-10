<?php

namespace App\Services\Posts;

use App\Models\Posts\Tag;
use App\Services\HistoryService;
use Illuminate\Support\Str;

class TagService
{
    public function __construct(
        private readonly SlugService $slugs,
        private readonly HistoryService $history,
    ) {}

    // Cadastra a tag e registra o histórico.
    public function create(array $data): Tag
    {
        $tag = Tag::create($this->attributes($data));
        $this->history->recordCreated($tag);

        return $tag;
    }

    // Atualiza a tag e registra o histórico.
    public function update(Tag $tag, array $data): Tag
    {
        $tag->update($this->attributes($data, $tag));
        $this->history->recordUpdated($tag);

        return $tag;
    }

    // Exclui a tag e registra o histórico.
    public function delete(Tag $tag): void
    {
        $this->history->recordDeleted($tag);
        $tag->delete();
    }

    // Reúne ids existentes com as tags novas digitadas.
    public function resolveIds(array $ids, ?string $newTags): array
    {
        return collect($ids)
            ->merge($this->createFromText($newTags))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    // Cria tags a partir de nomes separados por vírgula.
    public function createFromText(?string $text): array
    {
        if (blank($text)) {
            return [];
        }

        return collect(explode(',', $text))
            ->map(fn ($name) => trim($name))
            ->filter()
            ->map(fn ($name) => $this->firstOrCreate($name)->id)
            ->all();
    }

    // Monta os campos persistidos da tag.
    private function attributes(array $data, ?Tag $tag = null): array
    {
        return [
            'name' => $data['name'],
            'slug' => $this->slugs->unique(($data['slug'] ?? null) ?: $data['name'], 'tags', $tag?->id),
        ];
    }

    // Reaproveita a tag existente ou cadastra uma nova.
    private function firstOrCreate(string $name): Tag
    {
        $existing = Tag::query()->where('slug', Str::slug($name))->orWhere('name', $name)->first();

        if ($existing) {
            return $existing;
        }

        return $this->create(['name' => $name, 'slug' => '']);
    }
}
