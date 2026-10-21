<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CommentAutomation\RunStatus;
use Database\Factories\CommentAutomationRunFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentAutomationRun extends Model
{
    /** @use HasFactory<CommentAutomationRunFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'comment_automation_id',
        'comment_id',
        'commenter_username',
        'status',
        'error',
    ];

    protected function casts(): array
    {
        return [
            'status' => RunStatus::class,
        ];
    }

    public function commentAutomation(): BelongsTo
    {
        return $this->belongsTo(CommentAutomation::class);
    }
}
