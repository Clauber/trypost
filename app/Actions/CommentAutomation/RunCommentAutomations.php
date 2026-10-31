<?php

declare(strict_types=1);

namespace App\Actions\CommentAutomation;

use App\Enums\CommentAutomation\RunStatus;
use App\Enums\SocialAccount\Platform;
use App\Models\CommentAutomation;
use App\Models\SocialAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class RunCommentAutomations
{
    /**
     * Replies to one Instagram comment from a `comments` webhook change: the
     * first active automation of each receiving account whose keyword the
     * comment contains sends its private reply (Instagram allows one per
     * comment) and, when set, its public reply.
     *
     * @param  array<string, mixed>  $comment  the change's `value`
     */
    public static function execute(string $instagramAccountId, array $comment): void
    {
        $commentId = (string) data_get($comment, 'id');

        // The account's own replies come back as comments too.
        if ($commentId === '' || (string) data_get($comment, 'from.id') === $instagramAccountId) {
            return;
        }

        $accounts = SocialAccount::query()
            ->connected()
            ->where('platform', Platform::Instagram)
            ->where(fn (Builder $query): Builder => $query
                ->where('meta->ig_user_id', $instagramAccountId)
                ->orWhere('platform_user_id', $instagramAccountId))
            ->get();

        foreach ($accounts as $account) {
            $automation = CommentAutomation::query()
                ->active()
                ->whereBelongsTo($account)
                ->oldest()
                ->get()
                ->first(fn (CommentAutomation $automation): bool => $automation->matches((string) data_get($comment, 'text')));

            if ($automation !== null) {
                self::reply($account, $instagramAccountId, $automation, $comment);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $comment
     */
    private static function reply(SocialAccount $account, string $instagramAccountId, CommentAutomation $automation, array $comment): void
    {
        $commentId = (string) data_get($comment, 'id');

        // The unique (automation, comment) row is the dedupe: Meta redelivers webhooks.
        $run = $automation->runs()->createOrFirst(
            ['comment_id' => $commentId],
            ['commenter_username' => data_get($comment, 'from.username'), 'status' => RunStatus::Pending],
        );

        if (! $run->wasRecentlyCreated) {
            return;
        }

        $baseUrl = $account->platform->instagramGraphBaseUrl();

        // Any failure must resolve the run: a row left pending is never retried, the dedupe skips it.
        $errors = [];
        $dmFailed = false;

        try {
            Http::withToken($account->access_token)
                ->post("{$baseUrl}/{$instagramAccountId}/messages", [
                    'recipient' => ['comment_id' => $commentId],
                    'message' => ['text' => $automation->reply_message],
                ])
                ->throw();
        } catch (Throwable $e) {
            $dmFailed = true;
            $errors[] = $e->getMessage();
        }

        // A failed DM must not block the public reply.
        if (filled($automation->public_reply)) {
            try {
                Http::withToken($account->access_token)
                    ->post("{$baseUrl}/{$commentId}/replies", ['message' => $automation->public_reply])
                    ->throw();
            } catch (Throwable $e) {
                $errors[] = 'Public reply failed: '.$e->getMessage();
            }
        }

        $run->update([
            'status' => $dmFailed ? RunStatus::Failed : RunStatus::Sent,
            'error' => $errors === [] ? null : Str::limit(implode("\n", $errors), 1000),
        ]);
    }
}
