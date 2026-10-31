<?php

declare(strict_types=1);

namespace App\Mcp\Tools\CommentAutomation;

use App\Mcp\Concerns\AuthorizesMcpTool;
use App\Models\CommentAutomation;
use App\Models\Workspace;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsDestructive;
use Laravel\Mcp\Server\Tools\Annotations\IsOpenWorld;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly(true)]
#[IsDestructive(false)]
#[IsOpenWorld(false)]
#[Description('Get an Instagram comment automation by ID in the current workspace. Returns its channel ID, keyword, private reply, optional public reply and active state. Requires permission to manage channels.')]
class GetCommentAutomationTool extends Tool
{
    use AuthorizesMcpTool;

    public function handle(Request $request): Response|ResponseFactory
    {
        $workspace = $this->authorizeCurrentWorkspace($request, 'viewAny', CommentAutomation::class);

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

        return Response::structured($automation->toArray());
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'comment_automation_id' => $schema->string()->required()->description('The comment automation ID to retrieve.'),
        ];
    }
}
