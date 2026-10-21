<?php

declare(strict_types=1);

namespace App\Http\Requests\App\CommentAutomation;

use App\Enums\SocialAccount\Platform;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommentAutomationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'social_account_id' => [
                'required',
                'uuid',
                Rule::exists('social_accounts', 'id')
                    ->where('workspace_id', $this->user()->current_workspace_id)
                    ->where('platform', Platform::Instagram->value),
            ],
            'keyword' => ['required', 'string', 'max:100'],
            'reply_message' => ['required', 'string', 'max:1000'],
            'public_reply' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'social_account_id' => __('comment_automations.form.account'),
            'keyword' => __('comment_automations.form.keyword'),
            'reply_message' => __('comment_automations.form.reply_message'),
            'public_reply' => __('comment_automations.form.public_reply'),
        ];
    }
}
