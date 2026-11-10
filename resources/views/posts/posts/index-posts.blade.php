@php
    use App\Models\Posts\Post;
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.partials.favicon')
    <title>Posts — {{ config('app.name') }}</title>
    @vite(['resources/css/components/index-page.css'])
</head>
<body class="index-page">
    <div class="index-page-container">
        <header class="index-page-header">
            <div>
                <h1>Posts</h1>
                <p>Conteúdos do blog, do rascunho à lixeira.</p>
            </div>
            <a href="{{ route('posts.create') }}" class="index-page-btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Novo post
            </a>
        </header>

        @if (session('success'))
            <div class="index-page-alert index-page-alert--success">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('posts.index') }}" class="index-page-toolbar">
            <div class="index-page-search">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Buscar por título ou slug...">
            </div>
            <div class="index-page-select">
                <select name="status">
                    <option value="">Todos os status</option>
                    @foreach (Post::STATUS_LABELS as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="index-page-btn index-page-btn--secondary">Filtrar</button>
        </form>

        <div class="index-page-card">
            @if ($posts->isEmpty())
                <div class="index-page-empty">
                    <h2>Nenhum post encontrado</h2>
                    <p>
                        @if (request()->hasAny(['search', 'status']))
                            Tente ajustar os filtros ou cadastre um novo post.
                        @else
                            Ainda não há posts cadastrados.
                        @endif
                    </p>
                </div>
            @else
                <div class="index-page-table-wrap">
                    <table class="index-page-table">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Status</th>
                                <th>Autor</th>
                                <th>Publicação</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($posts as $post)
                                <tr>
                                    <td>
                                        <span class="index-page-title">{{ $post->title }}</span>
                                        <span>{{ $post->slug }}</span>
                                    </td>
                                    <td>
                                        <span class="index-page-badge {{ $post->status === 'published' ? 'index-page-badge--active' : ($post->status === 'trashed' ? 'index-page-badge--inactive' : 'index-page-badge--neutral') }}">
                                            {{ $post->statusLabel() }}
                                        </span>
                                    </td>
                                    <td>{{ $post->user?->name ?? '—' }}</td>
                                    <td>{{ $post->published_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                    <td class="index-page-actions">
                                        <a href="{{ route('posts.edit', $post) }}" class="index-page-action-link">Editar</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="index-page-footer">
                    <span>Exibindo {{ $posts->firstItem() }}–{{ $posts->lastItem() }} de {{ $posts->total() }} posts</span>
                    @if ($posts->hasPages())
                        {{ $posts->links('partials.pagination') }}
                    @endif
                </div>
            @endif
        </div>
    </div>
</body>
</html>
