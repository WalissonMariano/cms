# API pública do blog

Leitura para o site em Next.js. Não exige autenticação. A resposta traz só posts com status `published`, `published_at` preenchido e `published_at` menor ou igual ao momento da consulta. Rascunho, agendado e lixeira não aparecem. No detalhe, esses casos respondem 404.

A base é `/api`. O grupo de rotas limita cada IP a 60 requisições por minuto. O CORS já libera `api/*` para qualquer origem.

As URLs de imagem usam `APP_URL`. No exemplo abaixo, o CMS está em `https://cms.test`.

## Endpoints

| Método | Caminho | Uso |
| --- | --- | --- |
| GET | `/api/posts` | Listagem paginada |
| GET | `/api/posts/{id}` | Post completo pelo id |
| GET | `/api/posts/slug/{slug}` | Post completo pelo slug |
| GET | `/api/categories` | Categorias |
| GET | `/api/tags` | Tags |

## Listagem de posts

`GET /api/posts`

Ordenação: `published_at` decrescente. Em empate, o id maior vem primeiro. Cada página tem 10 posts.

Query opcional:

| Parâmetro | Efeito |
| --- | --- |
| `page` | Número da página. A primeira é `1`. |
| `category` | Slug da categoria. |
| `tag` | Slug da tag. |

`category` e `tag` juntos exigem as duas classificações no mesmo post. Slug inexistente devolve uma página vazia, não 404.

```http
GET /api/posts?page=1&category=laravel&tag=php
```

```json
{
  "data": [
    {
      "id": 12,
      "slug": "primeira-publicacao",
      "title": "Primeira publicação",
      "excerpt": "Resumo curto do post.",
      "published_at": "2026-11-10T20:00:00-03:00",
      "cover": {
        "url": "https://cms.test/storage/posts/capa.jpg",
        "alt": "Capa do post"
      },
      "categories": [
        {
          "id": 3,
          "name": "Laravel",
          "slug": "laravel",
          "description": "Posts sobre Laravel",
          "parent_id": null
        }
      ],
      "tags": [
        {
          "id": 5,
          "name": "PHP",
          "slug": "php"
        }
      ]
    }
  ],
  "links": {
    "first": "https://cms.test/api/posts?page=1",
    "last": "https://cms.test/api/posts?page=3",
    "prev": null,
    "next": "https://cms.test/api/posts?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 3,
    "path": "https://cms.test/api/posts",
    "per_page": 10,
    "to": 10,
    "total": 21
  }
}
```

`cover` é `null` quando o post não tem imagem de compartilhamento. `excerpt` pode ser `null`. Na listagem, categoria e tag não trazem `posts_count`. O HTML do conteúdo não vem neste endpoint.

`published_at` está em ISO 8601.

## Detalhe do post

`GET /api/posts/{id}`

`GET /api/posts/slug/{slug}`

Os dois caminhos devolvem o mesmo JSON. O id é numérico. O slug é o campo único do post e é o caminho adequado para a URL do Next.

O detalhe repete os campos da listagem e acrescenta o HTML, o SEO e o autor.

```json
{
  "data": {
    "id": 12,
    "slug": "primeira-publicacao",
    "title": "Primeira publicação",
    "excerpt": "Resumo curto do post.",
    "published_at": "2026-11-10T20:00:00-03:00",
    "cover": {
      "url": "https://cms.test/storage/posts/capa.jpg",
      "alt": "Capa do post"
    },
    "categories": [
      {
        "id": 3,
        "name": "Laravel",
        "slug": "laravel",
        "description": "Posts sobre Laravel",
        "parent_id": null
      }
    ],
    "tags": [
      {
        "id": 5,
        "name": "PHP",
        "slug": "php"
      }
    ],
    "content": "<p><img src=\"https://cms.test/storage/posts/foto.jpg\"></p>",
    "meta_title": "Primeira publicação",
    "meta_description": "Descrição para os buscadores.",
    "author": {
      "name": "Maria"
    }
  }
}
```

`content` é o HTML do editor. Imagens gravadas como `/storage/...` saem com a origem do CMS, para o Next em outro domínio conseguir carregá-las. `meta_title` e `meta_description` podem ser `null`. `author.name` é o nome de quem criou o post.

Id ou slug inexistente, e post que não está publicado, respondem 404 em JSON. O cabeçalho `Accept` não é necessário:

```json
{
  "message": "Post não encontrado."
}
```

Com `APP_DEBUG=true`, o Laravel acrescenta o rastreamento da exceção nessa resposta. Esse detalhe fica só no ambiente local.

## Categorias

`GET /api/categories`

Ordenação por nome. `posts_count` conta só posts publicados.

```json
{
  "data": [
    {
      "id": 3,
      "name": "Laravel",
      "slug": "laravel",
      "description": "Posts sobre Laravel",
      "parent_id": null,
      "posts_count": 4
    }
  ]
}
```

`parent_id` aponta para outra categoria quando houver hierarquia. `description` pode ser `null`.

Para listar os posts de uma categoria, use `GET /api/posts?category={slug}`.

## Tags

`GET /api/tags`

Ordenação por nome. `posts_count` conta só posts publicados.

```json
{
  "data": [
    {
      "id": 5,
      "name": "PHP",
      "slug": "php",
      "posts_count": 2
    }
  ]
}
```

Para listar os posts de uma tag, use `GET /api/posts?tag={slug}`.
