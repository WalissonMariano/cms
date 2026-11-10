# CMS

Painel administrativo de um blog, feito em Laravel. Cobre posts, categorias, tags, biblioteca de mídia, configurações do site e o histórico das ações. O site público ainda não faz parte desta entrega: a quantidade de posts por página e os códigos de rastreamento já ficam gravados para essa etapa.

## Funcionalidades

### Acesso

- Login de usuário ativo e logout
- Menu lateral com o conteúdo aberto em iframe
- Página da conta do usuário logado
- Home e dashboard com a contagem de usuários, grupos e histórico

O grupo **Blog** e o grupo **Cadastros** aparecem só para quem pertence ao grupo `ADMIN`.

### Blog

- Posts com título, slug, resumo, status, data de publicação e campos de SEO
- Editor TipTap (negrito, itálico, título, listas, link e imagem)
- Status `draft`, `published`, `scheduled` e `trashed`
- Publicado sem data usa o momento atual; agendado exige data futura
- A lixeira é um status. A exclusão definitiva só aparece quando o post já está nela
- Categorias com pai opcional e tags. Tags novas podem ser criadas no formulário do post
- O slug nasce do título e ganha um sufixo numérico se já existir

### Mídia

- Upload de imagem no disco `public`
- Miniatura e tamanho médio gerados com GD
- A imagem do conteúdo e a imagem de compartilhamento saem dessa biblioteca

### Configurações

Uma tela guarda título, slogan, logotipo, posts por página, links de Instagram, Facebook, X e YouTube, e o código de rastreamento.

### Histórico

Criar, atualizar e excluir post, categoria, tag, mídia, configuração e usuário gera um registro. A listagem geral fica em **Cadastros → Histórico**, com filtro por tipo e ação.

## Tecnologias

- PHP 8.1+ com extensão GD
- Laravel 10
- PostgreSQL
- Vite
- TipTap 2

A cor de destaque da interface é o azul `#2563EB`.

## Estrutura

Cada módulo novo tem pasta própria em model, controller, service, view e rota.

```
app/
├── Http/Controllers/
│   ├── Posts/                  # Posts, categorias e tags
│   ├── Midia/
│   └── Configuracoes/
├── Models/
│   ├── Posts/
│   ├── Midia/
│   └── Configuracoes/
└── Services/
    ├── HistoryService.php
    ├── Posts/
    ├── Midia/
    └── Configuracoes/

resources/views/
├── auth/                       # Login
├── layouts/                    # Menu
├── home/
├── dashboard/
├── account/
├── posts/                      # Posts, categorias e tags
├── midia/
├── configuracoes/
├── register/                   # Grupos e usuários
└── histories/

routes/
├── web.php                     # Login, menu, home e dashboard
├── posts/
├── midia/
└── configuracoes/
```

## Banco

| Tabela | Conteúdo |
|--------|----------|
| `groups` | Grupos de acesso |
| `users` | Usuários do painel |
| `posts` | Conteúdo, status, SEO e imagem de compartilhamento |
| `categories` | Categorias, com `parent_id` opcional |
| `tags` | Tags |
| `category_post` | Posts e categorias |
| `post_tag` | Posts e tags |
| `medias` | Arquivo original, miniatura, médio e texto alternativo |
| `settings` | Uma linha por chave de configuração |
| `histories` | Auditoria das ações |

## Instalação

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate
```

Ajuste `APP_NAME`, `APP_URL` e a conexão PostgreSQL no `.env`. As sessões usam o driver `database`.

```bash
php artisan migrate
php artisan storage:link
npm run dev
```

Com o [Laravel Herd](https://herd.laravel.com), a pasta `cms` responde em `http://cms.test`. Para os assets de produção, use `npm run build`.

### Acesso inicial

A migration cria o grupo `ADMIN` e um administrador.

| Perfil | E-mail | Senha |
|--------|--------|-------|
| Administrador | `admin@admin.com` | `admin` |

## Rotas

| Rota | Descrição |
|------|-----------|
| `/login` | Autenticação |
| `/menu` | Menu e iframe |
| `/home` | Página inicial |
| `/dashboard` | Resumo |
| `/account` | Conta do usuário logado |
| `/posts` | Posts |
| `/posts/categories` | Categorias |
| `/posts/tags` | Tags |
| `/midia` | Biblioteca de imagens |
| `/configuracoes` | Configurações do site |
| `/register/groups` | Grupos |
| `/register/users` | Usuários |
| `/register/histories` | Histórico |

As rotas do blog, da mídia, das configurações e dos cadastros exigem o grupo `ADMIN`.

## Agendamento

O comando `posts:publish-scheduled` passa os posts de `scheduled` para `published` quando `published_at` chega. Ele está agendado para rodar a cada minuto.

```bash
php artisan posts:publish-scheduled
php artisan schedule:work
```

O agendador também executa `migrate:fresh --seed --force` todo dia às 00:01 e às 12:01. Esse comando apaga o banco. Remova essas duas linhas de `app/Console/Kernel.php` antes de usar o painel com dados reais.

## Licença

Este projeto é open-source sob a licença [MIT](https://opensource.org/licenses/MIT).
