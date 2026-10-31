<?php

declare(strict_types=1);

namespace App\Actions\CommentAutomation;

use App\Models\CommentAutomation;

class DeleteCommentAutomation
{
    public static function execute(CommentAutomation $automation): void
    {
        $automation->delete();
    }
}
