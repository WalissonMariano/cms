<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.partials.favicon')
    <title>Tags — {{ config('app.name') }}</title>
    @vite(['resources/css/components/index-page.css'])
</head>
<body class="index-page">
    <div class="index-page-container">
        <header class="index-page-header">
            <div>
                <h1>Tags</h1>
                <p>Marcadores livres para os posts.</p>
            </div>
            <a href="{{ route('posts.tags.create') }}" class="index-page-btn">Nova tag</a>
        </header>
        @if (session('success'))
            <div class="index-page-alert index-page-alert--success">{{ session('success') }}</div>
        @endif
        <form method="GET" action="{{ route('posts.tags.index') }}" class="index-page-toolbar">
            <div class="index-page-search">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Buscar por nome ou slug...">
            </div>
            <button type="submit" class="index-page-btn index-page-btn--secondary">Filtrar</button>
        </form>
        <div class="index-page-card">
            @if ($tags->isEmpty())
                <div class="index-page-empty">
                    <h2>Nenhuma tag encontrada</h2>
                    <p>Ainda não há tags cadastradas.</p>
                </div>
            @else
                <div class="index-page-table-wrap">
                    <table class="index-page-table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Slug</th>
                                <th>Posts</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tags as $tag)
                                <tr>
                                    <td><span class="index-page-title">{{ $tag->name }}</span></td>
                                    <td>{{ $tag->slug }}</td>
                                    <td>{{ $tag->posts_count }}</td>
                                    <td class="index-page-actions">
                                        <a href="{{ route('posts.tags.edit', $tag) }}" class="index-page-action-link">Editar</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="index-page-footer">
                    <span>Exibindo {{ $tags->firstItem() }}–{{ $tags->lastItem() }} de {{ $tags->total() }} tags</span>
                    @if ($tags->hasPages())
                        {{ $tags->links('partials.pagination') }}
                    @endif
                </div>
            @endif
        </div>
    </div>
</body>
</html>
