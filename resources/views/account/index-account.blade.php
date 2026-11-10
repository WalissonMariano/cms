@php
    $initials = collect(explode(' ', $user->name))
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->join('');
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.partials.favicon')
    <title>Minha conta — {{ config('app.name') }}</title>
    @vite(['resources/css/components/account.css'])
</head>
<body class="account-page">
    <div class="account-container">
        <header class="account-header">
            <h1>Minha conta</h1>
            <p>Informações do seu perfil no sistema.</p>
        </header>

        <section class="account-profile">
            <div class="account-avatar">{{ $initials }}</div>
            <div class="account-profile-info">
                <h2>{{ $user->name }}</h2>
                <p>{{ $user->email }}</p>
                @if ($user->is_active)
                    <span class="account-badge account-badge--active">Ativo</span>
                @endif
            </div>
        </section>

        <section class="account-card">
            <h3>Dados pessoais</h3>
            <div class="account-details">
                <div class="account-field">
                    <label>Nome</label>
                    <span>{{ $user->name }}</span>
                </div>
                <div class="account-field">
                    <label>E-mail</label>
                    <span>{{ $user->email }}</span>
                </div>
                <div class="account-field">
                    <label>Grupo</label>
                    <span>{{ $user->group?->description ?? '—' }}</span>
                </div>
                <div class="account-field">
                    <label>Status</label>
                    <span>{{ $user->is_active ? 'Ativo' : 'Inativo' }}</span>
                </div>
                <div class="account-field">
                    <label>Membro desde</label>
                    <span>{{ $user->created_at?->format('d/m/Y') ?? '—' }}</span>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
