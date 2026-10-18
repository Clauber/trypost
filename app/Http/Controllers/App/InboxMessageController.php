<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\Inbox\BuildInboxPageProps;
use App\Dto\Inbox\InboxFilters;
use App\Enums\Inbox\InboxTab;
use App\Enums\SocialAccount\Platform;
use App\Http\Requests\App\Inbox\IndexInboxRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class InboxMessageController extends Controller
{
    public function index(IndexInboxRequest $request): Response|RedirectResponse
    {
        abort_unless(Platform::inboxModuleEnabled(), HttpResponse::HTTP_NOT_FOUND);

        if ($request->validated('tab') === InboxTab::Messages->value) {
            return redirect()->route('app.inbox.index', $request->query());
        }

        $workspace = $request->user()->currentWorkspace;
        $this->authorize('createPost', $workspace);

        return Inertia::render('inbox/Index', BuildInboxPageProps::execute(
            $request->user(),
            $workspace,
            InboxFilters::fromArray($request->validated()),
            'list',
            messagesAround: $request->validated('messages_around'),
        ));
    }
}
