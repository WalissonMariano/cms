<?php

namespace App\Models\Register;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
    ];

    // Lista os usuários deste grupo.
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
