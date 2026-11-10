@php
    use App\Models\History;
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.partials.favicon')
    <title>Home — {{ config('app.name') }}</title>
    @vite(['resources/css/components/home.css'])
</head>
<body class="home-page">
    <div class="home-container">
        <header class="home-header">
            <h1>Bem-vindo de volta</h1>
            <p>Acompanhe usuários, grupos e o histórico do sistema.</p>
        </header>

        <section class="home-stats">
            <div class="home-stat-card">
                <div class="home-stat-icon home-stat-icon--open">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </div>
                <div class="home-stat-info">
                    <strong>{{ $stats['users'] }}</strong>
                    <span>Usuários</span>
                </div>
            </div>

            <div class="home-stat-card">
                <div class="home-stat-icon home-stat-icon--progress">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div class="home-stat-info">
                    <strong>{{ $stats['active_users'] }}</strong>
                    <span>Usuários ativos</span>
                </div>
            </div>

            <div class="home-stat-card">
                <div class="home-stat-icon home-stat-icon--resolved">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                </div>
                <div class="home-stat-info">
                    <strong>{{ $stats['groups'] }}</strong>
                    <span>Grupos</span>
                </div>
            </div>

            <div class="home-stat-card">
                <div class="home-stat-icon home-stat-icon--total">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div class="home-stat-info">
                    <strong>{{ $stats['histories'] }}</strong>
                    <span>Registros de histórico</span>
                </div>
            </div>
        </section>

        <section class="home-grid">
            <div class="home-card">
                <h2>Ações rápidas</h2>
                <div class="home-actions">
                    <a href="{{ route('dashboard') }}" class="home-action-btn home-action-btn--primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                        </svg>
                        Ver dashboard
                    </a>
                    <a href="{{ route('account.index') }}" class="home-action-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        Minha conta
                    </a>
                </div>
            </div>

            <div class="home-card">
                <h2>Atividade recente</h2>
                @if ($recentHistories->isEmpty())
                    <p>Ainda não há ações registradas no sistema.</p>
                @else
                    <ul class="home-recent-list">
                        @foreach ($recentHistories as $history)
                            <li class="home-recent-item">
                                <div class="home-recent-item-info">
                                    <strong>{{ $history->auditableLabel() }}</strong>
                                    <span>{{ $history->user?->name ?? '—' }} · {{ $history->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
                                </div>
                                <span class="home-badge home-badge--open">
                                    {{ History::actionLabel($history->action) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>
    </div>
</body>
</html>
