@php $isEdit = isset($tag); @endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.partials.favicon')
    <title>{{ $isEdit ? 'Editar tag' : 'Nova tag' }} — {{ config('app.name') }}</title>
    @vite(['resources/css/components/form-page.css'])
</head>
<body class="form-page">
    <div class="form-page-container">
        <header class="form-page-header">
            <h1>{{ $isEdit ? 'Editar tag' : 'Nova tag' }}</h1>
            <p>{{ $isEdit ? 'Atualize o nome e o slug da tag.' : 'Cadastre uma tag livre para os posts.' }}</p>
        </header>
        <div class="form-page-card">
            @if ($errors->any())
                <div class="form-page-alert form-page-alert--error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form id="tag-form" method="POST" action="{{ $isEdit ? route('posts.tags.update', $tag) : route('posts.tags.store') }}">
                @csrf
                @if ($isEdit) @method('PUT') @endif
                <div class="form-page-grid">
                    <div class="form-page-field">
                        <label for="name">Nome</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $tag->name ?? '') }}" required>
                    </div>
                    <div class="form-page-field">
                        <label for="slug">Slug</label>
                        <input type="text" id="slug" name="slug" value="{{ old('slug', $tag->slug ?? '') }}">
                        <span class="form-page-hint">Deixe em branco para gerar a partir do nome.</span>
                    </div>
                </div>
            </form>
            <div class="form-page-actions">
                @if ($isEdit)
                    <form method="POST" action="{{ route('posts.tags.destroy', $tag) }}" onsubmit="return confirm('Excluir esta tag?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="form-page-btn form-page-btn--danger">Excluir</button>
                    </form>
                @endif
                <div class="form-page-actions-group">
                    <a href="{{ route('posts.tags.index') }}" class="form-page-btn form-page-btn--secondary">Cancelar</a>
                    <button type="submit" form="tag-form" class="form-page-btn form-page-btn--primary">{{ $isEdit ? 'Salvar alterações' : 'Cadastrar tag' }}</button>
                </div>
            </div>
        </div>
        @if ($isEdit)
            @include('histories.partials.audit-list', ['histories' => $histories ?? collect()])
        @endif
    </div>
</body>
</html>
