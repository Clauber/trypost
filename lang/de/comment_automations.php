<?php

declare(strict_types=1);

return [
    'title' => 'Auto-DMs',
    'description' => 'Sende eine DM, wenn jemand ein Stichwort kommentiert.',
    'new' => 'Auto-DM erstellen',
    'empty_title' => 'Du hast noch keine Auto-DMs',
    'empty_description' => 'Wähle einen Instagram-Kanal und ein Stichwort. Wer es kommentiert, bekommt deine Nachricht per DM.',
    'no_accounts' => 'Verbinde einen Instagram-Kanal, um Auto-DMs zu senden.',
    'row_actions' => 'Auto-DM-Aktionen',
    'actions' => [
        'edit' => 'Bearbeiten',
        'delete' => 'Löschen',
    ],
    'active' => 'Aktiv',
    'form' => [
        'create_title' => 'Auto-DM erstellen',
        'edit_title' => 'Auto-DM bearbeiten',
        'account' => 'Instagram-Kanal',
        'keyword' => 'Stichwort',
        'keyword_help' => 'Kommentare mit diesem Wort, egal ob groß oder klein, bekommen die DM.',
        'reply_message' => 'DM-Nachricht',
        'public_reply' => 'Öffentliche Antwort',
        'public_reply_help' => 'Optional. Wird als Antwort unter dem Kommentar gepostet.',
        'cancel' => 'Abbrechen',
        'save' => 'Speichern',
    ],
    'delete' => [
        'title' => 'Auto-DM löschen',
        'description' => 'Kommentare mit diesem Stichwort bekommen keine DM mehr.',
        'confirm' => 'Löschen',
        'cancel' => 'Abbrechen',
    ],
    'runs' => [
        'title' => 'Letzte DMs',
        'empty' => 'Noch keine DMs gesendet.',
    ],
    'status' => [
        'pending' => 'Wird gesendet',
        'sent' => 'Gesendet',
        'failed' => 'Fehlgeschlagen',
    ],
];
