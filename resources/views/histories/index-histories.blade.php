@php
    use App\Models\History;

    $typeLabels = [
        '' => 'Todos os registros',
        'user' => 'Usuários',
        'post' => 'Posts',
        'category' => 'Categorias',
        'tag' => 'Tags',
        'media' => 'Mídia',
        'setting' => 'Configurações',
    ];
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.partials.favicon')
    <title>Histórico — {{ config('app.name') }}</title>
    @vite(['resources/css/components/index-page.css'])
</head>
<body class="index-page">
    <div class="index-page-container">
        <header class="index-page-header">
            <div>
                <h1>Histórico</h1>
                <p>Registro de ações realizadas no sistema.</p>
            </div>
        </header>

        <form method="GET" action="{{ route('register.histories.index') }}" class="index-page-toolbar">
            <div class="index-page-search">
                <input
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Buscar por usuário, ação ou ID..."
                >
            </div>
            <div class="index-page-select">
                <select name="type">
                    @foreach ($typeLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="index-page-select">
                <select name="action">
                    <option value="">Todas as ações</option>
                    @foreach (History::ACTION_LABELS as $value => $label)
                        <option value="{{ $value }}" @selected(request('action') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="index-page-btn index-page-btn--secondary">Filtrar</button>
        </form>

        <div class="index-page-card">
            @if ($histories->isEmpty())
                <div class="index-page-empty">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <h2>Nenhum registro encontrado</h2>
                    <p>
                        @if (request()->hasAny(['search', 'type', 'action']))
                            Tente ajustar os filtros da consulta.
                        @else
                            Ainda não há ações registradas no sistema.
                        @endif
                    </p>
                </div>
            @else
                <div class="index-page-table-wrap">
                    <table class="index-page-table">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Usuário</th>
                                <th>Ação</th>
                                <th>Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($histories as $history)
                                <tr>
                                    <td>{{ $history->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                    <td>{{ $history->user?->name ?? '—' }}</td>
                                    <td>
                                        <span class="index-page-badge index-page-badge--neutral">
                                            {{ History::actionLabel($history->action) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="index-page-title">{{ $history->auditableLabel() }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="index-page-footer">
                    <span>
                        Exibindo {{ $histories->firstItem() }}–{{ $histories->lastItem() }} de {{ $histories->total() }} registros
                    </span>
                    @if ($histories->hasPages())
                        {{ $histories->links('partials.pagination') }}
                    @endif
                </div>
            @endif
        </div>
    </div>
</body>
</html>
