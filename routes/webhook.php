<?php

declare(strict_types=1);

use App\Http\Controllers\Webhooks\InstagramWebhookController;
use App\Http\Controllers\Webhooks\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

Route::group([
    'domain' => parse_url(config('app.webhook_url'), PHP_URL_HOST) ?: config('app.webhook_url'),
], function () {
    Route::post('telegram/webhook', [TelegramWebhookController::class, 'handle'])->name('telegram.webhook');
    Route::get('webhooks/instagram', [InstagramWebhookController::class, 'verify'])->name('instagram.webhook.verify');
    Route::post('webhooks/instagram', [InstagramWebhookController::class, 'handle'])->name('instagram.webhook');
});
