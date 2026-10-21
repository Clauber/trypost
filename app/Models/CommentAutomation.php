<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CommentAutomationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CommentAutomation extends Model
{
    /** @use HasFactory<CommentAutomationFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'workspace_id',
        'social_account_id',
        'keyword',
        'reply_message',
        'public_reply',
        'is_active',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function socialAccount(): BelongsTo
    {
        return $this->belongsTo(SocialAccount::class);
    }

    public function runs(): HasMany
    {
        return $this->hasMany(CommentAutomationRun::class);
    }

    /** Whether the comment contains the keyword, ignoring case and surrounding spaces. */
    public function matches(string $commentText): bool
    {
        $keyword = trim($this->keyword);

        return $keyword !== '' && Str::contains($commentText, $keyword, ignoreCase: true);
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
