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
#[Description('List Instagram comment automations for the current workspace, newest first. Requires permission to manage channels. Paginated with the app page size: pass page; the response carries total, per_page, current_page and last_page.')]
class ListCommentAutomationsTool extends Tool
{
    use AuthorizesMcpTool;

    public function handle(Request $request): Response|ResponseFactory
    {
        $workspace = $this->authorizeCurrentWorkspace($request, 'viewAny', CommentAutomation::class);

        if (! $workspace instanceof Workspace) {
            return $workspace;
        }

        $validated = $request->validate([
            'page' => ['sometimes', 'integer', 'min:1'],
        ]);

        $automations = CommentAutomation::query()
            ->where('workspace_id', $workspace->id)
            ->orderByDesc('created_at')
            ->paginate((int) config('app.pagination.default'), page: (int) data_get($validated, 'page', 1));

        return Response::structured([
            'automations' => $automations->items(),
            'total' => $automations->total(),
            'per_page' => $automations->perPage(),
            'current_page' => $automations->currentPage(),
            'last_page' => $automations->lastPage(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'page' => $schema->integer()->description('Page number.'),
        ];
    }
}
