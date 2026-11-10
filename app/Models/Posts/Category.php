<?php

namespace App\Models\Posts;

use App\Models\History;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'description',
    ];

    // Categoria pai, quando houver.
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    // Categorias filhas desta categoria.
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    // Posts classificados nesta categoria.
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    // Histórico de alterações desta categoria.
    public function auditHistories(): MorphMany
    {
        return $this->morphMany(History::class, 'auditable')->latest();
    }
}
