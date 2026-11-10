<?php

namespace App\Models\Configuracoes;

use App\Models\History;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    // Histórico de alterações desta configuração.
    public function auditHistories(): MorphMany
    {
        return $this->morphMany(History::class, 'auditable')->latest();
    }
}
