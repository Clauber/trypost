<?php

declare(strict_types=1);

return [
    'title' => 'DMs automáticas',
    'description' => 'Envie DM a quem comentar uma palavra-chave.',
    'new' => 'Criar DM automática',
    'empty_title' => 'Você ainda não tem DMs automáticas',
    'empty_description' => 'Escolha um canal do Instagram e uma palavra-chave. Quem comentar recebe sua mensagem na DM.',
    'no_accounts' => 'Conecte um canal do Instagram para enviar DMs automáticas.',
    'row_actions' => 'Ações da DM automática',
    'actions' => [
        'edit' => 'Editar',
        'delete' => 'Excluir',
    ],
    'active' => 'Ativa',
    'form' => [
        'create_title' => 'Criar DM automática',
        'edit_title' => 'Editar DM automática',
        'account' => 'Canal do Instagram',
        'keyword' => 'Palavra-chave',
        'keyword_help' => 'Comentários com esta palavra, em qualquer caixa, recebem a DM.',
        'reply_message' => 'Mensagem da DM',
        'public_reply' => 'Resposta pública',
        'public_reply_help' => 'Opcional. Publicada como resposta ao comentário.',
        'cancel' => 'Cancelar',
        'save' => 'Salvar',
    ],
    'delete' => [
        'title' => 'Excluir DM automática',
        'description' => 'Comentários com esta palavra-chave não vão mais receber DM.',
        'confirm' => 'Excluir',
        'cancel' => 'Cancelar',
    ],
    'runs' => [
        'title' => 'DMs recentes',
        'empty' => 'Nenhuma DM enviada ainda.',
    ],
    'status' => [
        'pending' => 'Enviando',
        'sent' => 'Enviada',
        'failed' => 'Falhou',
    ],
];
