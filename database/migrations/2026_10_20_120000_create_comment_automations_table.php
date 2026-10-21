<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comment_automations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workspace_id')->constrained('workspaces')->cascadeOnDelete();
            $table->foreignUuid('social_account_id')->constrained('social_accounts')->cascadeOnDelete();
            $table->string('keyword');
            $table->text('reply_message');
            $table->text('public_reply')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['social_account_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_automations');
    }
};
