<?php

namespace App\Services\Midia;

use App\Models\Midia\Media;
use App\Models\User;
use App\Services\HistoryService;
use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MediaService
{
    public function __construct(private readonly HistoryService $history) {}

    // Guarda a imagem, gera os tamanhos e registra o histórico.
    public function store(UploadedFile $file, User $user, ?string $alt = null): Media
    {
        $media = Media::create($this->attributes($file, $user, $alt));
        $this->history->recordCreated($media);

        return $media;
    }

    // Recria miniatura e tamanho médio a partir do original.
    public function resize(Media $media): Media
    {
        $extension = pathinfo($media->path, PATHINFO_EXTENSION);
        $name = pathinfo($media->path, PATHINFO_FILENAME);
        $folder = trim(dirname($media->path), '.');

        $media->thumbnail_path = $this->derivative($media->path, 400, $name, $folder, $extension);
        $media->medium_path = $this->derivative($media->path, 1200, $name, $folder, $extension);
        $media->save();
        $this->history->recordUpdated($media);

        return $media;
    }

    // Remove os arquivos e o registro da mídia.
    public function delete(Media $media): void
    {
        Storage::disk($media->disk)->delete(array_filter([
            $media->path,
            $media->thumbnail_path,
            $media->medium_path,
        ]));
        $this->history->recordDeleted($media);
        $media->delete();
    }

    // Monta os campos da mídia já com os arquivos gravados.
    private function attributes(UploadedFile $file, User $user, ?string $alt): array
    {
        $extension = $this->extension($file);
        $folder = 'media/'.now()->format('Y/m');
        $name = (string) Str::uuid();
        $path = $file->storeAs($folder, $name.'.'.$extension, 'public');
        [$width, $height] = getimagesize($file->getRealPath()) ?: [null, null];

        return [
            'user_id' => $user->id,
            'disk' => 'public',
            'path' => $path,
            'thumbnail_path' => $this->derivative($path, 400, $name, $folder, $extension),
            'medium_path' => $this->derivative($path, 1200, $name, $folder, $extension),
            'filename' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType() ?? 'image/jpeg',
            'size' => $file->getSize(),
            'width' => $width,
            'height' => $height,
            'alt' => $alt,
        ];
    }

    // Gera uma cópia redimensionada e devolve o caminho.
    private function derivative(string $source, int $width, string $name, string $folder, string $extension): string
    {
        $scaled = $this->scale($this->read($source), $width);
        $path = $folder.'/'.$name.'-'.$width.'.'.$extension;
        $this->write($scaled, $path, $extension);

        return $path;
    }

    // Lê o arquivo armazenado como imagem GD.
    private function read(string $storagePath): GdImage
    {
        $contents = Storage::disk('public')->get($storagePath);
        $image = imagecreatefromstring($contents ?: '');

        if (! $image instanceof GdImage) {
            throw ValidationException::withMessages([
                'file' => 'Não foi possível ler a imagem enviada.',
            ]);
        }

        return $image;
    }

    // Reduz a imagem até a largura máxima, mantendo a proporção.
    private function scale(GdImage $image, int $maxWidth): GdImage
    {
        $width = imagesx($image);

        if ($width <= $maxWidth) {
            return $image;
        }

        $height = (int) round(imagesy($image) * ($maxWidth / $width));
        $scaled = imagescale($image, $maxWidth, $height);

        return $scaled instanceof GdImage ? $scaled : $image;
    }

    // Grava a imagem redimensionada no disco público.
    private function write(GdImage $image, string $path, string $extension): void
    {
        ob_start();
        $this->render($image, $extension);
        Storage::disk('public')->put($path, (string) ob_get_clean());
    }

    // Codifica a imagem no formato de origem.
    private function render(GdImage $image, string $extension): void
    {
        match ($extension) {
            'png' => imagepng($image),
            'gif' => imagegif($image),
            'webp' => function_exists('imagewebp') ? imagewebp($image, null, 85) : imagejpeg($image, null, 85),
            default => imagejpeg($image, null, 85),
        };
    }

    // Normaliza a extensão aceita para o arquivo salvo.
    private function extension(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');

        return $extension === 'jpeg' ? 'jpg' : $extension;
    }
}
