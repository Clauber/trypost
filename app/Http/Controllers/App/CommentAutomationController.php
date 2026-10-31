<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CommentAutomation\CreateCommentAutomation;
use App\Actions\CommentAutomation\DeleteCommentAutomation;
use App\Actions\CommentAutomation\UpdateCommentAutomation;
use App\Enums\SocialAccount\Platform;
use App\Http\Requests\App\CommentAutomation\StoreCommentAutomationRequest;
use App\Http\Requests\App\CommentAutomation\UpdateCommentAutomationRequest;
use App\Models\CommentAutomation;
use App\Models\CommentAutomationRun;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CommentAutomationController extends Controller
{
    private const int RECENT_RUNS = 20;

    public function index(Request $request): Response
    {
        $workspace = $request->user()->currentWorkspace;

        $this->authorize('viewAny', CommentAutomation::class);

        return Inertia::render('comment-automations/Index', [
            'automations' => CommentAutomation::query()
                ->where('workspace_id', $workspace->id)
                ->with('socialAccount')
                ->orderByDesc('created_at')
                ->get(),
            'accounts' => $workspace->socialAccounts()
                ->where('platform', Platform::Instagram)
                ->get(),
            'runs' => CommentAutomationRun::query()
                ->whereHas('commentAutomation', fn (Builder $query): Builder => $query->where('workspace_id', $workspace->id))
                ->with('commentAutomation:id,keyword')
                ->latest()
                ->limit(self::RECENT_RUNS)
                ->get(),
        ]);
    }

    public function store(StoreCommentAutomationRequest $request): RedirectResponse
    {
        $this->authorize('create', CommentAutomation::class);

        CreateCommentAutomation::execute($request->user()->currentWorkspace, $request->validated());

        return back();
    }

    public function update(UpdateCommentAutomationRequest $request, CommentAutomation $commentAutomation): RedirectResponse
    {
        $this->authorize('update', $commentAutomation);

        UpdateCommentAutomation::execute($commentAutomation, $request->validated());

        return back();
    }

    public function destroy(CommentAutomation $commentAutomation): RedirectResponse
    {
        $this->authorize('delete', $commentAutomation);

        DeleteCommentAutomation::execute($commentAutomation);

        return back();
    }
}
