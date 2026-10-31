<?php

declare(strict_types=1);

return [
    'title' => '自動DM',
    'description' => 'キーワードをコメントした人にDMを送ります。',
    'new' => '自動DMを作成',
    'empty_title' => '自動DMはまだありません',
    'empty_description' => 'Instagramチャンネルとキーワードを選びます。そのキーワードをコメントした人にメッセージがDMで届きます。',
    'no_accounts' => '自動DMを送るにはInstagramチャンネルを接続してください。',
    'row_actions' => '自動DMの操作',
    'actions' => [
        'edit' => '編集',
        'delete' => '削除',
    ],
    'active' => '有効',
    'form' => [
        'create_title' => '自動DMを作成',
        'edit_title' => '自動DMを編集',
        'account' => 'Instagramチャンネル',
        'keyword' => 'キーワード',
        'keyword_help' => '大文字・小文字を問わず、この語を含むコメントにDMを送ります。',
        'reply_message' => 'DMメッセージ',
        'public_reply' => '公開返信',
        'public_reply_help' => '任意。コメントへの返信として投稿されます。',
        'cancel' => 'キャンセル',
        'save' => '保存',
    ],
    'delete' => [
        'title' => '自動DMを削除',
        'description' => 'このキーワードのコメントにはDMが送られなくなります。',
        'confirm' => '削除',
        'cancel' => 'キャンセル',
    ],
    'runs' => [
        'title' => '最近のDM',
        'empty' => 'まだDMは送信されていません。',
    ],
    'status' => [
        'pending' => '送信中',
        'sent' => '送信済み',
        'failed' => '失敗',
    ],
];
