<?php

declare(strict_types=1);

namespace App\Mcp\Tools\CommentAutomation;

use App\Actions\CommentAutomation\CreateCommentAutomation;
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
#[IsDestructive(false)]
#[IsOpenWorld(true)]
#[Description('Create an Instagram comment automation for the current workspace. Matching comments receive the configured private reply and optional public reply while active. Requires permission to manage channels.')]
class CreateCommentAutomationTool extends Tool
{
    use AuthorizesMcpTool;

    public function handle(Request $request): Response|ResponseFactory
    {
        $workspace = $this->authorizeCurrentWorkspace($request, 'create', CommentAutomation::class);

        if (! $workspace instanceof Workspace) {
            return $workspace;
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

        $automation = CreateCommentAutomation::execute($workspace, $validated);

        return Response::structured($automation->toArray());
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'social_account_id' => $schema->string()->required()->description('The Instagram channel ID in the current workspace. Only the direct Instagram connection is supported.'),
            'keyword' => $schema->string()->required()->description('Comment keyword to match, ignoring case. Maximum 100 characters.'),
            'reply_message' => $schema->string()->required()->description('Private reply sent when a comment matches. Maximum 1000 characters.'),
            'public_reply' => $schema->string()->nullable()->description('Optional public reply to the matching comment. Maximum 1000 characters.'),
            'is_active' => $schema->boolean()->required()->description('Whether the automation responds to new matching comments.'),
        ];
    }
}
