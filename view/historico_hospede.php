<?php
require_once __DIR__ . '/../controller/HospedeController.php';
require_once __DIR__ . '/../utils/Formatter.php';

use Controller\HospedeController;

// Verifica se foi passado um ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: listar_hospede.php');
    exit;
}

$id = (int)$_GET['id'];
$controller = new HospedeController();
$resultado = $controller->buscarPorId($id);

if (!$resultado['sucesso']) {
    header('Location: listar_hospede.php');
    exit;
}

$hospede = $resultado['dados'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico do Hóspede</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .info-card {
            border-left: 4px solid #0d6efd;
            background: #f8f9fa;
        }
        .section-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .badge-custom {
            font-size: 0.9rem;
            padding: 8px 15px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <!-- Cabeçalho -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-clock-history"></i> Histórico do Hóspede</h2>
            <div>
                <a href="editar_hospede.php?id=<?= $id ?>" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Editar
                </a>
                <a href="listar_hospede.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>

        <!-- Dados Pessoais -->
        <div class="section-header">
            <h4 class="mb-0"><i class="bi bi-person-circle"></i> Dados Pessoais</h4>
        </div>

        <div class="card info-card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong><i class="bi bi-person"></i> Nome:</strong>
                        <p class="mb-0"><?= htmlspecialchars($hospede['nome']) ?></p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <strong><i class="bi bi-credit-card"></i> CPF:</strong>
                        <p class="mb-0"><?= Formatter::formatarCPF($hospede['documento']) ?></p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <strong><i class="bi bi-calendar"></i> Data Nascimento:</strong>
                        <p class="mb-0">
                            <?= $hospede['data_nascimento'] ? Formatter::formatarData($hospede['data_nascimento']) : 'Não informado' ?>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <strong><i class="bi bi-envelope"></i> E-mail:</strong>
                        <p class="mb-0"><?= htmlspecialchars($hospede['email']) ?></p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong><i class="bi bi-telephone"></i> Telefone:</strong>
                        <p class="mb-0"><?= Formatter::formatarTelefone($hospede['telefone']) ?></p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong><i class="bi bi-gender-ambiguous"></i> Sexo:</strong>
                        <p class="mb-0">
                            <?php 
                            $sexo = $hospede['sexo'] ?? 'Não informado';
                            if ($sexo === 'M') echo 'Masculino';
                            elseif ($sexo === 'F') echo 'Feminino';
                            else echo $sexo;
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Endereço -->
        <div class="section-header">
            <h4 class="mb-0"><i class="bi bi-geo-alt"></i> Endereço</h4>
        </div>

        <div class="card info-card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <strong><i class="bi bi-signpost"></i> Logradouro:</strong>
                        <p class="mb-0">
                            <?= htmlspecialchars($hospede['logradouro'] ?? 'Não informado') ?>
                            <?php if (!empty($hospede['numero'])): ?>
                                , Nº <?= htmlspecialchars($hospede['numero']) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong><i class="bi bi-mailbox"></i> CEP:</strong>
                        <p class="mb-0"><?= htmlspecialchars($hospede['cep'] ?? 'Não informado') ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-5 mb-3">
                        <strong><i class="bi bi-building"></i> Cidade:</strong>
                        <p class="mb-0"><?= htmlspecialchars($hospede['cidade']) ?></p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <strong><i class="bi bi-map"></i> Estado:</strong>
                        <p class="mb-0"><?= htmlspecialchars($hospede['estado']) ?></p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong><i class="bi bi-globe"></i> País:</strong>
                        <p class="mb-0"><?= htmlspecialchars($hospede['pais'] ?? 'Brasil') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preferências -->
        <div class="section-header">
            <h4 class="mb-0"><i class="bi bi-star"></i> Preferências</h4>
        </div>

        <div class="card info-card mb-4">
            <div class="card-body">
                <?php if (!empty($hospede['preferencias'])): ?>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($hospede['preferencias'])) ?></p>
                <?php else: ?>
                    <p class="text-muted mb-0"><i class="bi bi-info-circle"></i> Nenhuma preferência cadastrada</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Observações/Histórico -->
        <div class="section-header">
            <h4 class="mb-0"><i class="bi bi-journal-text"></i> Observações e Histórico</h4>
        </div>

        <div class="card info-card mb-4">
            <div class="card-body">
                <?php if (!empty($hospede['historico'])): ?>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($hospede['historico'])) ?></p>
                <?php else: ?>
                    <p class="text-muted mb-0"><i class="bi bi-info-circle"></i> Nenhuma observação registrada</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Informações do Sistema -->
        <div class="section-header">
            <h4 class="mb-0"><i class="bi bi-info-square"></i> Informações do Sistema</h4>
        </div>

        <div class="card info-card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <strong><i class="bi bi-calendar-check"></i> Data de Cadastro:</strong>
                        <p class="mb-0">
                            <span class="badge badge-custom bg-primary">
                                <?= isset($hospede['data_criacao']) ? Formatter::formatarData($hospede['data_criacao']) : 'Não disponível' ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <strong><i class="bi bi-calendar-event"></i> Última Atualização:</strong>
                        <p class="mb-0">
                            <span class="badge badge-custom bg-info">
                                <?= isset($hospede['data_atualizacao']) ? Formatter::formatarData($hospede['data_atualizacao']) : 'Não disponível' ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-4">
                        <strong><i class="bi bi-hash"></i> ID do Hóspede:</strong>
                        <p class="mb-0">
                            <span class="badge badge-custom bg-secondary">
                                #<?= $hospede['id_pessoa'] ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção de Reservas (Placeholder para futura implementação) -->
        <div class="section-header">
            <h4 class="mb-0"><i class="bi bi-calendar3"></i> Histórico de Reservas</h4>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i> 
                    O histórico de reservas será exibido aqui quando o módulo de reservas estiver implementado.
                </div>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="d-flex gap-2 mb-5">
            <a href="editar_hospede.php?id=<?= $id ?>" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar Cadastro
            </a>
            <button onclick="imprimirHistorico()" class="btn btn-outline-primary">
                <i class="bi bi-printer"></i> Imprimir
            </button>
            <button onclick="confirmarExclusao(<?= $id ?>)" class="btn btn-outline-danger">
                <i class="bi bi-trash"></i> Excluir Hóspede
            </button>
            <a href="listar_hospede.php" class="btn btn-secondary ms-auto">
                <i class="bi bi-arrow-left"></i> Voltar para Lista
            </a>
        </div>
    </div>

    <!-- Modal de Confirmação -->
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
                    <p class="text-muted mb-4">
                        Você está prestes a excluir o hóspede <strong><?= htmlspecialchars($hospede['nome']) ?></strong>.
                        <br><br>
                        Esta ação não pode ser desfeita!
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
        const hospedeId = <?= $id ?>;

        function imprimirHistorico() {
            window.print();
        }

        function confirmarExclusao(id) {
            modalExcluir.show();
        }

        document.getElementById('btnConfirmarExclusao').addEventListener('click', function() {
            window.location.href = 'deletar_hospede.php?id=' + hospedeId;
        });
    </script>

    <style media="print">
        .btn, .section-header {
            display: none !important;
        }
        .card {
            border: 1px solid #ddd !important;
            page-break-inside: avoid;
        }
    </style>
</body>
</html>