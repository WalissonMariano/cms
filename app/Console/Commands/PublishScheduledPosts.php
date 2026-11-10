<?php

namespace App\Console\Commands;

use App\Services\Posts\PostService;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    protected $signature = 'posts:publish-scheduled';

    protected $description = 'Publica os posts agendados cuja data já chegou';

    // Publica os posts agendados e registra cada alteração.
    public function handle(PostService $posts): int
    {
        $posts->publishDue();

        return self::SUCCESS;
    }
}
