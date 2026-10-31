<?php

declare(strict_types=1);

return [
    'title' => 'MD automáticos',
    'description' => 'Envía un MD a quien comente una palabra clave.',
    'new' => 'Crear MD automático',
    'empty_title' => 'Aún no tienes MD automáticos',
    'empty_description' => 'Elige un canal de Instagram y una palabra clave. Quien la comente recibe tu mensaje por MD.',
    'no_accounts' => 'Conecta un canal de Instagram para enviar MD automáticos.',
    'row_actions' => 'Acciones del MD automático',
    'actions' => [
        'edit' => 'Editar',
        'delete' => 'Eliminar',
    ],
    'active' => 'Activo',
    'form' => [
        'create_title' => 'Crear MD automático',
        'edit_title' => 'Editar MD automático',
        'account' => 'Canal de Instagram',
        'keyword' => 'Palabra clave',
        'keyword_help' => 'Los comentarios con esta palabra, en mayúsculas o minúsculas, reciben el MD.',
        'reply_message' => 'Mensaje del MD',
        'public_reply' => 'Respuesta pública',
        'public_reply_help' => 'Opcional. Se publica como respuesta al comentario.',
        'cancel' => 'Cancelar',
        'save' => 'Guardar',
    ],
    'delete' => [
        'title' => 'Eliminar MD automático',
        'description' => 'Los comentarios con esta palabra clave ya no recibirán un MD.',
        'confirm' => 'Eliminar',
        'cancel' => 'Cancelar',
    ],
    'runs' => [
        'title' => 'MD recientes',
        'empty' => 'Aún no se ha enviado ningún MD.',
    ],
    'status' => [
        'pending' => 'Enviando',
        'sent' => 'Enviado',
        'failed' => 'Falló',
    ],
];
