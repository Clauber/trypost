<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CommentAutomation;
use App\Models\User;

class CommentAutomationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->currentWorkspace !== null
            && $user->can('manageAccounts', $user->currentWorkspace);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, CommentAutomation $commentAutomation): bool
    {
        return $commentAutomation->workspace_id === $user->current_workspace_id
            && $this->viewAny($user);
    }

    public function delete(User $user, CommentAutomation $commentAutomation): bool
    {
        return $this->update($user, $commentAutomation);
    }
}
