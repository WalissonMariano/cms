<?php

namespace Tests\Feature\Api;

use App\Models\Midia\Media;
use App\Models\Posts\Category;
use App\Models\Posts\Post;
use App\Models\Posts\Tag;
use App\Models\Register\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublishedPostTest extends TestCase
{
    use RefreshDatabase;

    // Confirma a ordem decrescente, a página de dez e a ausência de rascunho.
    public function test_lists_published_posts_ten_per_page(): void
    {
        $author = User::factory()->create();
        $this->publishMany($author, 11);
        $this->draft($author);
        $this->scheduled($author);

        $response = $this->getJson('/api/posts');

        $response->assertOk();
        $response->assertJsonCount(10, 'data');
        $response->assertJsonPath('meta.per_page', 10);
        $response->assertJsonPath('meta.total', 11);
        $response->assertJsonPath('data.0.title', 'Post 11');
        $response->assertJsonPath('data.0.excerpt', 'Resumo do post');
        $response->assertJsonStructure($this->summaryStructure());
        $this->getJson('/api/posts?page=2')->assertJsonCount(1, 'data');
    }

    // Filtra a listagem pelo slug da categoria e da tag.
    public function test_filters_posts_by_category_and_tag(): void
    {
        $author = User::factory()->create();
        $category = $this->category('Laravel', 'laravel');
        $tag = $this->tag('PHP', 'php');
        $match = $this->makePost($author, ['title' => 'Match', 'slug' => 'match']);
        $match->categories()->attach($category);
        $match->tags()->attach($tag);
        $this->makePost($author, ['title' => 'Outro', 'slug' => 'outro']);

        $response = $this->getJson('/api/posts?category=laravel&tag=php');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.title', 'Match');
        $response->assertJsonPath('data.0.categories.0.slug', 'laravel');
        $response->assertJsonPath('data.0.tags.0.slug', 'php');
    }

    // Exibe capa, HTML com imagem absoluta, SEO e autor.
    public function test_shows_published_post_by_id_and_slug(): void
    {
        $author = User::factory()->create();
        $post = $this->detailedPost($author);

        $byId = $this->getJson('/api/posts/'.$post->id);
        $bySlug = $this->getJson('/api/posts/slug/'.$post->slug);

        $byId->assertOk();
        $bySlug->assertOk();
        $byId->assertJsonPath('data.cover.url', url('/storage/posts/capa.jpg'));
        $byId->assertJsonPath('data.cover.alt', 'Capa do post');
        $byId->assertJsonPath('data.author.name', $author->name);
        $byId->assertJsonPath('data.meta_title', 'Titulo SEO');
        $this->assertStringContainsString(
            url('/storage/posts/foto.jpg'),
            $byId->json('data.content'),
        );
        $bySlug->assertJsonPath('data.id', $post->id);
    }

    // Esconde rascunho e registro inexistente no detalhe.
    public function test_returns_not_found_for_unpublished_or_missing_post(): void
    {
        $draft = $this->draft(User::factory()->create());

        $this->getJson('/api/posts/'.$draft->id)
            ->assertNotFound()
            ->assertJsonPath('message', 'Post não encontrado.');
        $this->getJson('/api/posts/slug/'.$draft->slug)->assertNotFound();
        $this->get('/api/posts/999999')
            ->assertNotFound()
            ->assertJsonPath('message', 'Post não encontrado.');
        $this->getJson('/api/posts/slug/inexistente')->assertNotFound();
    }

    // Conta só posts publicados nas categorias e nas tags.
    public function test_lists_categories_and_tags_with_published_counts(): void
    {
        $author = User::factory()->create();
        $category = $this->category('Notícias', 'noticias');
        $tag = $this->tag('Laravel', 'laravel');
        $published = $this->makePost($author, ['slug' => 'publicado']);
        $published->categories()->attach($category);
        $published->tags()->attach($tag);
        $this->draft($author)->categories()->attach($category);

        $this->getJson('/api/categories')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'noticias')
            ->assertJsonPath('data.0.posts_count', 1);
        $this->getJson('/api/tags')
            ->assertOk()
            ->assertJsonPath('data.0.slug', 'laravel')
            ->assertJsonPath('data.0.posts_count', 1);
    }

    // Cria vários posts publicados em ordem crescente de data.
    private function publishMany(User $author, int $count): void
    {
        for ($number = 1; $number <= $count; $number++) {
            $this->makePost($author, [
                'title' => 'Post '.$number,
                'slug' => 'post-'.$number,
                'published_at' => now()->subMinutes($count - $number),
            ]);
        }
    }

    // Cria um post publicado com capa, categoria, tag e imagem no HTML.
    private function detailedPost(User $author): Post
    {
        $post = $this->makePost($author, [
            'slug' => 'post-completo',
            'content' => '<p><img src="/storage/posts/foto.jpg"></p>',
            'og_media_id' => $this->cover($author)->id,
            'meta_title' => 'Titulo SEO',
            'meta_description' => 'Descricao SEO',
        ]);
        $post->categories()->attach($this->category('PHP', 'php'));
        $post->tags()->attach($this->tag('API', 'api'));

        return $post;
    }

    // Cria a imagem de capa usada no detalhe.
    private function cover(User $author): Media
    {
        return Media::query()->create([
            'user_id' => $author->id,
            'path' => 'posts/capa.jpg',
            'thumbnail_path' => 'posts/capa-thumb.jpg',
            'medium_path' => 'posts/capa-medium.jpg',
            'filename' => 'capa.jpg',
            'mime' => 'image/jpeg',
            'size' => 1200,
            'alt' => 'Capa do post',
        ]);
    }

    // Cria um post publicado com os campos informados.
    private function makePost(User $author, array $overrides = []): Post
    {
        return Post::query()->create(array_merge([
            'user_id' => $author->id,
            'title' => 'Post publicado',
            'slug' => 'post-'.uniqid(),
            'excerpt' => 'Resumo do post',
            'content' => '<p>Conteúdo</p>',
            'status' => Post::STATUS_PUBLISHED,
            'published_at' => now()->subHour(),
        ], $overrides));
    }

    // Cria um rascunho que a API não pode exibir.
    private function draft(User $author): Post
    {
        return $this->makePost($author, [
            'title' => 'Rascunho',
            'slug' => 'rascunho-'.uniqid(),
            'status' => Post::STATUS_DRAFT,
            'published_at' => null,
        ]);
    }

    // Cria um post agendado para o futuro.
    private function scheduled(User $author): Post
    {
        return $this->makePost($author, [
            'title' => 'Agendado',
            'slug' => 'agendado-'.uniqid(),
            'status' => Post::STATUS_SCHEDULED,
            'published_at' => now()->addDay(),
        ]);
    }

    // Cria uma categoria.
    private function category(string $name, string $slug): Category
    {
        return Category::query()->create([
            'name' => $name,
            'slug' => $slug,
            'description' => 'Descricao',
        ]);
    }

    // Cria uma tag.
    private function tag(string $name, string $slug): Tag
    {
        return Tag::query()->create([
            'name' => $name,
            'slug' => $slug,
        ]);
    }

    // Campos esperados em cada item da listagem.
    private function summaryStructure(): array
    {
        return [
            'data' => [[
                'id', 'slug', 'title', 'excerpt', 'published_at', 'cover',
                'categories', 'tags',
            ]],
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ];
    }
}
