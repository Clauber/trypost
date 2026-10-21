<?php

declare(strict_types=1);

use App\Enums\CommentAutomation\RunStatus;
use App\Jobs\HandleInstagramWebhook;
use App\Models\CommentAutomation;
use App\Models\CommentAutomationRun;
use App\Models\SocialAccount;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    config([
        'services.instagram.client_secret' => 'app-secret',
        'services.instagram.webhook_verify_token' => 'verify-me',
    ]);
});

function instagramCommentPayload(string $accountId, string $commentId = '17890', string $text = 'I want the GUIDE please', string $fromId = '555'): array
{
    return [
        'object' => 'instagram',
        'entry' => [[
            'id' => $accountId,
            'time' => 1760000000,
            'changes' => [[
                'field' => 'comments',
                'value' => [
                    'id' => $commentId,
                    'from' => ['id' => $fromId, 'username' => 'fan'],
                    'text' => $text,
                    'media' => ['id' => '999', 'media_product_type' => 'FEED'],
                ],
            ]],
        ]],
    ];
}

/** Graph API writes only: creating an account also reads its follower count. */
function instagramPostsSent(): int
{
    return collect(Http::recorded())->filter(fn (array $pair): bool => $pair[0]->method() === 'POST')->count();
}

test('the subscription check echoes the challenge for the right token', function () {
    $this->get(route('instagram.webhook.verify', ['hub.mode' => 'subscribe', 'hub.verify_token' => 'verify-me', 'hub.challenge' => '1158201444']))
        ->assertOk()
        ->assertSeeText('1158201444');
});

test('the subscription check rejects a wrong token', function () {
    $this->get(route('instagram.webhook.verify', ['hub.mode' => 'subscribe', 'hub.verify_token' => 'nope', 'hub.challenge' => '1158201444']))
        ->assertForbidden();
});

test('a notification with a bad signature is rejected', function () {
    Queue::fake();

    $this->postJson(route('instagram.webhook'), instagramCommentPayload('1784'), ['X-Hub-Signature-256' => 'sha256=bad'])
        ->assertForbidden();

    Queue::assertNothingPushed();
});

test('a signed notification is queued', function () {
    Queue::fake();
    $body = json_encode(instagramCommentPayload('1784'));

    $this->call('POST', route('instagram.webhook'), server: [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_X_HUB_SIGNATURE_256' => 'sha256='.hash_hmac('sha256', $body, 'app-secret'),
    ], content: $body)->assertOk();

    Queue::assertPushed(HandleInstagramWebhook::class, fn (HandleInstagramWebhook $job): bool => data_get($job->payload, 'entry.0.id') === '1784');
});

test('a matching comment gets one private reply and a public reply, even when redelivered', function () {
    Http::fake(['*' => Http::response(['recipient_id' => '555', 'message_id' => 'm1'])]);

    $account = SocialAccount::factory()->instagram()->create([
        'platform_user_id' => 'app-scoped-1',
        'meta' => ['ig_user_id' => '1784'],
        'access_token' => 'ig-token',
    ]);
    $automation = CommentAutomation::factory()->for($account)->create([
        'workspace_id' => $account->workspace_id,
        'keyword' => ' guide ',
        'reply_message' => 'Here is your guide',
        'public_reply' => 'Check your DMs!',
    ]);

    (new HandleInstagramWebhook(instagramCommentPayload('1784')))->handle();
    (new HandleInstagramWebhook(instagramCommentPayload('1784')))->handle();

    $base = config('trypost.platforms.instagram.graph_api');

    expect(instagramPostsSent())->toBe(2);
    Http::assertSent(fn (Request $request): bool => $request->url() === "{$base}/1784/messages"
        && $request->hasHeader('Authorization', 'Bearer ig-token')
        && $request->data() === ['recipient' => ['comment_id' => '17890'], 'message' => ['text' => 'Here is your guide']]);
    Http::assertSent(fn (Request $request): bool => $request->url() === "{$base}/17890/replies"
        && $request->data() === ['message' => 'Check your DMs!']);

    $run = CommentAutomationRun::query()->sole();
    expect($run->comment_automation_id)->toBe($automation->id)
        ->and($run->status)->toBe(RunStatus::Sent)
        ->and($run->commenter_username)->toBe('fan');
});

test('accounts without a stored professional id are found by their platform user id', function () {
    Http::fake(['*' => Http::response(['message_id' => 'm1'])]);

    $account = SocialAccount::factory()->instagram()->create(['platform_user_id' => '1784', 'meta' => []]);
    CommentAutomation::factory()->for($account)->create(['workspace_id' => $account->workspace_id, 'keyword' => 'guide']);

    (new HandleInstagramWebhook(instagramCommentPayload('1784')))->handle();

    Http::assertSent(fn (Request $request): bool => str_ends_with($request->url(), '/1784/messages'));
});

test('comments by the account itself, without the keyword, or for an inactive automation send nothing', function (array $payload, bool $active) {
    Http::fake();

    $account = SocialAccount::factory()->instagram()->create(['meta' => ['ig_user_id' => '1784']]);
    CommentAutomation::factory()->for($account)->create([
        'workspace_id' => $account->workspace_id,
        'keyword' => 'guide',
        'is_active' => $active,
    ]);

    (new HandleInstagramWebhook($payload))->handle();

    expect(instagramPostsSent())->toBe(0);
    expect(CommentAutomationRun::query()->count())->toBe(0);
})->with([
    'own comment' => [fn () => instagramCommentPayload('1784', fromId: '1784'), true],
    'no keyword' => [fn () => instagramCommentPayload('1784', text: 'nice post'), true],
    'inactive' => [fn () => instagramCommentPayload('1784'), false],
]);

test('a failed private reply is recorded and not retried', function () {
    Http::fake(['*' => Http::response(['error' => ['message' => 'nope']], 400)]);

    $account = SocialAccount::factory()->instagram()->create(['meta' => ['ig_user_id' => '1784']]);
    CommentAutomation::factory()->for($account)->create(['workspace_id' => $account->workspace_id, 'keyword' => 'guide']);

    (new HandleInstagramWebhook(instagramCommentPayload('1784')))->handle();
    (new HandleInstagramWebhook(instagramCommentPayload('1784')))->handle();

    expect(instagramPostsSent())->toBe(1);
    $run = CommentAutomationRun::query()->sole();
    expect($run->status)->toBe(RunStatus::Failed)
        ->and($run->error)->toContain('400');
});

test('the public reply is posted even when the private reply fails', function () {
    $base = config('trypost.platforms.instagram.graph_api');
    Http::fake([
        "{$base}/1784/messages" => Http::response(['error' => ['message' => 'An unknown error has occurred.']], 500),
        '*' => Http::response(['id' => 'r1']),
    ]);

    $account = SocialAccount::factory()->instagram()->create(['meta' => ['ig_user_id' => '1784']]);
    CommentAutomation::factory()->for($account)->create([
        'workspace_id' => $account->workspace_id,
        'keyword' => 'guide',
        'public_reply' => 'Check your DMs!',
    ]);

    (new HandleInstagramWebhook(instagramCommentPayload('1784')))->handle();

    Http::assertSent(fn (Request $request): bool => $request->url() === "{$base}/17890/replies");
    $run = CommentAutomationRun::query()->sole();
    expect($run->status)->toBe(RunStatus::Failed)
        ->and($run->error)->toContain('500')
        ->and($run->error)->not->toContain('Public reply failed');
});
