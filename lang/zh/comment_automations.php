<?php

declare(strict_types=1);

return [
    'title' => '自动私信',
    'description' => '向评论关键词的人发送私信。',
    'new' => '创建自动私信',
    'empty_title' => '还没有自动私信',
    'empty_description' => '选择一个 Instagram 频道和关键词。评论该关键词的人会收到你的私信。',
    'no_accounts' => '连接 Instagram 频道即可发送自动私信。',
    'row_actions' => '自动私信操作',
    'actions' => [
        'edit' => '编辑',
        'delete' => '删除',
    ],
    'active' => '已启用',
    'form' => [
        'create_title' => '创建自动私信',
        'edit_title' => '编辑自动私信',
        'account' => 'Instagram 频道',
        'keyword' => '关键词',
        'keyword_help' => '包含此词的评论（不区分大小写）都会收到私信。',
        'reply_message' => '私信内容',
        'public_reply' => '公开回复',
        'public_reply_help' => '可选。作为评论的回复发布。',
        'cancel' => '取消',
        'save' => '保存',
    ],
    'delete' => [
        'title' => '删除自动私信',
        'description' => '包含此关键词的评论将不再收到私信。',
        'confirm' => '删除',
        'cancel' => '取消',
    ],
    'runs' => [
        'title' => '最近的私信',
        'empty' => '尚未发送私信。',
    ],
    'status' => [
        'pending' => '发送中',
        'sent' => '已发送',
        'failed' => '失败',
    ],
];
