<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.partials.favicon')
    <title>Mídia — {{ config('app.name') }}</title>
    @vite(['resources/css/components/index-page.css'])
</head>
<body class="index-page">
    <div class="index-page-container">
        <header class="index-page-header">
            <div>
                <h1>Mídia</h1>
                <p>Biblioteca de imagens para reutilizar nos posts.</p>
            </div>
        </header>
        @if (session('success'))
            <div class="index-page-alert index-page-alert--success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="index-page-alert">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('midia.store') }}" enctype="multipart/form-data" class="index-page-upload">
            @csrf
            <div class="index-page-search">
                <input type="file" name="file" accept="image/*" required>
            </div>
            <div class="index-page-search">
                <input type="text" name="alt" value="{{ old('alt') }}" placeholder="Texto alternativo">
            </div>
            <button type="submit" class="index-page-btn">Enviar imagem</button>
        </form>
        <form method="GET" action="{{ route('midia.index') }}" class="index-page-toolbar">
            <div class="index-page-search">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Buscar por arquivo ou texto alternativo...">
            </div>
            <button type="submit" class="index-page-btn index-page-btn--secondary">Filtrar</button>
        </form>
        <div class="index-page-card">
            @if ($medias->isEmpty())
                <div class="index-page-empty">
                    <h2>Nenhuma imagem encontrada</h2>
                    <p>Envie a primeira imagem da biblioteca.</p>
                </div>
            @else
                <div class="index-page-media-grid">
                    @foreach ($medias as $media)
                        <article class="index-page-media-card">
                            <img src="{{ $media->url('thumbnail') }}" alt="{{ $media->alt ?? $media->filename }}">
                            <div class="index-page-media-body">
                                <strong>{{ $media->filename }}</strong>
                                <span>{{ $media->width }}×{{ $media->height }}</span>
                                <form method="POST" action="{{ route('midia.resize', $media) }}">
                                    @csrf
                                    <button type="submit" class="index-page-btn index-page-btn--secondary">Redimensionar</button>
                                </form>
                                <form method="POST" action="{{ route('midia.destroy', $media) }}" onsubmit="return confirm('Excluir esta imagem?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="index-page-btn index-page-btn--secondary">Excluir</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="index-page-footer">
                    <span>Exibindo {{ $medias->firstItem() }}–{{ $medias->lastItem() }} de {{ $medias->total() }} imagens</span>
                    @if ($medias->hasPages())
                        {{ $medias->links('partials.pagination') }}
                    @endif
                </div>
            @endif
        </div>
    </div>
</body>
</html>
