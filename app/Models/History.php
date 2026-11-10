<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class History extends Model
{
    public const UPDATED_AT = null;

    public const ACTION_CREATED = 'created';
    public const ACTION_UPDATED = 'updated';
    public const ACTION_DELETED = 'deleted';

    public const ACTION_LABELS = [
        self::ACTION_CREATED => 'Cadastrado',
        self::ACTION_UPDATED => 'Atualizado',
        self::ACTION_DELETED => 'Excluído',
    ];

    protected $fillable = [
        'user_id',
        'auditable_type',
        'auditable_id',
        'action',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function actionLabel(string $action): string
    {
        return self::ACTION_LABELS[$action] ?? $action;
    }

    public function auditableLabel(): string
    {
        $type = class_basename((string) $this->auditable_type);

        if (! class_exists($this->auditable_type) || ! $this->auditable) {
            return match ($type) {
                'User' => 'Usuário',
                'Group' => 'Grupo',
                'Post' => 'Post',
                'Category' => 'Categoria',
                'Tag' => 'Tag',
                'Media' => 'Mídia',
                'Setting' => 'Configuração',
                default => 'Registro',
            } . ' #' . $this->auditable_id;
        }

        return match ($type) {
            'User' => 'Usuário: ' . $this->auditable->name,
            'Group' => 'Grupo: ' . ($this->auditable->description ?? $this->auditable->code),
            'Post' => 'Post: ' . $this->auditable->title,
            'Category' => 'Categoria: ' . $this->auditable->name,
            'Tag' => 'Tag: ' . $this->auditable->name,
            'Media' => 'Mídia: ' . ($this->auditable->alt ?: $this->auditable->filename),
            'Setting' => 'Configuração: ' . $this->auditable->key,
            default => 'Registro #' . $this->auditable_id,
        };
    }
}
