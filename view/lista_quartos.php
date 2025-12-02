<?php

use controller\QuartoController;
use database\database;
use model\Quarto;

require_once __DIR__ . '/../controller/QuartoController.php';
require_once __DIR__ . '/../controller/QuartoController.php';
require_once __DIR__ . '/../model/Quarto.php';
require_once __DIR__ . '/../database/Database.php';
require_once __DIR__ . '/../utils/Validacoes.php';
require_once __DIR__ . '/../utils/Formatter.php';

$controller = new QuartoController();
$resultado = $controller->lista();

$quartos = $resultado['sucesso'] ? $resultado['dados'] : [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Quartos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-door-closed"></i> Lista de Quartos</h2>
            <div>
                <a href="cadastrar_quarto.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Novo Quarto
                </a>
                <a href="../index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-house"></i> Menu Principal
                </a>
            </div>
        </div>

        <?php if (empty($quartos)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Nenhum quarto cadastrado ainda.
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Número</th>
                                    <th>Andar</th>
                                    <th>Tipo</th>
                                    <th>Capacidade</th>
                                    <th>Valor/Diaria</th>
                                    <th>Status</th>
                                    <th class="text-center">Acões</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($quartos as $quarto): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($quarto['numero']) ?></strong></td>
                                        <td><?= htmlspecialchars($quarto['andar']) ?>º</td>
                                        <td><?= htmlspecialchars($quarto['tipo_quarto']) ?></td>
                                        <td><?= htmlspecialchars($quarto['capacidade_maxima']) ?> pessoa(s)</td>
                                        <td>R$ <?= number_format($quarto['valor_diaria'], 2, ',', '.') ?></td>
                                        <td>
                                            <?php
                                            $badges = [
                                                'disponivel' => 'success',
                                                'ocupado' => 'danger',
                                                'manutencao' => 'warning'
                                            ];
                                            $badge = $badges[$quarto['status']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $badge ?>">
                                                <?= ucfirst($quarto['status']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="editar_quarto.php?id=<?= $quarto['id_quarto'] ?>" 
                                                   class="btn btn-warning" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button onclick="confirmarExclusao(<?= $quarto['id_quarto'] ?>)" 
                                                        class="btn btn-danger" title="Excluir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <i class="bi bi-info-circle"></i> Total: <strong><?= count($quartos) ?></strong> quarto(s) cadastrado(s)
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmarExclusao(id) {
            if (confirm('Tem certeza que deseja excluir este quarto?')) {
                window.location.href = 'deletar_quarto.php?id=' + id;
            }
        }
    </script>
</body>
</html>