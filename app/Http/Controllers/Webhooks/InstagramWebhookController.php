<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\HandleInstagramWebhook;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class InstagramWebhookController extends Controller
{
    /**
     * Meta's subscription check. PHP turns the `hub.*` query keys into `hub_*`.
     */
    public function verify(Request $request): Response
    {
        $token = (string) config('services.instagram.webhook_verify_token');

        abort_if(
            $token === ''
                || $request->query('hub_mode') !== 'subscribe'
                || ! hash_equals($token, (string) $request->query('hub_verify_token')),
            SymfonyResponse::HTTP_FORBIDDEN,
        );

        return response((string) $request->query('hub_challenge'));
    }

    /**
     * Event notifications, signed with the app secret over the raw body.
     * Acknowledged at once; the work runs on the queue.
     */
    public function handle(Request $request): Response
    {
        $secret = (string) config('services.instagram.client_secret');
        $expected = 'sha256='.hash_hmac('sha256', $request->getContent(), $secret);

        abort_if(
            $secret === '' || ! hash_equals($expected, (string) $request->header('X-Hub-Signature-256')),
            SymfonyResponse::HTTP_FORBIDDEN,
        );

        HandleInstagramWebhook::dispatch($request->all());

        return response()->noContent(SymfonyResponse::HTTP_OK);
    }
}
