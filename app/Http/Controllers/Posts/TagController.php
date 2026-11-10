<?php

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Models\Posts\Tag;
use App\Services\Posts\TagService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagController extends Controller
{
    public function __construct(private readonly TagService $tags)
    {
        $this->middleware('admin');
    }

    // Lista as tags com busca por nome ou slug.
    public function index(Request $request): View
    {
        $tags = Tag::query()
            ->withCount('posts')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim();
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('posts.tags.index-tags', compact('tags'));
    }

    // Exibe o formulário de nova tag.
    public function create(): View
    {
        return view('posts.tags.form-tags');
    }

    // Grava a tag e volta para a listagem.
    public function store(Request $request): RedirectResponse
    {
        $this->tags->create($this->validated($request));

        return redirect()->route('posts.tags.index')->with('success', 'Tag cadastrada com sucesso.');
    }

    // Exibe o formulário de edição da tag.
    public function edit(Tag $tag): View
    {
        $tag->load('auditHistories.user');

        return view('posts.tags.form-tags', [
            'tag' => $tag,
            'histories' => $tag->auditHistories,
        ]);
    }

    // Atualiza a tag e volta para a listagem.
    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $this->tags->update($tag, $this->validated($request));

        return redirect()->route('posts.tags.index')->with('success', 'Tag atualizada com sucesso.');
    }

    // Exclui a tag e volta para a listagem.
    public function destroy(Tag $tag): RedirectResponse
    {
        $this->tags->delete($tag);

        return redirect()->route('posts.tags.index')->with('success', 'Tag excluída com sucesso.');
    }

    // Valida os campos do formulário de tag.
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
