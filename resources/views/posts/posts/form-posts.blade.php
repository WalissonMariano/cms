@php
    $isEdit = isset($post);
    $selectedCategories = old('categories', $isEdit ? $post->categories->pluck('id')->all() : []);
    $selectedTags = old('tags', $isEdit ? $post->tags->pluck('id')->all() : []);
    $publishedAt = old('published_at', $isEdit ? $post->published_at?->format('Y-m-d\TH:i') : '');
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.partials.favicon')
    <title>{{ $isEdit ? 'Editar post' : 'Novo post' }} — {{ config('app.name') }}</title>
    @vite(['resources/css/components/form-page.css', 'resources/js/editors/post.js'])
</head>
<body class="form-page">
    <div class="form-page-container">
        <header class="form-page-header">
            <h1>{{ $isEdit ? 'Editar post' : 'Novo post' }}</h1>
            <p>{{ $isEdit ? 'Atualize o conteúdo, a taxonomia e o SEO.' : 'Preencha o conteúdo do novo post.' }}</p>
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

            <form id="post-form" method="POST" action="{{ $isEdit ? route('posts.update', $post) : route('posts.store') }}">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="form-page-grid">
                    <div class="form-page-field">
                        <label for="title">Título</label>
                        <input type="text" id="title" name="title" value="{{ old('title', $post->title ?? '') }}" class="@error('title') is-invalid @enderror" required>
                        @error('title') <span class="form-page-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-page-field">
                        <label for="slug">Slug</label>
                        <input type="text" id="slug" name="slug" value="{{ old('slug', $post->slug ?? '') }}" class="@error('slug') is-invalid @enderror">
                        <span class="form-page-hint">Deixe em branco para gerar a partir do título.</span>
                        @error('slug') <span class="form-page-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-page-field form-page-field--full">
                        <label for="excerpt">Resumo</label>
                        <textarea id="excerpt" name="excerpt" rows="3" class="@error('excerpt') is-invalid @enderror">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
                        @error('excerpt') <span class="form-page-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-page-field form-page-field--full">
                        <label for="content">Conteúdo</label>
                        <div class="form-page-editor">
                            <div class="form-page-editor-toolbar">
                                <button type="button" class="form-page-editor-btn" data-editor-action="bold">Negrito</button>
                                <button type="button" class="form-page-editor-btn" data-editor-action="italic">Itálico</button>
                                <button type="button" class="form-page-editor-btn" data-editor-action="heading">Título</button>
                                <button type="button" class="form-page-editor-btn" data-editor-action="bullet">Lista</button>
                                <button type="button" class="form-page-editor-btn" data-editor-action="ordered">Numerada</button>
                                <button type="button" class="form-page-editor-btn" data-editor-action="link">Link</button>
                            </div>
                            <div data-post-editor></div>
                        </div>
                        <textarea id="content" name="content" hidden>{{ old('content', $post->content ?? '') }}</textarea>
                        @error('content') <span class="form-page-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-page-field">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            @foreach (\App\Models\Posts\Post::STATUS_LABELS as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $post->status ?? 'draft') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status') <span class="form-page-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-page-field">
                        <label for="published_at">Data de publicação</label>
                        <input type="datetime-local" id="published_at" name="published_at" value="{{ $publishedAt }}">
                        @error('published_at') <span class="form-page-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-page-field form-page-field--full">
                        <label>Categorias</label>
                        @foreach ($categories as $category)
                            <label class="form-page-checkbox">
                                <input type="checkbox" name="categories[]" value="{{ $category->id }}" @checked(in_array($category->id, $selectedCategories))>
                                <span>{{ $category->parent ? $category->parent->name.' / ' : '' }}{{ $category->name }}</span>
                            </label>
                        @endforeach
                        @error('categories') <span class="form-page-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-page-field form-page-field--full">
                        <label>Tags</label>
                        @foreach ($tags as $tag)
                            <label class="form-page-checkbox">
                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" @checked(in_array($tag->id, $selectedTags))>
                                <span>{{ $tag->name }}</span>
                            </label>
                        @endforeach
                        <input type="text" name="new_tags" value="{{ old('new_tags') }}" placeholder="Novas tags, separadas por vírgula">
                        @error('new_tags') <span class="form-page-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-page-field">
                        <label for="meta_title">Meta title</label>
                        <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $post->meta_title ?? '') }}">
                    </div>
                    <div class="form-page-field">
                        <label for="meta_description">Meta description</label>
                        <textarea id="meta_description" name="meta_description" rows="3">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
                    </div>
                    <div class="form-page-field form-page-field--full">
                        <label>Imagem de compartilhamento</label>
                        <div class="form-page-media-picker">
                            <label class="form-page-media-option">
                                <input type="radio" name="og_media_id" value="" @checked(old('og_media_id', $post->og_media_id ?? '') == '')>
                                Nenhuma
                            </label>
                            @foreach ($medias as $media)
                                <label class="form-page-media-option">
                                    <img src="{{ $media->url('thumbnail') }}" alt="{{ $media->alt ?? $media->filename }}">
                                    <span>
                                        <input type="radio" name="og_media_id" value="{{ $media->id }}" @checked((string) old('og_media_id', $post->og_media_id ?? '') === (string) $media->id)>
                                        OG
                                    </span>
                                    <button type="button" data-insert-image="{{ $media->url('medium') }}">Inserir</button>
                                </label>
                            @endforeach
                        </div>
                        @error('og_media_id') <span class="form-page-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </form>

            <div class="form-page-actions">
                @if ($isEdit)
                    <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('{{ $post->status === 'trashed' ? 'Excluir este post de vez?' : 'Mover este post para a lixeira?' }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="form-page-btn form-page-btn--danger">
                            {{ $post->status === 'trashed' ? 'Excluir definitivamente' : 'Mover para a lixeira' }}
                        </button>
                    </form>
                @endif
                <div class="form-page-actions-group">
                    <a href="{{ route('posts.index') }}" class="form-page-btn form-page-btn--secondary">Cancelar</a>
                    <button type="submit" form="post-form" class="form-page-btn form-page-btn--primary">
                        {{ $isEdit ? 'Salvar alterações' : 'Cadastrar post' }}
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
