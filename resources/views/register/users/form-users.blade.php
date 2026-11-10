@php
    $isEdit = isset($user);
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.partials.favicon')
    <title>{{ $isEdit ? 'Editar usuário' : 'Novo usuário' }} — {{ config('app.name') }}</title>
    @vite(['resources/css/components/form-page.css'])
</head>
<body class="form-page">
    <div class="form-page-container">
        <header class="form-page-header">
            <h1>{{ $isEdit ? 'Editar usuário' : 'Novo usuário' }}</h1>
            <p>{{ $isEdit ? 'Atualize os dados do usuário.' : 'Preencha os dados para cadastrar um novo usuário.' }}</p>
        </header>

        <div class="form-page-card">
            @if ($errors->any())
                <div class="form-page-alert form-page-alert--error">
                    <strong>Verifique os campos abaixo:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                id="user-form"
                method="POST"
                action="{{ $isEdit ? route('register.users.update', $user) : route('register.users.store') }}"
            >
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="form-page-grid">
                    <div class="form-page-field">
                        <label for="name">Nome</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name ?? '') }}"
                            class="@error('name') is-invalid @enderror"
                            required
                        >
                        @error('name')
                            <span class="form-page-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-page-field">
                        <label for="email">E-mail</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email ?? '') }}"
                            class="@error('email') is-invalid @enderror"
                            required
                        >
                        @error('email')
                            <span class="form-page-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-page-field">
                        <label for="password">Senha</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="@error('password') is-invalid @enderror"
                            {{ $isEdit ? '' : 'required' }}
                        >
                        @if ($isEdit)
                            <span class="form-page-hint">Deixe em branco para manter a senha atual.</span>
                        @endif
                        @error('password')
                            <span class="form-page-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-page-field">
                        <label for="group_id">Grupo</label>
                        <select
                            id="group_id"
                            name="group_id"
                            class="@error('group_id') is-invalid @enderror"
                            required
                        >
                            <option value="">Selecione um grupo</option>
                            @foreach ($groups as $group)
                                <option
                                    value="{{ $group->id }}"
                                    @selected(old('group_id', $user->group_id ?? '') == $group->id)
                                >
                                    {{ $group->description }}
                                </option>
                            @endforeach
                        </select>
                        @error('group_id')
                            <span class="form-page-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-page-field form-page-field--full">
                        <input type="hidden" name="is_active" value="0">
                        <label class="form-page-checkbox">
                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                @checked(old('is_active', $user->is_active ?? true))
                            >
                            <span>Usuário ativo</span>
                        </label>
                    </div>
                </div>
            </form>

            <div class="form-page-actions">
                @if ($isEdit)
                    <form
                        method="POST"
                        action="{{ route('register.users.destroy', $user) }}"
                        class="form-page-delete-form"
                        onsubmit="return confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.');"
                    >
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="form-page-btn form-page-btn--danger">Excluir</button>
                    </form>
                @endif

                <div class="form-page-actions-group">
                    <a href="{{ route('register.users.index') }}" class="form-page-btn form-page-btn--secondary">Cancelar</a>
                    <button type="submit" form="user-form" class="form-page-btn form-page-btn--primary">
                        {{ $isEdit ? 'Salvar alterações' : 'Cadastrar usuário' }}
                    </button>
                </div>
            </div>
        </div>

        @if ($isEdit)
            @include('histories.partials.audit-list', ['histories' => $histories ?? collect()])
        @endif
    </div>
</body>
</html>
