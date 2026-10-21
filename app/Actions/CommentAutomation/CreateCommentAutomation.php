<?php

declare(strict_types=1);

namespace App\Actions\CommentAutomation;

use App\Models\CommentAutomation;
use App\Models\Workspace;

class CreateCommentAutomation
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function execute(Workspace $workspace, array $data): CommentAutomation
    {
        return CommentAutomation::query()->create([
            'workspace_id' => $workspace->id,
            'social_account_id' => data_get($data, 'social_account_id'),
            'keyword' => trim((string) data_get($data, 'keyword')),
            'reply_message' => data_get($data, 'reply_message'),
            'public_reply' => data_get($data, 'public_reply'),
            'is_active' => (bool) data_get($data, 'is_active'),
        ]);
    }
}
