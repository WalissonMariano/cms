<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.partials.favicon')
    <title>Grupos — {{ config('app.name') }}</title>
    @vite(['resources/css/components/index-page.css'])
</head>
<body class="index-page">
    <div class="index-page-container">
        <header class="index-page-header">
            <div>
                <h1>Grupos</h1>
                <p>Listagem de grupos de usuários cadastrados no sistema.</p>
            </div>
        </header>

        @if (session('success'))
            <div class="index-page-alert index-page-alert--success">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('register.groups.index') }}" class="index-page-toolbar">
            <div class="index-page-search">
                <input
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Buscar por código ou descrição..."
                >
            </div>
            <button type="submit" class="index-page-btn index-page-btn--secondary">Filtrar</button>
        </form>

        <div class="index-page-card">
            @if ($groups->isEmpty())
                <div class="index-page-empty">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                    <h2>Nenhum grupo encontrado</h2>
                    <p>
                        @if (request()->filled('search'))
                            Tente ajustar os filtros ou cadastre um novo grupo.
                        @else
                            Ainda não há grupos cadastrados no sistema.
                        @endif
                    </p>
                </div>
            @else
                <div class="index-page-table-wrap">
                    <table class="index-page-table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Descrição</th>
                                <th>Usuários</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groups as $group)
                                <tr>
                                    <td>
                                        <span class="index-page-code">{{ $group->code }}</span>
                                    </td>
                                    <td>
                                        <span class="index-page-title">{{ $group->description }}</span>
                                    </td>
                                    <td>{{ $group->users_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="index-page-footer">
                    <span>
                        Exibindo {{ $groups->firstItem() }}–{{ $groups->lastItem() }} de {{ $groups->total() }} grupos
                    </span>
                    @if ($groups->hasPages())
                        {{ $groups->links('partials.pagination') }}
                    @endif
                </div>
            @endif
        </div>
    </div>
</body>
</html>
