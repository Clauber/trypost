<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import {
    IconDotsVertical,
    IconMessageCircle,
    IconPencil,
    IconPlus,
    IconTrash,
} from '@tabler/icons-vue';
import { ref } from 'vue';

import ConfirmDeleteModal from '@/components/ConfirmDeleteModal.vue';
import EmptyState from '@/components/EmptyState.vue';
import InputError from '@/components/InputError.vue';
import SettingsListRow from '@/components/settings/SettingsListRow.vue';
import SettingsSection from '@/components/settings/SettingsSection.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    NativeSelect,
    NativeSelectOption,
} from '@/components/ui/native-select';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import date from '@/date';
import SettingsLayout from '@/layouts/SettingsLayout.vue';
import { destroy, store, update } from '@/routes/app/comment-automations';

interface InstagramAccount {
    id: string;
    username: string | null;
    display_name: string | null;
}

interface CommentAutomation {
    id: string;
    social_account_id: string;
    keyword: string;
    reply_message: string;
    public_reply: string | null;
    is_active: boolean;
    social_account: InstagramAccount | null;
}

interface CommentAutomationRun {
    id: string;
    commenter_username: string | null;
    status: 'pending' | 'sent' | 'failed';
    error: string | null;
    created_at: string;
    comment_automation: { id: string; keyword: string } | null;
}

const props = defineProps<{
    automations: CommentAutomation[];
    accounts: InstagramAccount[];
    runs: CommentAutomationRun[];
}>();

const dialogOpen = ref(false);
const editing = ref<CommentAutomation | null>(null);

const form = useForm({
    social_account_id: '',
    keyword: '',
    reply_message: '',
    public_reply: '',
    is_active: true,
});

const accountLabel = (account: InstagramAccount | null): string =>
    account?.username ? `@${account.username}` : (account?.display_name ?? '');

const openCreateDialog = (): void => {
    editing.value = null;
    form.defaults({
        social_account_id: props.accounts[0]?.id ?? '',
        keyword: '',
        reply_message: '',
        public_reply: '',
        is_active: true,
    });
    form.reset();
    form.clearErrors();
    dialogOpen.value = true;
};

const openEditDialog = (automation: CommentAutomation): void => {
    editing.value = automation;
    form.defaults({
        social_account_id: automation.social_account_id,
        keyword: automation.keyword,
        reply_message: automation.reply_message,
        public_reply: automation.public_reply ?? '',
        is_active: automation.is_active,
    });
    form.reset();
    form.clearErrors();
    dialogOpen.value = true;
};

const closeDialog = (): void => {
    dialogOpen.value = false;
};

const submit = (): void => {
    const options = {
        preserveScroll: true,
        onSuccess: closeDialog,
    };

    if (editing.value) {
        form.put(update.url(editing.value), options);

        return;
    }

    form.post(store.url(), options);
};

const toggleActive = (
    automation: CommentAutomation,
    isActive: boolean,
): void => {
    router.put(
        update.url(automation),
        {
            social_account_id: automation.social_account_id,
            keyword: automation.keyword,
            reply_message: automation.reply_message,
            public_reply: automation.public_reply,
            is_active: isActive,
        },
        { preserveScroll: true },
    );
};

const confirmDeleteModal = ref<InstanceType<typeof ConfirmDeleteModal> | null>(
    null,
);

const handleDelete = (automation: CommentAutomation): void => {
    confirmDeleteModal.value?.open({ url: destroy.url(automation) });
};

const runStatusClass = (status: CommentAutomationRun['status']): string =>
    ({
        pending: 'text-muted-foreground',
        sent: 'text-emerald-600 dark:text-emerald-400',
        failed: 'text-destructive',
    })[status];
</script>

<template>
    <Head :title="$t('comment_automations.title')" />

    <SettingsLayout
        :title="$t('comment_automations.title')"
        :description="$t('comment_automations.description')"
        :centered="automations.length === 0"
    >
        <template v-if="accounts.length > 0" #actions>
            <Button
                data-testid="create-comment-automation-button"
                @click="openCreateDialog"
            >
                <IconPlus class="size-4" />
                {{ $t('comment_automations.new') }}
            </Button>
        </template>

        <EmptyState
            v-if="automations.length === 0"
            :icon="IconMessageCircle"
            :title="$t('comment_automations.empty_title')"
            :description="
                accounts.length > 0
                    ? $t('comment_automations.empty_description')
                    : $t('comment_automations.no_accounts')
            "
            data-testid="comment-automations-empty"
        >
            <template v-if="accounts.length > 0" #action>
                <Button
                    data-testid="comment-automations-empty-create"
                    @click="openCreateDialog"
                >
                    <IconPlus class="size-4" />
                    {{ $t('comment_automations.new') }}
                </Button>
            </template>
        </EmptyState>

        <div v-else class="flex flex-col gap-10">
            <ul class="flex flex-col gap-2">
                <SettingsListRow
                    v-for="automation in automations"
                    :key="automation.id"
                    :data-testid="`comment-automation-row-${automation.id}`"
                >
                    <template #media>
                        <span
                            class="inline-flex size-10 shrink-0 items-center justify-center rounded-lg bg-muted text-muted-foreground"
                        >
                            <IconMessageCircle class="size-5" />
                        </span>
                    </template>
                    <p class="truncate text-sm leading-tight font-emphasis">
                        {{ automation.keyword }}
                    </p>
                    <p class="truncate text-sm text-muted-foreground">
                        {{ accountLabel(automation.social_account) }}
                        <span aria-hidden="true">·</span>
                        {{ automation.reply_message }}
                    </p>
                    <template #actions>
                        <Switch
                            :model-value="automation.is_active"
                            :aria-label="$t('comment_automations.active')"
                            :data-testid="`comment-automation-active-${automation.id}`"
                            @update:model-value="
                                toggleActive(automation, Boolean($event))
                            "
                        />
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="shrink-0 text-muted-foreground data-[state=open]:bg-accent"
                                    :aria-label="
                                        $t('comment_automations.row_actions')
                                    "
                                >
                                    <IconDotsVertical class="size-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <DropdownMenuItem
                                    @click="openEditDialog(automation)"
                                >
                                    <IconPencil class="size-4" />
                                    {{ $t('comment_automations.actions.edit') }}
                                </DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem
                                    variant="destructive"
                                    @click="handleDelete(automation)"
                                >
                                    <IconTrash class="size-4" />
                                    {{
                                        $t('comment_automations.actions.delete')
                                    }}
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </template>
                </SettingsListRow>
            </ul>

            <SettingsSection :title="$t('comment_automations.runs.title')">
                <p
                    v-if="runs.length === 0"
                    class="text-sm text-muted-foreground"
                >
                    {{ $t('comment_automations.runs.empty') }}
                </p>
                <ul v-else class="flex flex-col divide-y text-sm">
                    <li
                        v-for="run in runs"
                        :key="run.id"
                        class="flex items-center justify-between gap-4 py-2"
                        :data-testid="`comment-automation-run-${run.id}`"
                    >
                        <span class="min-w-0 truncate">
                            <span class="font-emphasis">{{
                                run.commenter_username
                                    ? `@${run.commenter_username}`
                                    : '—'
                            }}</span>
                            <span class="text-muted-foreground">
                                · {{ run.comment_automation?.keyword }} ·
                                {{ date.diffForHumans(run.created_at) }}
                            </span>
                        </span>
                        <span
                            :class="['shrink-0', runStatusClass(run.status)]"
                            :title="run.error ?? undefined"
                        >
                            {{ $t(`comment_automations.status.${run.status}`) }}
                        </span>
                    </li>
                </ul>
            </SettingsSection>
        </div>
    </SettingsLayout>

    <Dialog v-model:open="dialogOpen">
        <DialogContent
            data-testid="comment-automation-dialog"
            class="gap-0 p-0 sm:max-w-lg"
        >
            <form @submit.prevent="submit">
                <div class="flex flex-col gap-1.5 px-8 pt-7 pb-5">
                    <DialogTitle class="font-sans text-base font-emphasis">
                        {{
                            editing
                                ? $t('comment_automations.form.edit_title')
                                : $t('comment_automations.form.create_title')
                        }}
                    </DialogTitle>
                    <DialogDescription class="text-sm text-muted-foreground">
                        {{ $t('comment_automations.description') }}
                    </DialogDescription>
                </div>

                <div class="flex flex-col gap-5 px-8 pb-6">
                    <div class="grid gap-2">
                        <Label for="comment-automation-account">{{
                            $t('comment_automations.form.account')
                        }}</Label>
                        <NativeSelect
                            id="comment-automation-account"
                            v-model="form.social_account_id"
                            class="w-full"
                        >
                            <NativeSelectOption
                                v-for="account in accounts"
                                :key="account.id"
                                :value="account.id"
                            >
                                {{ accountLabel(account) }}
                            </NativeSelectOption>
                        </NativeSelect>
                        <InputError :message="form.errors.social_account_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="comment-automation-keyword">{{
                            $t('comment_automations.form.keyword')
                        }}</Label>
                        <Input
                            id="comment-automation-keyword"
                            v-model="form.keyword"
                            autocomplete="off"
                            data-testid="comment-automation-keyword"
                        />
                        <p class="text-sm text-muted-foreground">
                            {{ $t('comment_automations.form.keyword_help') }}
                        </p>
                        <InputError :message="form.errors.keyword" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="comment-automation-reply">{{
                            $t('comment_automations.form.reply_message')
                        }}</Label>
                        <Textarea
                            id="comment-automation-reply"
                            v-model="form.reply_message"
                            rows="3"
                            data-testid="comment-automation-reply"
                        />
                        <InputError :message="form.errors.reply_message" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="comment-automation-public-reply">{{
                            $t('comment_automations.form.public_reply')
                        }}</Label>
                        <Textarea
                            id="comment-automation-public-reply"
                            v-model="form.public_reply"
                            rows="2"
                        />
                        <p class="text-sm text-muted-foreground">
                            {{
                                $t('comment_automations.form.public_reply_help')
                            }}
                        </p>
                        <InputError :message="form.errors.public_reply" />
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <Label for="comment-automation-active">{{
                            $t('comment_automations.active')
                        }}</Label>
                        <Switch
                            id="comment-automation-active"
                            v-model="form.is_active"
                        />
                    </div>
                </div>

                <DialogFooter class="mx-2 mb-2">
                    <Button variant="ghost" type="button" @click="closeDialog">
                        {{ $t('comment_automations.form.cancel') }}
                    </Button>
                    <Button
                        type="submit"
                        data-testid="comment-automation-submit"
                        :loading="form.processing"
                    >
                        {{ $t('comment_automations.form.save') }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <ConfirmDeleteModal
        ref="confirmDeleteModal"
        :title="$t('comment_automations.delete.title')"
        :description="$t('comment_automations.delete.description')"
        :action="$t('comment_automations.delete.confirm')"
        :cancel="$t('comment_automations.delete.cancel')"
    />
</template>
