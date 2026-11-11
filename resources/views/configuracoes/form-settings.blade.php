<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.partials.favicon')
    <title>Configurações — {{ config('app.name') }}</title>
    @vite(['resources/css/components/form-page.css'])
</head>
<body class="form-page">
    <div class="form-page-container">
        <header class="form-page-header">
            <h1>Configurações</h1>
            <p>Metadados do blog, paginação, redes e rastreamento.</p>
        </header>
        <div class="form-page-card">
            @if (session('success'))
                <div class="form-page-alert form-page-alert--success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="form-page-alert form-page-alert--error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form id="settings-form" method="POST" action="{{ route('configuracoes.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-page-grid">
                    <div class="form-page-field">
                        <label for="site_title">Título do site</label>
                        <input type="text" id="site_title" name="site_title" value="{{ old('site_title', $settings['site_title']) }}">
                    </div>
                    <div class="form-page-field">
                        <label for="tagline">Slogan</label>
                        <input type="text" id="tagline" name="tagline" value="{{ old('tagline', $settings['tagline']) }}">
                    </div>
                    <div class="form-page-field">
                        <label for="posts_per_page">Posts por página</label>
                        <input type="number" id="posts_per_page" name="posts_per_page" min="1" max="100" value="{{ old('posts_per_page', $settings['posts_per_page']) }}" required>
                    </div>
                    <div class="form-page-field">
                        <label for="logo_media_id">Logotipo da biblioteca</label>
                        <select id="logo_media_id" name="logo_media_id">
                            <option value="">Nenhum</option>
                            @foreach ($medias as $media)
                                <option value="{{ $media->id }}" @selected((string) old('logo_media_id', $settings['logo_media_id']) === (string) $media->id)>{{ $media->filename }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-page-field form-page-field--full">
                        <label for="logo">Enviar novo logotipo</label>
                        <input type="file" id="logo" name="logo" accept="image/*">
                    </div>
                    <div class="form-page-field">
                        <label for="social_instagram">Instagram</label>
                        <input type="url" id="social_instagram" name="social_instagram" value="{{ old('social_instagram', $settings['social_instagram']) }}">
                    </div>
                    <div class="form-page-field">
                        <label for="social_facebook">Facebook</label>
                        <input type="url" id="social_facebook" name="social_facebook" value="{{ old('social_facebook', $settings['social_facebook']) }}">
                    </div>
                    <div class="form-page-field">
                        <label for="social_x">X</label>
                        <input type="url" id="social_x" name="social_x" value="{{ old('social_x', $settings['social_x']) }}">
                    </div>
                    <div class="form-page-field">
                        <label for="social_youtube">YouTube</label>
                        <input type="url" id="social_youtube" name="social_youtube" value="{{ old('social_youtube', $settings['social_youtube']) }}">
                    </div>
                    <div class="form-page-field">
                        <label for="social_linkedin">LinkedIn</label>
                        <input type="url" id="social_linkedin" name="social_linkedin" value="{{ old('social_linkedin', $settings['social_linkedin']) }}">
                    </div>
                    <div class="form-page-field">
                        <label for="social_github">GitHub</label>
                        <input type="url" id="social_github" name="social_github" value="{{ old('social_github', $settings['social_github']) }}">
                    </div>
                    <div class="form-page-field">
                        <label for="social_email">E-mail</label>
                        <input type="email" id="social_email" name="social_email" value="{{ old('social_email', $settings['social_email']) }}">
                    </div>
                    <div class="form-page-field form-page-field--full">
                        <label for="tracking_code">Códigos de rastreamento</label>
                        <textarea id="tracking_code" name="tracking_code" rows="6">{{ old('tracking_code', $settings['tracking_code']) }}</textarea>
                    </div>
                </div>
            </form>
            <div class="form-page-actions">
                <div class="form-page-actions-group">
                    <button type="submit" form="settings-form" class="form-page-btn form-page-btn--primary">Salvar configurações</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
