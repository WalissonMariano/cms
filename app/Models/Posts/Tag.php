<?php

namespace App\Models\Posts;

use App\Models\Register\History;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    // Posts que usam esta tag.
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    // Histórico de alterações desta tag.
    public function auditHistories(): MorphMany
    {
        return $this->morphMany(History::class, 'auditable')->latest();
    }
}
