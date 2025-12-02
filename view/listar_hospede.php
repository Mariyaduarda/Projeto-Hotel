<?php
require_once __DIR__ . '/../controller/HospedeController.php';
require_once __DIR__ . '/../utils/Formatter.php';
require_once __DIR__ . '/../database/Database.php';

use Controller\HospedeController;

$controller = new HospedeController();
$resultado = $controller->listar();

$hospedes = $resultado['sucesso'] ? $resultado['dados'] : [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Hóspedes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .modal-header {
            border-bottom: none;
        }
        .btn {
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-people"></i> Lista de Hóspedes</h2>
            <div>
                <a href="cadastrar_hospede.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Novo Hóspede
                </a>
                <a href="../index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-house"></i> Menu
                </a>
            </div>
        </div>

        <?php if (empty($hospedes)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Nenhum hóspede cadastrado ainda.
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
                            <th>Cadastro</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hospedes as $hospede): ?>
                            <tr>
                                <td><?= htmlspecialchars($hospede['id']) ?></td>
                                <td><?= htmlspecialchars($hospede['nome']) ?></td>
                                <td><?= Formatter::formatarCPF($hospede['cpf']) ?></td>
                                <td><?= htmlspecialchars($hospede['email']) ?></td>
                                <td><?= Formatter::formatarTelefone($hospede['telefone']) ?></td>
                                <td><?= Formatter::formatarData($hospede['data_criacao']) ?></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="editar_hospede.php?id=<?= $hospede['id'] ?>" 
                                           class="btn btn-warning" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="historico_hospede.php?id=<?= $hospede['id'] ?>" 
                                           class="btn btn-info" title="Histórico">
                                            <i class="bi bi-clock-history"></i>
                                        </a>
                                        <button onclick="confirmarExclusao(<?= $hospede['id'] ?>, '<?= htmlspecialchars($hospede['nome'], ENT_QUOTES) ?>')" 
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
                <strong>Total:</strong> <?= count($hospedes) ?> hóspede(s) cadastrado(s)
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal de Confirmação Soft -->
    <div class="modal fade" id="modalExcluir" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center px-5 pb-4">
                    <div class="mb-4">
                        <i class="bi bi-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="mb-3">Tem certeza?</h5>
                    <p class="text-muted mb-2">
                        Você está prestes a excluir o hóspede:
                    </p>
                    <p class="fw-bold mb-3" id="nomeHospede"></p>
                    <p class="text-muted small mb-4">
                        <i class="bi bi-info-circle"></i> Esta ação não pode ser desfeita!
                        <br>
                        Não será possível excluir se houver reservas ativas.
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-danger px-4" id="btnConfirmarExclusao">
                            <i class="bi bi-trash"></i> Sim, excluir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const modalExcluir = new bootstrap.Modal(document.getElementById('modalExcluir'));
        let hospedeIdParaExcluir = null;

        function confirmarExclusao(id, nome) {
            hospedeIdParaExcluir = id;
            document.getElementById('nomeHospede').textContent = nome;
            modalExcluir.show();
        }

        document.getElementById('btnConfirmarExclusao').addEventListener('click', function() {
            if (hospedeIdParaExcluir) {
                window.location.href = 'deletar_hospede.php?id=' + hospedeIdParaExcluir;
            }
        });
    </script>
</body>
</html>