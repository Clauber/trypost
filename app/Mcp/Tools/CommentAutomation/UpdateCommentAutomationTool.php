<?php

declare(strict_types=1);

namespace App\Mcp\Tools\CommentAutomation;

use App\Actions\CommentAutomation\UpdateCommentAutomation;
use App\Enums\SocialAccount\Platform;
use App\Mcp\Concerns\AuthorizesMcpTool;
use App\Models\CommentAutomation;
use App\Models\Workspace;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use Laravel\Mcp\Server\Tools\Annotations\IsOpenWorld;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly(false)]
#[IsDestructive(true)]
#[IsOpenWorld(true)]
#[Description('Replace the configuration of an Instagram comment automation in the current workspace. Send all required fields; set is_active to false to stop future replies. Requires permission to manage channels.')]
class UpdateCommentAutomationTool extends Tool
{
    use AuthorizesMcpTool;

    public function handle(Request $request): Response|ResponseFactory
    {
        $workspace = $this->currentWorkspace($request);

        if (! $workspace instanceof Workspace) {
            return $workspace;
        }

        $validated = $request->validate(['comment_automation_id' => ['required', 'uuid']]);

        $automation = CommentAutomation::query()
            ->where('workspace_id', $workspace->id)
            ->find(data_get($validated, 'comment_automation_id'));

        if (! $automation) {
            return Response::error('Comment automation not found.');
        }

        if ($denied = $this->denyUnlessCan($request, 'update', $automation)) {
            return $denied;
        }

        $validated = $request->validate([
            'social_account_id' => [
                'required',
                'uuid',
                Rule::exists('social_accounts', 'id')
                    ->where('workspace_id', $workspace->id)
                    ->where('platform', Platform::Instagram->value),
            ],
            'keyword' => ['required', 'string', 'max:100'],
            'reply_message' => ['required', 'string', 'max:1000'],
            'public_reply' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['required', 'boolean'],
        ]);

        UpdateCommentAutomation::execute($automation, $validated);

        return Response::structured($automation->toArray());
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'comment_automation_id' => $schema->string()->required()->description('The comment automation ID to update.'),
            'social_account_id' => $schema->string()->required()->description('The Instagram channel ID in the current workspace. Only the direct Instagram connection is supported.'),
            'keyword' => $schema->string()->required()->description('Comment keyword to match, ignoring case. Maximum 100 characters.'),
            'reply_message' => $schema->string()->required()->description('Private reply sent when a comment matches. Maximum 1000 characters.'),
            'public_reply' => $schema->string()->nullable()->description('Optional public reply to the matching comment. Maximum 1000 characters.'),
            'is_active' => $schema->boolean()->required()->description('Whether the automation responds to new matching comments.'),
        ];
    }
}
