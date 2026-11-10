<?php

namespace App\Http\Controllers;

use App\Models\Configuracoes\Setting;
use App\Models\Register\History;
use App\Models\Midia\Media;
use App\Models\Posts\Category;
use App\Models\Posts\Post;
use App\Models\Posts\Tag;
use App\Models\Register\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->loadMissing('group')->isAdmin(), 403);

        $query = History::query()->with('user')->orderByDesc('created_at');
        $this->applySearch($query, $request);

        if ($type = $this->auditableType($request->string('type')->toString())) {
            $query->where('auditable_type', $type);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->string('action')->toString());
        }

        $histories = $query->paginate(20)->withQueryString();

        return view('histories.index-histories', compact('histories'));
    }

    // Restringe a consulta ao texto informado na busca.
    private function applySearch(Builder $query, Request $request): void
    {
        if (! $request->filled('search')) {
            return;
        }

        $search = $request->string('search')->trim();
        $query->where(function ($inner) use ($search) {
            $inner->whereHas('user', fn ($userQuery) => $userQuery
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"))
                ->orWhere('action', 'like', "%{$search}%")
                ->orWhere('auditable_id', 'like', "%{$search}%");
        });
    }

    // Traduz o filtro da tela para a classe auditada.
    private function auditableType(string $type): ?string
    {
        return [
            'user' => User::class,
            'post' => Post::class,
            'category' => Category::class,
            'tag' => Tag::class,
            'media' => Media::class,
            'setting' => Setting::class,
        ][$type] ?? null;
    }
}
