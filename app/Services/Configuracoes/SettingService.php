<?php

namespace App\Services\Configuracoes;

use App\Models\Configuracoes\Setting;
use App\Models\Register\History;
use App\Models\Register\User;
use App\Services\HistoryService;
use App\Services\Midia\MediaService;
use Illuminate\Http\UploadedFile;

class SettingService
{
    public const KEYS = [
        'site_title',
        'tagline',
        'logo_media_id',
        'posts_per_page',
        'social_instagram',
        'social_facebook',
        'social_x',
        'social_youtube',
        'social_linkedin',
        'social_github',
        'social_email',
        'tracking_code',
    ];

    public function __construct(
        private readonly HistoryService $history,
        private readonly MediaService $media,
    ) {}

    // Devolve as configurações gravadas, com o padrão de paginação.
    public function values(): array
    {
        $stored = Setting::query()->pluck('value', 'key');

        return collect(self::KEYS)
            ->mapWithKeys(fn (string $key) => [$key => $stored[$key] ?? $this->default($key)])
            ->all();
    }

    // Grava as chaves, o logotipo enviado e o histórico.
    public function save(array $values, ?UploadedFile $logo, User $user): void
    {
        if ($logo) {
            $values['logo_media_id'] = (string) $this->media->store($logo, $user, 'Logotipo')->id;
        }

        $anchor = $this->persist('site_title', $values['site_title'] ?? null);
        collect(self::KEYS)->reject(fn ($key) => $key === 'site_title')
            ->each(fn (string $key) => $this->persist($key, $values[$key] ?? null));

        $action = $anchor->wasRecentlyCreated ? History::ACTION_CREATED : History::ACTION_UPDATED;
        $this->history->record($anchor, $action);
    }

    // Cria ou atualiza uma chave de configuração.
    private function persist(string $key, mixed $value): Setting
    {
        return Setting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => blank($value) ? $this->default($key) : $value],
        );
    }

    // Valor inicial quando a chave ainda não foi salva.
    private function default(string $key): ?string
    {
        return $key === 'posts_per_page' ? '10' : null;
    }
}
