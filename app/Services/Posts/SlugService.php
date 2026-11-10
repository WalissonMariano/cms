<?php

namespace App\Services\Posts;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SlugService
{
    // Gera um slug único na tabela informada.
    public function unique(string $source, string $table, ?int $ignoreId = null): string
    {
        $base = Str::slug($source) ?: 'item';
        $slug = $base;
        $suffix = 2;

        while ($this->exists($table, $slug, $ignoreId)) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    // Verifica se o slug já está em uso.
    private function exists(string $table, string $slug, ?int $ignoreId): bool
    {
        return DB::table($table)
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();
    }
}
