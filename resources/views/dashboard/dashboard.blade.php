<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.partials.favicon')
    <title>Dashboard — {{ config('app.name') }}</title>
    @vite(['resources/css/components/dashboard.css'])
</head>
<body class="dashboard-page">
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1>Dashboard</h1>
            <p>Visão geral de usuários, grupos e histórico.</p>
        </header>

        <section class="dashboard-stats">
            <div class="dashboard-stat-card">
                <div class="dashboard-stat-icon dashboard-stat-icon--blue">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </div>
                <div class="dashboard-stat-info">
                    <strong>{{ $stats['users'] }}</strong>
                    <span>Usuários</span>
                </div>
            </div>

            <div class="dashboard-stat-card">
                <div class="dashboard-stat-icon dashboard-stat-icon--gray">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div class="dashboard-stat-info">
                    <strong>{{ $stats['active_users'] }}</strong>
                    <span>Usuários ativos</span>
                </div>
            </div>

            <div class="dashboard-stat-card">
                <div class="dashboard-stat-icon dashboard-stat-icon--blue">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                </div>
                <div class="dashboard-stat-info">
                    <strong>{{ $stats['groups'] }}</strong>
                    <span>Grupos</span>
                </div>
            </div>

            <div class="dashboard-stat-card">
                <div class="dashboard-stat-icon dashboard-stat-icon--dark">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div class="dashboard-stat-info">
                    <strong>{{ $stats['histories'] }}</strong>
                    <span>Registros de histórico</span>
                </div>
            </div>
        </section>

        <section class="dashboard-grid">
            <div class="dashboard-card">
                <h2>Histórico por ação</h2>
                <div class="dashboard-bars">
                    @foreach ($actionLabels as $action => $label)
                        <div class="dashboard-bar-item">
                            <span class="dashboard-bar-label">{{ $label }}</span>
                            <div class="dashboard-bar-track">
                                <div
                                    class="dashboard-bar-fill {{ $loop->index === 1 ? 'dashboard-bar-fill--progress' : ($loop->last ? 'dashboard-bar-fill--resolved' : '') }}"
                                    style="width: {{ $actionBars[$action] }}%"
                                ></div>
                            </div>
                            <span class="dashboard-bar-value">{{ $actionCounts[$action] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="dashboard-card">
                <h2>Usuários por grupo</h2>
                <div class="dashboard-severity-list">
                    @forelse ($groups as $group)
                        <div class="dashboard-severity-item">
                            <span>{{ $group->description }}</span>
                            <span class="dashboard-badge dashboard-badge--medium">
                                {{ $group->users_count }} {{ $group->users_count === 1 ? 'usuário' : 'usuários' }}
                            </span>
                        </div>
                    @empty
                        <div class="dashboard-severity-item">
                            <span>Nenhum grupo cadastrado</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="dashboard-card dashboard-card--wide">
                <h2>Atividade da semana</h2>
                <div class="dashboard-activity">
                    @foreach ($weeklyActivity as $day)
                        <div class="dashboard-activity-day">
                            <div
                                class="dashboard-activity-bar"
                                style="height: {{ max($day['height'], 5) }}%"
                                title="{{ $day['count'] }} {{ $day['count'] === 1 ? 'registro' : 'registros' }}"
                            ></div>
                            <span>{{ $day['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</body>
</html>
