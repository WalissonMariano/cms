<?php

namespace App\Http\Controllers\Midia;

use App\Http\Controllers\Controller;
use App\Models\Midia\Media;
use App\Services\Midia\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function __construct(private readonly MediaService $media)
    {
        $this->middleware('admin');
    }

    // Lista a biblioteca com busca por nome ou texto alternativo.
    public function index(Request $request): View
    {
        $medias = Media::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim();
                $query->where(function ($inner) use ($search) {
                    $inner->where('filename', 'like', "%{$search}%")
                        ->orWhere('alt', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('midia.index-medias', compact('medias'));
    }

    // Recebe o upload e volta para a biblioteca.
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'file' => ['required', 'image', 'max:5120'],
            'alt' => ['nullable', 'string', 'max:255'],
        ]);

        $this->media->store($request->file('file'), $request->user(), $data['alt'] ?? null);

        return redirect()->route('midia.index')->with('success', 'Imagem enviada com sucesso.');
    }

    // Gera novamente a miniatura e o tamanho médio.
    public function resize(Media $media): RedirectResponse
    {
        $this->media->resize($media);

        return redirect()->route('midia.index')->with('success', 'Tamanhos da imagem atualizados.');
    }

    // Exclui a imagem da biblioteca.
    public function destroy(Media $media): RedirectResponse
    {
        $this->media->delete($media);

        return redirect()->route('midia.index')->with('success', 'Imagem excluída com sucesso.');
    }
}
