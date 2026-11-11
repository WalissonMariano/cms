<?php

namespace App\Models\Midia;

use App\Models\Register\History;
use App\Models\Register\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Media extends Model
{
    protected $table = 'medias';

    protected $fillable = [
        'user_id',
        'disk',
        'path',
        'thumbnail_path',
        'medium_path',
        'filename',
        'mime',
        'size',
        'width',
        'height',
        'alt',
    ];

    // Usuário que enviou o arquivo.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Histórico de alterações desta mídia.
    public function auditHistories(): MorphMany
    {
        return $this->morphMany(History::class, 'auditable')->latest();
    }

    // Endereço público do original, da miniatura ou do tamanho médio.
    public function url(string $variant = 'original'): string
    {
        $path = match ($variant) {
            'thumbnail' => $this->thumbnail_path,
            'medium' => $this->medium_path,
            default => $this->path,
        };

        return '/storage/'.ltrim((string) $path, '/');
    }
}
