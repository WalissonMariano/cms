<?php

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Models\Midia\Media;
use App\Models\Posts\Category;
use App\Models\Posts\Post;
use App\Models\Posts\Tag;
use App\Services\Posts\PostService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(private readonly PostService $posts)
    {
        $this->middleware('admin');
    }

    // Lista os posts com busca e filtro de status.
    public function index(Request $request): View
    {
        $posts = Post::query()
            ->with('user')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim();
                $query->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('posts.posts.index-posts', compact('posts'));
    }

    // Exibe o formulário de novo post.
    public function create(): View
    {
        return view('posts.posts.form-posts', $this->formData());
    }

    // Grava o post e volta para a listagem.
    public function store(Request $request): RedirectResponse
    {
        $this->posts->create($this->validated($request), $request->user());

        return redirect()->route('posts.index')->with('success', 'Post cadastrado com sucesso.');
    }

    // Exibe o formulário de edição do post.
    public function edit(Post $post): View
    {
        $post->load(['categories', 'tags', 'auditHistories.user']);

        return view('posts.posts.form-posts', $this->formData($post));
    }

    // Atualiza o post e volta para a listagem.
    public function update(Request $request, Post $post): RedirectResponse
    {
        $this->posts->update($post, $this->validated($request));

        return redirect()->route('posts.index')->with('success', 'Post atualizado com sucesso.');
    }

    // Move o post para a lixeira ou exclui definitivamente.
    public function destroy(Post $post): RedirectResponse
    {
        $message = $post->status === Post::STATUS_TRASHED
            ? 'Post excluído com sucesso.'
            : 'Post movido para a lixeira.';

        $this->posts->delete($post);

        return redirect()->route('posts.index')->with('success', $message);
    }

    // Dados compartilhados pelos formulários de post.
    private function formData(?Post $post = null): array
    {
        return [
            'post' => $post,
            'categories' => Category::query()->orderBy('name')->get(),
            'tags' => Tag::query()->orderBy('name')->get(),
            'medias' => Media::query()->latest()->get(),
            'histories' => $post?->auditHistories ?? collect(),
        ];
    }

    // Valida os campos do formulário de post.
    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'status' => ['required', Rule::in(Post::STATUSES)],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'og_media_id' => ['nullable', 'integer', 'exists:medias,id'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
            'new_tags' => ['nullable', 'string', 'max:500'],
        ]);
    }
}
