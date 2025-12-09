<?php
require_once __DIR__ . '/../Database.php';

echo "Iniciando povoamento...\n";

// 1. Primeiro as tabelas sem dependências
include __DIR__ . '/seed_endereco.php';
include __DIR__ . '/seed_pessoa.php';      // pessoa depende de endereco

// 2. Depois as tabelas que dependem de pessoa
include __DIR__ . '/seed_hospede.php';     // hospede depende de pessoa
include __DIR__ . '/seed_funcionario.php'; // funcionario depende de pessoa

// 3. Tabelas independentes ou que dependem de outras
include __DIR__ . '/seed_quarto.php';

// 4. Por último, reserva (depende de funcionario, hospede e quarto)
include __DIR__ . '/seed_reserva.php';

echo "Povoamento finalizado!\n";