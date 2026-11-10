<?php

namespace App\Models\Posts;

use App\Models\History;
use App\Models\Midia\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_TRASHED = 'trashed';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_PUBLISHED,
        self::STATUS_SCHEDULED,
        self::STATUS_TRASHED,
    ];

    public const STATUS_LABELS = [
        self::STATUS_DRAFT => 'Rascunho',
        self::STATUS_PUBLISHED => 'Publicado',
        self::STATUS_SCHEDULED => 'Agendado',
        self::STATUS_TRASHED => 'Lixeira',
    ];

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'og_media_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // Autor do post.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Categorias vinculadas ao post.
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    // Tags vinculadas ao post.
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    // Imagem de compartilhamento do post.
    public function ogMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'og_media_id');
    }

    // Histórico de alterações deste post.
    public function auditHistories(): MorphMany
    {
        return $this->morphMany(History::class, 'auditable')->latest();
    }

    // Rótulo do status para a listagem.
    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }
}
