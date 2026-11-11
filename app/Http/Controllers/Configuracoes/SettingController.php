<?php

namespace App\Http\Controllers\Configuracoes;

use App\Http\Controllers\Controller;
use App\Models\Midia\Media;
use App\Services\Configuracoes\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(private readonly SettingService $settings)
    {
        $this->middleware('admin');
    }

    // Exibe o formulário das configurações do blog.
    public function edit(): View
    {
        return view('configuracoes.form-settings', [
            'settings' => $this->settings->values(),
            'medias' => Media::query()->latest()->get(),
        ]);
    }

    // Grava as configurações e permanece na tela.
    public function update(Request $request): RedirectResponse
    {
        $this->settings->save($this->validated($request), $request->file('logo'), $request->user());

        return redirect()->route('configuracoes.edit')->with('success', 'Configurações salvas com sucesso.');
    }

    // Valida os campos da tela de configurações.
    private function validated(Request $request): array
    {
        return $request->validate([
            'site_title' => ['nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'logo_media_id' => ['nullable', 'integer', 'exists:medias,id'],
            'logo' => ['nullable', 'image', 'max:5120'],
            'posts_per_page' => ['required', 'integer', 'min:1', 'max:100'],
            'social_instagram' => ['nullable', 'url', 'max:255'],
            'social_facebook' => ['nullable', 'url', 'max:255'],
            'social_x' => ['nullable', 'url', 'max:255'],
            'social_youtube' => ['nullable', 'url', 'max:255'],
            'social_linkedin' => ['nullable', 'url', 'max:255'],
            'social_github' => ['nullable', 'url', 'max:255'],
            'social_email' => ['nullable', 'email', 'max:255'],
            'tracking_code' => ['nullable', 'string', 'max:5000'],
        ]);
    }
}
