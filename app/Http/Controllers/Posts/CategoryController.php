<?php

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Models\Posts\Category;
use App\Services\Posts\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categories)
    {
        $this->middleware('admin');
    }

    // Lista as categorias com busca por nome ou slug.
    public function index(Request $request): View
    {
        $categories = Category::query()
            ->with('parent')
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

        return view('posts.categories.index-categories', compact('categories'));
    }

    // Exibe o formulário de nova categoria.
    public function create(): View
    {
        return view('posts.categories.form-categories', $this->formData());
    }

    // Grava a categoria e volta para a listagem.
    public function store(Request $request): RedirectResponse
    {
        $this->categories->create($this->validated($request));

        return redirect()->route('posts.categories.index')->with('success', 'Categoria cadastrada com sucesso.');
    }

    // Exibe o formulário de edição da categoria.
    public function edit(Category $category): View
    {
        $category->load('auditHistories.user');

        return view('posts.categories.form-categories', $this->formData($category));
    }

    // Atualiza a categoria e volta para a listagem.
    public function update(Request $request, Category $category): RedirectResponse
    {
        $this->categories->update($category, $this->validated($request));

        return redirect()->route('posts.categories.index')->with('success', 'Categoria atualizada com sucesso.');
    }

    // Exclui a categoria e volta para a listagem.
    public function destroy(Category $category): RedirectResponse
    {
        $this->categories->delete($category);

        return redirect()->route('posts.categories.index')->with('success', 'Categoria excluída com sucesso.');
    }

    // Dados compartilhados pelos formulários de categoria.
    private function formData(?Category $category = null): array
    {
        return [
            'category' => $category,
            'parents' => Category::query()->when($category, fn ($query) => $query->where('id', '!=', $category->id))->orderBy('name')->get(),
            'histories' => $category?->auditHistories ?? collect(),
        ];
    }

    // Valida os campos do formulário de categoria.
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
