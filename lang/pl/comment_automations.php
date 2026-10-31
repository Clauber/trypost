<?php

declare(strict_types=1);

return [
    'title' => 'Auto DM',
    'description' => 'Wyślij DM, gdy ktoś skomentuje słowo kluczowe.',
    'new' => 'Utwórz auto DM',
    'empty_title' => 'Nie masz jeszcze auto DM',
    'empty_description' => 'Wybierz kanał Instagram i słowo kluczowe. Kto je skomentuje, dostanie Twoją wiadomość w DM.',
    'no_accounts' => 'Połącz kanał Instagram, aby wysyłać auto DM.',
    'row_actions' => 'Akcje auto DM',
    'actions' => [
        'edit' => 'Edytuj',
        'delete' => 'Usuń',
    ],
    'active' => 'Aktywne',
    'form' => [
        'create_title' => 'Utwórz auto DM',
        'edit_title' => 'Edytuj auto DM',
        'account' => 'Kanał Instagram',
        'keyword' => 'Słowo kluczowe',
        'keyword_help' => 'Komentarze z tym słowem, bez względu na wielkość liter, dostaną DM.',
        'reply_message' => 'Treść DM',
        'public_reply' => 'Odpowiedź publiczna',
        'public_reply_help' => 'Opcjonalnie. Publikowana jako odpowiedź pod komentarzem.',
        'cancel' => 'Anuluj',
        'save' => 'Zapisz',
    ],
    'delete' => [
        'title' => 'Usuń auto DM',
        'description' => 'Komentarze z tym słowem kluczowym nie dostaną już DM.',
        'confirm' => 'Usuń',
        'cancel' => 'Anuluj',
    ],
    'runs' => [
        'title' => 'Ostatnie DM',
        'empty' => 'Nie wysłano jeszcze żadnych DM.',
    ],
    'status' => [
        'pending' => 'Wysyłanie',
        'sent' => 'Wysłano',
        'failed' => 'Błąd',
    ],
];
