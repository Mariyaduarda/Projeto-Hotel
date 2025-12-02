<?php
require_once __DIR__ . '/../controller/FuncionarioController.php';
require_once __DIR__ . '/../utils/Formatter.php';

use Controller\FuncionarioController;

$controller = new FuncionarioController();
$resultado = $controller->lista();

// pega a lista ou array vazio
$funcionarios = $resultado['sucesso'] ? $resultado['dados'] : [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Funcionarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Lista de Funcionarios</h2>
            <div>
                <a href="cadastrar_funcionario.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Novo Funcionario
                </a>
                <a href="../index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-house"></i> Menu
                </a>
            </div>
        </div>

        <?php if (empty($funcionarios)): ?>
            <div class="alert alert-info">
                Nenhum funcionario cadastrado ainda.
            </div>

        <?php else: ?>

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Cidade</th>
                            <th>Estado</th>
                            <th>Cadastro</th>
                            <th class="text-center">Acões</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($funcionarios as $f): ?>
                            <tr>
                                <td><?= htmlspecialchars($f['id']) ?></td>
                                <td><?= htmlspecialchars($f['nome']) ?></td>
                                <td><?= Formatter::formatarCPF($f['cpf']) ?></td>
                                <td><?= htmlspecialchars($f['email']) ?></td>
                                <td><?= Formatter::formatarTelefone($f['telefone']) ?></td>

                                <td><?= htmlspecialchars($f['cidade'] ?? '') ?></td>
                                <td><?= htmlspecialchars($f['estado'] ?? '') ?></td>

                                <td><?= Formatter::formatarData($f['data_criacao']) ?></td>

                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">

                                        <a href="editar_funcionario.php?id=<?= $f['id'] ?>" 
                                           class="btn btn-warning" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <button onclick="confirmarExclusao(<?= $f['id'] ?>)" 
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

            <div class="alert alert-light">
                <strong>Total:</strong> <?= count($funcionarios) ?> funcionario(s) cadastrado(s)
            </div>

        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function confirmarExclusao(id) {
            if (confirm('Tem certeza que deseja excluir este funcionario?')) {
                window.location.href = 'deletar_funcionario.php?id=' + id;
            }
        }
    </script>
</body>
</html>
