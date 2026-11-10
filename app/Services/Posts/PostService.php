<?php

namespace App\Services\Posts;

use App\Models\Posts\Post;
use App\Models\Register\User;
use App\Services\HistoryService;
use Illuminate\Validation\ValidationException;

class PostService
{
    public function __construct(
        private readonly SlugService $slugs,
        private readonly TagService $tags,
        private readonly HistoryService $history,
    ) {}

    // Cria o post, vincula a taxonomia e registra o histórico.
    public function create(array $data, User $author): Post
    {
        $this->assertSchedule($data);
        $post = Post::create($this->attributes($data, $author));
        $this->syncTaxonomy($post, $data);
        $this->history->recordCreated($post);

        return $post;
    }

    // Atualiza o post, a taxonomia e registra o histórico.
    public function update(Post $post, array $data): Post
    {
        $this->assertSchedule($data);
        $post->update($this->attributes($data, null, $post));
        $this->syncTaxonomy($post, $data);
        $this->history->recordUpdated($post);

        return $post;
    }

    // Envia o post para a lixeira ou exclui de vez.
    public function delete(Post $post): void
    {
        if ($post->status !== Post::STATUS_TRASHED) {
            $post->update(['status' => Post::STATUS_TRASHED]);
            $this->history->recordUpdated($post);

            return;
        }

        $this->history->recordDeleted($post);
        $post->delete();
    }

    // Publica os posts agendados cuja data já chegou.
    public function publishDue(): void
    {
        Post::query()
            ->where('status', Post::STATUS_SCHEDULED)
            ->where('published_at', '<=', now())
            ->orderBy('id')
            ->each(fn (Post $post) => $this->publish($post));
    }

    // Monta os campos persistidos do post.
    private function attributes(array $data, ?User $author, ?Post $post = null): array
    {
        return [
            'user_id' => $post?->user_id ?? $author->id,
            'title' => $data['title'],
            'slug' => $this->slugs->unique(($data['slug'] ?? null) ?: $data['title'], 'posts', $post?->id),
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'] ?? null,
            'status' => $data['status'],
            'published_at' => $this->publishedAt($data),
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'og_media_id' => ($data['og_media_id'] ?? null) ?: null,
        ];
    }

    // Define a data de publicação conforme o status.
    private function publishedAt(array $data): ?string
    {
        if ($data['status'] === Post::STATUS_PUBLISHED && blank($data['published_at'] ?? null)) {
            return now()->toDateTimeString();
        }

        return $data['published_at'] ?? null;
    }

    // Exige data futura quando o post está agendado.
    private function assertSchedule(array $data): void
    {
        if (($data['status'] ?? null) !== Post::STATUS_SCHEDULED) {
            return;
        }

        $date = $data['published_at'] ?? null;

        if (blank($date) || now()->parse($date)->lte(now())) {
            throw ValidationException::withMessages([
                'published_at' => 'Informe uma data futura para agendar o post.',
            ]);
        }
    }

    // Sincroniza categorias e tags, criando as tags novas.
    private function syncTaxonomy(Post $post, array $data): void
    {
        $post->categories()->sync($data['categories'] ?? []);
        $post->tags()->sync($this->tags->resolveIds($data['tags'] ?? [], $data['new_tags'] ?? null));
    }

    // Marca um post agendado como publicado e registra o histórico.
    private function publish(Post $post): void
    {
        $post->update(['status' => Post::STATUS_PUBLISHED]);
        $this->history->recordUpdated($post, $post->user);
    }
}
