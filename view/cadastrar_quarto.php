<?php

use controller\QuartoController;
use database\Database;
use model\Quarto;

require_once __DIR__ . '/../controller/QuartoController.php';
require_once __DIR__ . '/../database/Database.php';  

$mensagem = '';
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new QuartoController();
    
    $dados = [
        'numero' => $_POST['numero'] ?? 0,
        'andar' => $_POST['andar'] ?? 0,
        'tipo_quarto' => $_POST['tipo_quarto'] ?? '',
        'valor_diaria' => $_POST['valor_diaria'] ?? 0,
        'capacidade_maxima' => $_POST['capacidade_maxima'] ?? 1,
        'descricao' => $_POST['descricao'] ?? null,
        'status' => $_POST['status'] ?? 'disponivel'
    ];
    
    $resultado = $controller->criar($dados);
    
    if ($resultado['sucesso']) {
        $mensagem = $resultado['mensagem'];
        $_POST = [];
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
    <title>Cadastrar Quarto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-door-open"></i> Cadastrar Quarto</h2>
                    <div>
                        <a href="lista_quartos.php" class="btn btn-outline-primary">
                            <i class="bi bi-list"></i> Ver Lista
                        </a>
                        <a href="../index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-house"></i> Menu
                        </a>
                    </div>
                </div>
                
                <?php if ($mensagem): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle"></i> <?= htmlspecialchars($mensagem) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($erros)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle"></i> <strong>Erros:</strong>
                        <ul class="mb-0 mt-2">
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
                                    <label for="numero" class="form-label">Número do Quarto *</label>
                                    <input type="number" class="form-control" id="numero" name="numero" 
                                           value="<?= htmlspecialchars($_POST['numero'] ?? '') ?>" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="andar" class="form-label">Andar *</label>
                                    <input type="number" class="form-control" id="andar" name="andar" 
                                           value="<?= htmlspecialchars($_POST['andar'] ?? '') ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tipo_quarto" class="form-label">Tipo de Quarto *</label>
                                    <select class="form-select" id="tipo_quarto" name="tipo_quarto" required>
                                        <option value="">Selecione</option>
                                        <option value="Standard">Standard</option>
                                        <option value="Luxo">Luxo</option>
                                        <option value="Suite">Suíte</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="capacidade_maxima" class="form-label">Capacidade Maxima *</label>
                                    <input type="number" class="form-control" id="capacidade_maxima" name="capacidade_maxima" 
                                           min="1" max="10" value="<?= htmlspecialchars($_POST['capacidade_maxima'] ?? '2') ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="valor_diaria" class="form-label">Valor da Diaria (R$) *</label>
                                    <input type="number" class="form-control" id="valor_diaria" name="valor_diaria" 
                                           step="0.01" min="0" value="<?= htmlspecialchars($_POST['valor_diaria'] ?? '') ?>" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="disponivel">Disponível</option>
                                        <option value="ocupado">Ocupado</option>
                                        <option value="manutencao">Manutencao</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descricao</label>
                                <textarea class="form-control" id="descricao" name="descricao" rows="3"
                                          placeholder="Ex: Quarto com ar-condicionado, TV, frigobar..."><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Cadastrar Quarto
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Limpar
                                </button>
                                <a href="lista_quartos.php" class="btn btn-outline-primary">
                                    <i class="bi bi-list"></i> Ver Lista
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