<?php

declare(strict_types=1);

use App\Mcp\Servers\TryPostServer;
use App\Mcp\Tools\CommentAutomation\CreateCommentAutomationTool;
use App\Mcp\Tools\CommentAutomation\DeleteCommentAutomationTool;
use App\Mcp\Tools\CommentAutomation\GetCommentAutomationTool;
use App\Mcp\Tools\CommentAutomation\ListCommentAutomationsTool;
use App\Mcp\Tools\CommentAutomation\UpdateCommentAutomationTool;
use App\Models\CommentAutomation;
use App\Models\CommentAutomationRun;
use App\Models\SocialAccount;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->workspace = Workspace::factory()->create(['user_id' => $this->user->id]);
    $this->workspace->members()->attach($this->user->id, membershipPivot('admin'));
    $this->user->update(['current_workspace_id' => $this->workspace->id]);
    $this->instagram = SocialAccount::factory()->instagram()->create(['workspace_id' => $this->workspace->id]);
    $this->automation = CommentAutomation::factory()->create(['social_account_id' => $this->instagram->id]);
    $this->data = [
        'social_account_id' => $this->instagram->id,
        'keyword' => ' GUIDE ',
        'reply_message' => 'Here is your guide.',
        'public_reply' => 'Check your inbox.',
        'is_active' => true,
    ];
});

test('list comment automations paginates newest first within the current workspace', function () {
    config(['app.pagination.default' => 1]);
    $newest = CommentAutomation::factory()->create([
        'social_account_id' => $this->instagram->id,
        'created_at' => now()->addMinute(),
    ]);
    CommentAutomation::factory()->create();

    TryPostServer::actingAs($this->user)->tool(ListCommentAutomationsTool::class, [])
        ->assertOk()
        ->assertStructuredContent(fn (AssertableJson $json) => $json
            ->where('total', 2)->where('per_page', 1)->where('current_page', 1)->where('last_page', 2)
            ->has('automations', 1)->where('automations.0.id', $newest->id)->etc());

    TryPostServer::actingAs($this->user)->tool(ListCommentAutomationsTool::class, ['page' => 2])
        ->assertOk()
        ->assertStructuredContent(fn (AssertableJson $json) => $json
            ->where('current_page', 2)->where('automations.0.id', $this->automation->id)->etc());
});

test('list comment automations returns an empty page', function () {
    $this->automation->delete();

    TryPostServer::actingAs($this->user)->tool(ListCommentAutomationsTool::class, [])
        ->assertOk()->assertStructuredContent(fn (AssertableJson $json) => $json
        ->has('automations', 0)->where('total', 0)->etc());
});

test('get comment automation returns its configuration without account credentials', function () {
    TryPostServer::actingAs($this->user)->tool(GetCommentAutomationTool::class, ['comment_automation_id' => $this->automation->id])
        ->assertOk()->assertStructuredContent(fn (AssertableJson $json) => $json
        ->where('id', $this->automation->id)
        ->where('social_account_id', $this->instagram->id)
        ->where('keyword', $this->automation->keyword)
        ->where('reply_message', $this->automation->reply_message)
        ->where('public_reply', null)->where('is_active', true)
        ->missing('social_account')->missing('access_token')->etc());
});

test('create comment automation uses the shared action and current workspace', function () {
    TryPostServer::actingAs($this->user)->tool(CreateCommentAutomationTool::class, [
        ...$this->data,
        'workspace_id' => Workspace::factory()->create()->id,
    ])->assertOk()->assertStructuredContent(fn (AssertableJson $json) => $json
        ->where('keyword', 'GUIDE')->where('public_reply', 'Check your inbox.')
        ->where('workspace_id', $this->workspace->id)->etc());

    expect(CommentAutomation::where('workspace_id', $this->workspace->id)->count())->toBe(2);
});

test('update comment automation replaces configuration and can disable it', function () {
    TryPostServer::actingAs($this->user)->tool(UpdateCommentAutomationTool::class, [
        ...$this->data,
        'comment_automation_id' => $this->automation->id,
        'public_reply' => null,
        'is_active' => false,
    ])->assertOk()->assertStructuredContent(fn (AssertableJson $json) => $json
        ->where('id', $this->automation->id)->where('keyword', 'GUIDE')
        ->where('public_reply', null)->where('is_active', false)->etc());

    expect($this->automation->fresh()->is_active)->toBeFalse();
});

test('delete comment automation removes its runs through the shared action', function () {
    $run = CommentAutomationRun::factory()->create(['comment_automation_id' => $this->automation->id]);

    TryPostServer::actingAs($this->user)->tool(DeleteCommentAutomationTool::class, ['comment_automation_id' => $this->automation->id])
        ->assertOk()->assertStructuredContent(['deleted' => true]);

    $this->assertModelMissing($this->automation);
    $this->assertModelMissing($run);
});

test('comment automation tools enforce admin policy for every operation', function (string $role) {
    $member = $role === 'outsider' ? workspaceOutsider($this->workspace) : User::factory()->create(['account_id' => $this->workspace->account_id]);

    if ($role !== 'outsider') {
        $this->workspace->members()->attach($member->id, membershipPivot($role));
        $member->update(['current_workspace_id' => $this->workspace->id]);
    }

    foreach ([ListCommentAutomationsTool::class, GetCommentAutomationTool::class, CreateCommentAutomationTool::class, UpdateCommentAutomationTool::class, DeleteCommentAutomationTool::class] as $tool) {
        TryPostServer::actingAs($member)->tool($tool, [
            ...$this->data,
            'comment_automation_id' => $this->automation->id,
        ])->assertHasErrors(['This action is unauthorized.']);
    }

    expect($this->automation->fresh()->keyword)->toBe($this->automation->keyword)
        ->and(CommentAutomation::count())->toBe(1);
})->with(['member', 'approval', 'outsider']);

test('workspace admins can manage comment automations', function () {
    $admin = User::factory()->create(['account_id' => $this->workspace->account_id]);
    $this->workspace->members()->attach($admin->id, membershipPivot('admin'));
    $admin->update(['current_workspace_id' => $this->workspace->id]);

    TryPostServer::actingAs($admin)->tool(CreateCommentAutomationTool::class, $this->data)->assertOk();
    TryPostServer::actingAs($admin)->tool(UpdateCommentAutomationTool::class, [
        ...$this->data, 'comment_automation_id' => $this->automation->id,
    ])->assertOk();
    TryPostServer::actingAs($admin)->tool(DeleteCommentAutomationTool::class, ['comment_automation_id' => $this->automation->id])->assertOk();
});

test('comment automation tools conceal records outside the current workspace', function (string $tool) {
    $other = CommentAutomation::factory()->create();

    TryPostServer::actingAs($this->user)->tool($tool, [
        ...$this->data, 'comment_automation_id' => $other->id,
    ])->assertHasErrors(['Comment automation not found.']);

    expect($other->fresh()->keyword)->toBe($other->keyword);
})->with([GetCommentAutomationTool::class, UpdateCommentAutomationTool::class, DeleteCommentAutomationTool::class]);

test('comment automation tools reject missing records', function (string $tool) {
    TryPostServer::actingAs($this->user)->tool($tool, [
        ...$this->data, 'comment_automation_id' => (string) Str::uuid(),
    ])->assertHasErrors(['Comment automation not found.']);
})->with([GetCommentAutomationTool::class, UpdateCommentAutomationTool::class, DeleteCommentAutomationTool::class]);

test('comment automation writes reject foreign and unsupported social accounts', function (string $tool, string $kind) {
    $account = $kind === 'foreign'
        ? SocialAccount::factory()->instagram()->create()
        : SocialAccount::factory()->linkedin()->create(['workspace_id' => $this->workspace->id]);

    TryPostServer::actingAs($this->user)->tool($tool, [
        ...$this->data, 'social_account_id' => $account->id, 'comment_automation_id' => $this->automation->id,
    ])->assertHasErrors();

    expect(CommentAutomation::count())->toBe(1)
        ->and($this->automation->fresh()->social_account_id)->toBe($this->instagram->id);
})->with([CreateCommentAutomationTool::class, UpdateCommentAutomationTool::class])->with(['foreign', 'unsupported']);

test('comment automation writes mirror required fields and length validation', function (string $tool, string $field, mixed $value) {
    $data = [...$this->data, 'comment_automation_id' => $this->automation->id, $field => $value];

    TryPostServer::actingAs($this->user)->tool($tool, $data)->assertHasErrors();

    expect(CommentAutomation::count())->toBe(1);
})->with([CreateCommentAutomationTool::class, UpdateCommentAutomationTool::class])->with([
    'missing account' => ['social_account_id', null],
    'invalid account' => ['social_account_id', 'invalid'],
    'empty keyword' => ['keyword', ''],
    'long keyword' => ['keyword', str_repeat('a', 101)],
    'empty reply' => ['reply_message', ''],
    'long reply' => ['reply_message', str_repeat('a', 1001)],
    'long public reply' => ['public_reply', str_repeat('a', 1001)],
    'invalid active' => ['is_active', 'yes'],
    'missing active' => ['is_active', null],
]);

test('comment automation tools reject invalid identifiers', function (string $tool) {
    TryPostServer::actingAs($this->user)->tool($tool, ['comment_automation_id' => 'invalid'])->assertHasErrors();
})->with([GetCommentAutomationTool::class, UpdateCommentAutomationTool::class, DeleteCommentAutomationTool::class]);

test('list comment automations validates page', function () {
    TryPostServer::actingAs($this->user)->tool(ListCommentAutomationsTool::class, ['page' => 0])->assertHasErrors();
});

test('comment automation writes accept maximum lengths and omitted public reply', function (string $tool) {
    $data = [
        ...$this->data,
        'comment_automation_id' => $this->automation->id,
        'keyword' => str_repeat('k', 100),
        'reply_message' => str_repeat('r', 1000),
    ];
    unset($data['public_reply']);

    TryPostServer::actingAs($this->user)->tool($tool, $data)
        ->assertOk()->assertStructuredContent(fn (AssertableJson $json) => $json
        ->where('keyword', str_repeat('k', 100))
        ->where('reply_message', str_repeat('r', 1000))
        ->where('public_reply', null)->etc());
})->with([CreateCommentAutomationTool::class, UpdateCommentAutomationTool::class]);

test('comment automation tools refuse users without a current workspace', function (string $tool) {
    $this->user->update(['current_workspace_id' => null]);

    TryPostServer::actingAs($this->user)->tool($tool, [
        ...$this->data, 'comment_automation_id' => $this->automation->id,
    ])->assertHasErrors(['This action is unauthorized.']);
})->with([ListCommentAutomationsTool::class, GetCommentAutomationTool::class, CreateCommentAutomationTool::class, UpdateCommentAutomationTool::class, DeleteCommentAutomationTool::class]);

test('comment automation tools are discoverable with titles and OAuth security', function () {
    subscribeAccount($this->user->account);
    $token = mcpBearerToken($this->user, $this->workspace)['plain_token'];

    $response = $this->withToken($token)
        ->withHeaders(['Accept' => 'application/json, text/event-stream'])
        ->postJson(route('mcp.trypost'), [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/list',
            'params' => (object) [],
        ])->assertOk();

    $tools = collect($response->json('result.tools'))->keyBy('name');

    foreach (['list-comment-automations-tool', 'get-comment-automation-tool', 'create-comment-automation-tool', 'update-comment-automation-tool', 'delete-comment-automation-tool'] as $name) {
        expect($tools->has($name))->toBeTrue()
            ->and(data_get($tools->get($name), 'title'))->toBeString()->not->toBeEmpty()
            ->and(data_get($tools->get($name), 'securitySchemes'))->toBe([['type' => 'oauth2', 'scopes' => ['mcp:use']]]);
    }
});
