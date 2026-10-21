<?php

declare(strict_types=1);

return [
    'title' => 'DM automatici',
    'description' => 'Invia un DM a chi commenta una parola chiave.',
    'new' => 'Crea DM automatico',
    'empty_title' => 'Non hai ancora DM automatici',
    'empty_description' => 'Scegli un canale Instagram e una parola chiave. Chi la commenta riceve il tuo messaggio in DM.',
    'no_accounts' => 'Collega un canale Instagram per inviare DM automatici.',
    'row_actions' => 'Azioni del DM automatico',
    'actions' => [
        'edit' => 'Modifica',
        'delete' => 'Elimina',
    ],
    'active' => 'Attivo',
    'form' => [
        'create_title' => 'Crea DM automatico',
        'edit_title' => 'Modifica DM automatico',
        'account' => 'Canale Instagram',
        'keyword' => 'Parola chiave',
        'keyword_help' => 'I commenti con questa parola, maiuscola o minuscola, ricevono il DM.',
        'reply_message' => 'Messaggio del DM',
        'public_reply' => 'Risposta pubblica',
        'public_reply_help' => 'Facoltativa. Pubblicata come risposta al commento.',
        'cancel' => 'Annulla',
        'save' => 'Salva',
    ],
    'delete' => [
        'title' => 'Elimina DM automatico',
        'description' => 'I commenti con questa parola chiave non riceveranno più un DM.',
        'confirm' => 'Elimina',
        'cancel' => 'Annulla',
    ],
    'runs' => [
        'title' => 'DM recenti',
        'empty' => 'Nessun DM inviato finora.',
    ],
    'status' => [
        'pending' => 'Invio',
        'sent' => 'Inviato',
        'failed' => 'Non riuscito',
    ],
];
