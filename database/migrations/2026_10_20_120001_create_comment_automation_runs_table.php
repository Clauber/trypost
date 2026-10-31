<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comment_automation_runs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('comment_automation_id')->constrained('comment_automations')->cascadeOnDelete();
            $table->string('comment_id');
            $table->string('commenter_username')->nullable();
            $table->string('status');
            $table->text('error')->nullable();
            $table->timestamps();

            $table->unique(['comment_automation_id', 'comment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_automation_runs');
    }
};
