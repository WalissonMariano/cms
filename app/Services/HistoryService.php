<?php

namespace App\Services;

use App\Models\History;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class HistoryService
{
    public function record(Model $auditable, string $action, ?User $actor = null): History
    {
        $actor ??= Auth::user();

        return History::create([
            'user_id' => $actor->id,
            'auditable_type' => $auditable->getMorphClass(),
            'auditable_id' => $auditable->getKey(),
            'action' => $action,
        ]);
    }

    public function recordCreated(Model $auditable, ?User $actor = null): History
    {
        return $this->record($auditable, History::ACTION_CREATED, $actor);
    }

    public function recordUpdated(Model $auditable, ?User $actor = null): History
    {
        return $this->record($auditable, History::ACTION_UPDATED, $actor);
    }

    public function recordDeleted(Model $auditable, ?User $actor = null): History
    {
        return $this->record($auditable, History::ACTION_DELETED, $actor);
    }
}
