<?php
return [
    'home' => 'Ir para o início',
    'pages' => [
        403 => ['title' => 'Acesso negado', 'message' => 'Você não tem permissão para acessar esta página.'],
        404 => ['title' => 'Página não encontrada', 'message' => 'Não foi possível encontrar a página que você procura.'],
        419 => ['title' => 'Página expirada', 'message' => 'Sua sessão expirou. Volte e tente novamente.'],
        500 => ['title' => 'Erro no servidor', 'message' => 'Ocorreu um erro. Tente novamente mais tarde.'],
        503 => ['title' => 'Indisponível temporariamente', 'message' => 'O site está temporariamente indisponível. Tente novamente em instantes.'],
    ],
];
