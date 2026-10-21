<?php

declare(strict_types=1);

return [
    'title' => 'Auto DMs',
    'description' => 'DM people who comment a keyword on Instagram.',
    'new' => 'Create auto DM',
    'empty_title' => 'You don\'t have auto DMs yet',
    'empty_description' => 'Pick an Instagram channel and a keyword. When someone comments it, they get your message in their DMs.',
    'no_accounts' => 'Connect an Instagram channel to send auto DMs.',
    'row_actions' => 'Auto DM actions',
    'actions' => [
        'edit' => 'Edit',
        'delete' => 'Delete',
    ],
    'active' => 'Active',
    'form' => [
        'create_title' => 'Create auto DM',
        'edit_title' => 'Edit auto DM',
        'account' => 'Instagram channel',
        'keyword' => 'Keyword',
        'keyword_help' => 'Comments containing this word, in any case, get the DM.',
        'reply_message' => 'DM message',
        'public_reply' => 'Public reply',
        'public_reply_help' => 'Optional. Posted as a reply under the comment.',
        'cancel' => 'Cancel',
        'save' => 'Save',
    ],
    'delete' => [
        'title' => 'Delete auto DM',
        'description' => 'Comments with this keyword will no longer get a DM.',
        'confirm' => 'Delete',
        'cancel' => 'Cancel',
    ],
    'runs' => [
        'title' => 'Recent DMs',
        'empty' => 'No DMs sent yet.',
    ],
    'status' => [
        'pending' => 'Sending',
        'sent' => 'Sent',
        'failed' => 'Failed',
    ],
];
