<?php

declare(strict_types=1);

use App\Models\CommentAutomation;
use App\Models\SocialAccount;
use App\Models\User;
use App\Models\Workspace;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->workspace = Workspace::factory()->create([
        'account_id' => $this->user->account_id,
        'user_id' => $this->user->id,
    ]);
    $this->workspace->members()->attach($this->user->id, membershipPivot('admin'));
    $this->user->update(['current_workspace_id' => $this->workspace->id]);

    $this->account = SocialAccount::factory()->instagram()->create(['workspace_id' => $this->workspace->id]);
});

function commentAutomationData(string $accountId, array $overrides = []): array
{
    return [
        'social_account_id' => $accountId,
        'keyword' => 'guide',
        'reply_message' => 'Here is your guide',
        'public_reply' => null,
        'is_active' => true,
        ...$overrides,
    ];
}

test('admins see the workspace automations and Instagram accounts', function () {
    CommentAutomation::factory()->for($this->account)->create(['workspace_id' => $this->workspace->id]);
    CommentAutomation::factory()->create();
    SocialAccount::factory()->x()->create(['workspace_id' => $this->workspace->id]);

    $this->actingAs($this->user)
        ->get(route('app.comment-automations.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('comment-automations/Index')
            ->has('automations', 1)
            ->has('accounts', 1)
            ->has('runs', 0));
});

test('admins create, toggle and delete an automation', function () {
    $this->actingAs($this->user)
        ->post(route('app.comment-automations.store'), commentAutomationData($this->account->id, ['keyword' => '  Guide ']))
        ->assertRedirect();

    $automation = CommentAutomation::query()->sole();
    expect($automation->keyword)->toBe('Guide')
        ->and($automation->workspace_id)->toBe($this->workspace->id);

    $this->actingAs($this->user)
        ->put(route('app.comment-automations.update', $automation), commentAutomationData($this->account->id, ['is_active' => false]))
        ->assertRedirect();

    expect($automation->fresh()->is_active)->toBeFalse();

    $this->actingAs($this->user)
        ->delete(route('app.comment-automations.destroy', $automation))
        ->assertRedirect();

    expect(CommentAutomation::query()->count())->toBe(0);
});

test('an account outside the workspace or not on Instagram is rejected', function (Closure $account) {
    $this->actingAs($this->user)
        ->post(route('app.comment-automations.store'), commentAutomationData($account()->id))
        ->assertSessionHasErrors('social_account_id');

    expect(CommentAutomation::query()->count())->toBe(0);
})->with([
    'other workspace' => [fn () => SocialAccount::factory()->instagram()->create()],
    'other network' => [fn () => SocialAccount::factory()->x()->create(['workspace_id' => test()->workspace->id])],
]);

test('another workspace\'s automation cannot be changed or deleted', function () {
    $foreign = CommentAutomation::factory()->create();

    $this->actingAs($this->user)
        ->put(route('app.comment-automations.update', $foreign), commentAutomationData($this->account->id))
        ->assertForbidden();

    $this->actingAs($this->user)
        ->delete(route('app.comment-automations.destroy', $foreign))
        ->assertForbidden();

    expect($foreign->fresh())->not->toBeNull();
});

test('members without channel rights cannot manage automations', function () {
    $member = User::factory()->create(['account_id' => $this->workspace->account_id, 'current_workspace_id' => $this->workspace->id]);
    $this->workspace->members()->attach($member->id, membershipPivot('member'));

    $this->actingAs($member)
        ->get(route('app.comment-automations.index'))
        ->assertForbidden();

    $this->actingAs($member)
        ->post(route('app.comment-automations.store'), commentAutomationData($this->account->id))
        ->assertForbidden();
});
