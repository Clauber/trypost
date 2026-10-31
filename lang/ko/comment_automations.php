<?php

declare(strict_types=1);

return [
    'title' => '자동 DM',
    'description' => '키워드로 댓글을 단 사람에게 DM을 보냅니다.',
    'new' => '자동 DM 만들기',
    'empty_title' => '아직 자동 DM이 없습니다',
    'empty_description' => 'Instagram 채널과 키워드를 선택하세요. 키워드로 댓글을 단 사람은 DM으로 메시지를 받습니다.',
    'no_accounts' => '자동 DM을 보내려면 Instagram 채널을 연결하세요.',
    'row_actions' => '자동 DM 작업',
    'actions' => [
        'edit' => '수정',
        'delete' => '삭제',
    ],
    'active' => '활성',
    'form' => [
        'create_title' => '자동 DM 만들기',
        'edit_title' => '자동 DM 수정',
        'account' => 'Instagram 채널',
        'keyword' => '키워드',
        'keyword_help' => '대소문자와 관계없이 이 단어가 포함된 댓글에 DM을 보냅니다.',
        'reply_message' => 'DM 메시지',
        'public_reply' => '공개 답글',
        'public_reply_help' => '선택 사항. 댓글의 답글로 게시됩니다.',
        'cancel' => '취소',
        'save' => '저장',
    ],
    'delete' => [
        'title' => '자동 DM 삭제',
        'description' => '이 키워드가 포함된 댓글에 더 이상 DM을 보내지 않습니다.',
        'confirm' => '삭제',
        'cancel' => '취소',
    ],
    'runs' => [
        'title' => '최근 DM',
        'empty' => '아직 보낸 DM이 없습니다.',
    ],
    'status' => [
        'pending' => '보내는 중',
        'sent' => '보냄',
        'failed' => '실패',
    ],
];
