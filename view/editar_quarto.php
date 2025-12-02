<?php

use controller\QuartoController;
use model\Quarto;
use database\database;

require_once __DIR__ . '/../controller/QuartoController.php';
require_once __DIR__ . '/../controller/QuartoController.php';
require_once __DIR__ . '/../model/Quarto.php';
require_once __DIR__ . '/../database/Database.php';
require_once __DIR__ . '/../utils/Validacoes.php';
require_once __DIR__ . '/../utils/Formatter.php';

$id = $_GET['id'] ?? 0;
$controller = new QuartoController();

$mensagem = '';
$erros = [];

$resultado = $controller->buscarPorId((int)$id);

if (!$resultado['sucesso']) {
    header('Location: lista_quartos.php');
    exit;
}

$quarto = $resultado['dados'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'numero' => $_POST['numero'] ?? 0,
        'andar' => $_POST['andar'] ?? 0,
        'tipo_quarto' => $_POST['tipo_quarto'] ?? '',
        'valor_diaria' => $_POST['valor_diaria'] ?? 0,
        'capacidade_maxima' => $_POST['capacidade_maxima'] ?? 1,
        'descricao' => $_POST['descricao'] ?? null,
        'status' => $_POST['status'] ?? 'disponivel'
    ];
    
    $resultado = $controller->atualizar((int)$id, $dados);
    
    if ($resultado['sucesso']) {
        $mensagem = $resultado['mensagem'];
        $resultado = $controller->buscarPorId((int)$id);
        $quarto = $resultado['dados'];
    } else {
        $erros = $resultado['erros'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Quarto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-pencil-square"></i> Editar Quarto</h2>
                    <a href="lista_quartos.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>

                <?php if ($mensagem): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle"></i> <?= htmlspecialchars($mensagem) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($erros)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            <?php foreach ($erros as $erro): ?>
                                <li><?= htmlspecialchars($erro) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="numero" class="form-label">Número *</label>
                                    <input type="number" class="form-control" id="numero" name="numero" 
                                           value="<?= htmlspecialchars($quarto['numero']) ?>" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="andar" class="form-label">Andar *</label>
                                    <input type="number" class="form-control" id="andar" name="andar" 
                                           value="<?= htmlspecialchars($quarto['andar']) ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tipo_quarto" class="form-label">Tipo *</label>
                                    <select class="form-select" id="tipo_quarto" name="tipo_quarto" required>
                                        <option value="Standard" <?= $quarto['tipo_quarto'] === 'Standard' ? 'selected' : '' ?>>Standard</option>
                                        <option value="Luxo" <?= $quarto['tipo_quarto'] === 'Luxo' ? 'selected' : '' ?>>Luxo</option>
                                        <option value="Suite" <?= $quarto['tipo_quarto'] === 'Suite' ? 'selected' : '' ?>>Suíte</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="capacidade_maxima" class="form-label">Capacidade *</label>
                                    <input type="number" class="form-control" id="capacidade_maxima" name="capacidade_maxima" 
                                           min="1" value="<?= htmlspecialchars($quarto['capacidade_maxima']) ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="valor_diaria" class="form-label">Valor Diaria *</label>
                                    <input type="number" class="form-control" id="valor_diaria" name="valor_diaria" 
                                           step="0.01" value="<?= htmlspecialchars($quarto['valor_diaria']) ?>" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="disponivel" <?= $quarto['status'] === 'disponivel' ? 'selected' : '' ?>>Disponível</option>
                                        <option value="ocupado" <?= $quarto['status'] === 'ocupado' ? 'selected' : '' ?>>Ocupado</option>
                                        <option value="manutencao" <?= $quarto['status'] === 'manutencao' ? 'selected' : '' ?>>Manutencao</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descricao</label>
                                <textarea class="form-control" id="descricao" name="descricao" rows="3"><?= htmlspecialchars($quarto['descricao'] ?? '') ?></textarea>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Salvar
                                </button>
                                <a href="lista_quartos.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>