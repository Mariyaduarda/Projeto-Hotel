<?php

require_once __DIR__ . '/../controller/HospedeController.php';
require_once __DIR__ . '/../utils/Formatter.php';
require_once __DIR__ . '/../model/Hospede.php';
require_once __DIR__ . '/../model/Pessoa.php';

use Controller\HospedeController;

session_start();

$mensagem = '';
$erros = [];
$hospede = null;

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: listar_hospede.php');
    exit;
}

$id = (int)$_GET['id'];

$controller = new HospedeController();
$resultado = $controller->buscarPorId($id);

if (!$resultado['sucesso']) {
    $_SESSION['mensagem_erro'] = 'Hóspede não encontrado.';
    header('Location: listar_hospede.php');
    exit;
}

$hospede = $resultado['dados'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultadoUpdate = $controller->atualizar($id, $_POST);
    
    if ($resultadoUpdate['sucesso']) {
        $mensagem = $resultadoUpdate['mensagem'];
        $resultado = $controller->buscarPorId($id);
        $hospede = $resultado['dados'];
    } else {
        $erros = $resultadoUpdate['erros'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Hóspede</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-10 mx-auto">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-pencil"></i> Editar Hóspede</h2>
                    <a href="listar_hospede.php" class="btn btn-secondary">
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

                <form method="POST" action="">
                    
                    <h5 class="mt-3 mb-3 border-bottom pb-2">
                        <i class="bi bi-person"></i> Dados Pessoais
                    </h5>

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo *</label>
                        <input type="text" class="form-control" id="nome" name="nome" 
                               value="<?= htmlspecialchars($hospede['nome']) ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="documento" class="form-label">CPF</label>
                            <input type="text" class="form-control" id="documento" name="documento" 
                                   placeholder="000.000.000-00"
                                   value="<?= htmlspecialchars($hospede['documento'] ?? '') ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                            <input type="date" class="form-control" id="data_nascimento" name="data_nascimento"
                                   value="<?= htmlspecialchars($hospede['data_nascimento'] ?? '') ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="sexo" class="form-label">Sexo</label>
                            <select class="form-select" id="sexo" name="sexo">
                                <option value="">Selecione...</option>
                                <option value="M" <?= ($hospede['sexo'] ?? '') == 'M' ? 'selected' : '' ?>>Masculino</option>
                                <option value="F" <?= ($hospede['sexo'] ?? '') == 'F' ? 'selected' : '' ?>>Feminino</option>
                                <option value="Outro" <?= ($hospede['sexo'] ?? '') == 'Outro' ? 'selected' : '' ?>>Outro</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?= htmlspecialchars($hospede['email'] ?? '') ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="telefone" name="telefone"
                                   placeholder="(00) 00000-0000"
                                   value="<?= htmlspecialchars($hospede['telefone'] ?? '') ?>">
                        </div>
                    </div>

                    <hr>

                    <h5 class="mt-3 mb-3 border-bottom pb-2">
                        <i class="bi bi-star"></i> Preferências e Observações
                    </h5>

                    <div class="mb-3">
                        <label for="data_cadastro" class="form-label">Data de Cadastro</label>
                        <input type="date" class="form-control" id="data_cadastro" name="data_cadastro"
                               value="<?= htmlspecialchars($hospede['data_cadastro'] ?? date('Y-m-d')) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="preferencias" class="form-label">Preferências</label>
                        <textarea class="form-control" id="preferencias" name="preferencias" 
                                  rows="2" placeholder="Ex: Quarto silencioso, andar alto, vista para o mar..."><?= htmlspecialchars($hospede['preferencias'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" 
                                  rows="3" placeholder="Ex: Alergias, restrições alimentares, necessidades especiais..."><?= htmlspecialchars($hospede['observacoes'] ?? '') ?></textarea>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Nota:</strong> Para alterar o endereço, entre em contato com o administrador do sistema.
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Salvar Alterações
                        </button>
                        <a href="lista_hospede.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="button" class="btn btn-danger ms-auto" onclick="confirmarExclusao(<?= $id ?>)">
                            <i class="bi bi-trash"></i> Excluir Hóspede
                        </button>
                    </div>
                </form>

                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <i class="bi bi-geo-alt"></i> Informações de Endereço
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>CEP:</strong> <?= Formatter::formatarCEP($hospede['cep'] ?? null) ?></p>
                                <p><strong>Logradouro:</strong> <?= htmlspecialchars($hospede['logradouro'] ?? '-') ?></p>
                                <p><strong>Número:</strong> <?= htmlspecialchars($hospede['numero'] ?? '-') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Bairro:</strong> <?= htmlspecialchars($hospede['bairro'] ?? '-') ?></p>
                                <p><strong>Cidade:</strong> <?= htmlspecialchars($hospede['cidade'] ?? '-') ?></p>
                                <p><strong>Estado:</strong> <?= htmlspecialchars($hospede['estado'] ?? '-') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmarExclusao(id) {
            if (confirm('⚠️ ATENÇÃO!\n\nTem certeza que deseja EXCLUIR este hóspede?\n\nEsta ação NÃO pode ser desfeita!')) {
                window.location.href = 'deletar_hospede.php?id=' + id;
            }
        }
    </script>
</body>
</html>