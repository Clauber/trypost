<?php

declare(strict_types=1);

return [
    'title' => 'DM automatiques',
    'description' => 'Envoyez un DM à qui commente un mot-clé.',
    'new' => 'Créer un DM auto',
    'empty_title' => 'Vous n\'avez pas encore de DM automatique',
    'empty_description' => 'Choisissez un canal Instagram et un mot-clé. Qui le commente reçoit votre message en DM.',
    'no_accounts' => 'Connectez un canal Instagram pour envoyer des DM automatiques.',
    'row_actions' => 'Actions du DM automatique',
    'actions' => [
        'edit' => 'Modifier',
        'delete' => 'Supprimer',
    ],
    'active' => 'Actif',
    'form' => [
        'create_title' => 'Créer un DM auto',
        'edit_title' => 'Modifier le DM auto',
        'account' => 'Canal Instagram',
        'keyword' => 'Mot-clé',
        'keyword_help' => 'Les commentaires contenant ce mot, quelle que soit la casse, reçoivent le DM.',
        'reply_message' => 'Message du DM',
        'public_reply' => 'Réponse publique',
        'public_reply_help' => 'Facultatif. Publiée en réponse au commentaire.',
        'cancel' => 'Annuler',
        'save' => 'Enregistrer',
    ],
    'delete' => [
        'title' => 'Supprimer le DM auto',
        'description' => 'Les commentaires avec ce mot-clé ne recevront plus de DM.',
        'confirm' => 'Supprimer',
        'cancel' => 'Annuler',
    ],
    'runs' => [
        'title' => 'DM récents',
        'empty' => 'Aucun DM envoyé pour l\'instant.',
    ],
    'status' => [
        'pending' => 'Envoi',
        'sent' => 'Envoyé',
        'failed' => 'Échec',
    ],
];
