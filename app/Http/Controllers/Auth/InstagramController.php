<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\SocialAccount\Platform as SocialPlatform;
use App\Models\SocialAccount;
use App\Support\Social\PendingConnection;
use Exception;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

class InstagramController extends SocialController
{
    protected string $driver = 'instagram';

    protected SocialPlatform $platform = SocialPlatform::Instagram;

    protected array $scopes = [
        'instagram_business_basic',
        'instagram_business_content_publish',
        'instagram_business_manage_insights',
        'instagram_business_manage_comments',
        'instagram_business_manage_messages',
    ];

    public function connect(Request $request): Response
    {
        $this->ensurePlatformEnabled();

        $workspace = $request->user()->currentWorkspace;

        $this->authorize('manageAccounts', $workspace);

        $this->rememberConnectSession($request, $workspace);

        $url = Socialite::driver($this->driver)
            ->scopes($this->scopes)
            ->redirect()
            ->getTargetUrl();

        return Inertia::location($url);
    }

    public function callback(Request $request): RedirectResponse
    {
        $workspace = $this->connectWorkspace($request);

        try {
            $socialUser = Socialite::driver($this->driver)->user();

            $scopes = $this->reportedScopes($socialUser->approvedScopes, $this->scopes);

            return $this->refusalForMissingPublishScopes($scopes) ?? $this->offerIdentities($workspace, [
                PendingConnection::identity(
                    $this->platform,
                    (string) $socialUser->getId(),
                    $socialUser->getName() ?? $socialUser->getNickname(),
                    $socialUser->getNickname(),
                    $socialUser->getAvatar(),
                    $this->platform->identityType()->value,
                    [
                        'username' => $socialUser->getNickname(),
                        'display_name' => $socialUser->getName() ?? $socialUser->getNickname(),
                        'access_token' => $socialUser->token,
                        'refresh_token' => $socialUser->refreshToken,
                        'token_expires_at' => now()->addSeconds($socialUser->expiresIn ?? $this->platform->defaultTokenTtlSeconds()),
                        'scopes' => $scopes,
                        'meta' => [
                            'account_type' => data_get($socialUser->user, 'account_type'),
                            'ig_user_id' => transform(data_get($socialUser->user, 'user_id'), fn (mixed $userId): string => (string) $userId),
                        ],
                    ],
                ),
            ], $this->reconnectAccount($workspace));
        } catch (Exception $e) {
            Log::error('Instagram OAuth Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->failConnection('error_connecting');
        }
    }

    /**
     * Subscribes the account to comment webhooks so comment automations can
     * reply. A failure only disables those automations, so it never stops the
     * connection.
     */
    protected function afterConnected(SocialAccount $account): void
    {
        $accountId = $account->instagramAccountId() ?? $account->platform_user_id;

        $response = rescue(
            fn (): HttpResponse => Http::withToken($account->access_token)
                ->post("{$this->graphApi()}/{$accountId}/subscribed_apps?subscribed_fields=comments"),
            report: false,
        );

        if (! $response?->successful()) {
            Log::warning('Instagram webhook subscription failed', [
                'social_account_id' => $account->id,
                'status' => $response?->status(),
                'body' => $response?->body(),
            ]);
        }
    }
}
