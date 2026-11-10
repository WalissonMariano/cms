<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.partials.favicon')
    <title>Categorias — {{ config('app.name') }}</title>
    @vite(['resources/css/components/index-page.css'])
</head>
<body class="index-page">
    <div class="index-page-container">
        <header class="index-page-header">
            <div>
                <h1>Categorias</h1>
                <p>Organize os posts em uma hierarquia simples.</p>
            </div>
            <a href="{{ route('posts.categories.create') }}" class="index-page-btn">Nova categoria</a>
        </header>
        @if (session('success'))
            <div class="index-page-alert index-page-alert--success">{{ session('success') }}</div>
        @endif
        <form method="GET" action="{{ route('posts.categories.index') }}" class="index-page-toolbar">
            <div class="index-page-search">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Buscar por nome ou slug...">
            </div>
            <button type="submit" class="index-page-btn index-page-btn--secondary">Filtrar</button>
        </form>
        <div class="index-page-card">
            @if ($categories->isEmpty())
                <div class="index-page-empty">
                    <h2>Nenhuma categoria encontrada</h2>
                    <p>Ainda não há categorias cadastradas.</p>
                </div>
            @else
                <div class="index-page-table-wrap">
                    <table class="index-page-table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Pai</th>
                                <th>Posts</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td><span class="index-page-title">{{ $category->name }}</span></td>
                                    <td>{{ $category->parent?->name ?? '—' }}</td>
                                    <td>{{ $category->posts_count }}</td>
                                    <td class="index-page-actions">
                                        <a href="{{ route('posts.categories.edit', $category) }}" class="index-page-action-link">Editar</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="index-page-footer">
                    <span>Exibindo {{ $categories->firstItem() }}–{{ $categories->lastItem() }} de {{ $categories->total() }} categorias</span>
                    @if ($categories->hasPages())
                        {{ $categories->links('partials.pagination') }}
                    @endif
                </div>
            @endif
        </div>
    </div>
</body>
</html>
