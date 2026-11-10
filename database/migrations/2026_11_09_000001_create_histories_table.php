<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('histories')) {
            Schema::create('histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->restrictOnDelete();
                $table->string('auditable_type');
                $table->unsignedBigInteger('auditable_id');
                $table->string('action');
                $table->timestamp('created_at')->useCurrent();

                $table->index(['auditable_type', 'auditable_id']);
            });

            return;
        }

        $hasIndex = collect(Schema::getIndexes('histories'))
            ->contains(fn (array $index) => $index['columns'] === ['auditable_type', 'auditable_id']);

        if (! $hasIndex) {
            Schema::table('histories', function (Blueprint $table) {
                $table->index(['auditable_type', 'auditable_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
