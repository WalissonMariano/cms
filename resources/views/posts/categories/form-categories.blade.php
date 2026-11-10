@php $isEdit = isset($category); @endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.partials.favicon')
    <title>{{ $isEdit ? 'Editar categoria' : 'Nova categoria' }} — {{ config('app.name') }}</title>
    @vite(['resources/css/components/form-page.css'])
</head>
<body class="form-page">
    <div class="form-page-container">
        <header class="form-page-header">
            <h1>{{ $isEdit ? 'Editar categoria' : 'Nova categoria' }}</h1>
            <p>{{ $isEdit ? 'Atualize o nome, o slug e a categoria pai.' : 'Cadastre uma categoria para organizar os posts.' }}</p>
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
            <form id="category-form" method="POST" action="{{ $isEdit ? route('posts.categories.update', $category) : route('posts.categories.store') }}">
                @csrf
                @if ($isEdit) @method('PUT') @endif
                <div class="form-page-grid">
                    <div class="form-page-field">
                        <label for="name">Nome</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $category->name ?? '') }}" required>
                    </div>
                    <div class="form-page-field">
                        <label for="slug">Slug</label>
                        <input type="text" id="slug" name="slug" value="{{ old('slug', $category->slug ?? '') }}">
                        <span class="form-page-hint">Deixe em branco para gerar a partir do nome.</span>
                    </div>
                    <div class="form-page-field">
                        <label for="parent_id">Categoria pai</label>
                        <select id="parent_id" name="parent_id">
                            <option value="">Nenhuma</option>
                            @foreach ($parents as $parent)
                                <option value="{{ $parent->id }}" @selected((string) old('parent_id', $category->parent_id ?? '') === (string) $parent->id)>{{ $parent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-page-field form-page-field--full">
                        <label for="description">Descrição</label>
                        <textarea id="description" name="description" rows="4">{{ old('description', $category->description ?? '') }}</textarea>
                    </div>
                </div>
            </form>
            <div class="form-page-actions">
                @if ($isEdit)
                    <form method="POST" action="{{ route('posts.categories.destroy', $category) }}" onsubmit="return confirm('Excluir esta categoria?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="form-page-btn form-page-btn--danger">Excluir</button>
                    </form>
                @endif
                <div class="form-page-actions-group">
                    <a href="{{ route('posts.categories.index') }}" class="form-page-btn form-page-btn--secondary">Cancelar</a>
                    <button type="submit" form="category-form" class="form-page-btn form-page-btn--primary">{{ $isEdit ? 'Salvar alterações' : 'Cadastrar categoria' }}</button>
                </div>
            </div>
        </div>
        @if ($isEdit)
            @include('histories.partials.audit-list', ['histories' => $histories ?? collect()])
        @endif
    </div>
</body>
</html>
