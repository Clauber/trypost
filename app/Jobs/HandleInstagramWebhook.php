<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\CommentAutomation\RunCommentAutomations;
use App\Enums\SocialAccount\Platform;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class HandleInstagramWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(public array $payload)
    {
        $this->onQueue(Platform::Instagram->queue());
    }

    public function handle(): void
    {
        foreach ((array) data_get($this->payload, 'entry', []) as $entry) {
            foreach ((array) data_get($entry, 'changes', []) as $change) {
                if (data_get($change, 'field') === 'comments' && is_array(data_get($change, 'value'))) {
                    RunCommentAutomations::execute((string) data_get($entry, 'id'), data_get($change, 'value'));
                }
            }
        }
    }
}
