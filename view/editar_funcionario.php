<?php

require_once __DIR__ . '/../controller/FuncionarioController.php';
require_once __DIR__ . '/../model/Funcionario.php';
require_once __DIR__ . '/../model/Pessoa.php';
require_once __DIR__ . '/../utils/Formatter.php';

use Controller\FuncionarioController;

session_start();

$mensagem = '';
$erros = [];
$funcionario = null;

// Verifica se o ID foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: lista_funcionario.php');
    exit;
}

$id = (int)$_GET['id'];

// Busca os dados do funcionario
$controller = new FuncionarioController();
$resultado = $controller->buscarPorId($id);

if (!$resultado['sucesso']) {
    $_SESSION['mensagem_erro'] = 'Funcionario nao encontrado.';
    header('Location: lista_funcionario.php');
    exit;
}

$funcionario = $resultado['dados'];

// Processa o formulario de edicao
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultadoUpdate = $controller->atualizar($id, $_POST);
    
    if ($resultadoUpdate['sucesso']) {
        $mensagem = $resultadoUpdate['mensagem'];
        // Recarrega os dados atualizados
        $resultado = $controller->buscarPorId($id);
        $funcionario = $resultado['dados'];
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
    <title>Editar Funcionario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-10 mx-auto">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Editar Funcionario</h2>
                    <div>
                        <a href="lista_funcionario.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
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
                        <i class="bi bi-exclamation-triangle"></i>
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
                               value="<?= htmlspecialchars($funcionario['nome']) ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="documento" class="form-label">CPF</label>
                            <input type="text" class="form-control" id="documento" name="documento" 
                                   placeholder="000.000.000-00"
                                   value="<?= htmlspecialchars($funcionario['documento'] ?? '') ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                            <input type="date" class="form-control" id="data_nascimento" name="data_nascimento"
                                   value="<?= htmlspecialchars($funcionario['data_nascimento'] ?? '') ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="sexo" class="form-label">Sexo</label>
                            <select class="form-select" id="sexo" name="sexo">
                                <option value="">Selecione...</option>
                                <option value="M" <?= ($funcionario['sexo'] ?? '') == 'M' ? 'selected' : '' ?>>Masculino</option>
                                <option value="F" <?= ($funcionario['sexo'] ?? '') == 'F' ? 'selected' : '' ?>>Feminino</option>
                                <option value="Outro" <?= ($funcionario['sexo'] ?? '') == 'Outro' ? 'selected' : '' ?>>Outro</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?= htmlspecialchars($funcionario['email'] ?? '') ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="telefone" name="telefone"
                                   placeholder="(00) 00000-0000"
                                   value="<?= htmlspecialchars($funcionario['telefone'] ?? '') ?>">
                        </div>
                    </div>

                    <hr>

                    <h5 class="mt-3 mb-3 border-bottom pb-2">
                        <i class="bi bi-briefcase"></i> Dados Profissionais
                    </h5>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="cargo" class="form-label">Cargo</label>
                            <select class="form-select" id="cargo" name="cargo">
                                <option value="">Selecione...</option>
                                <?php 
                                $cargos = ['Recepcionista', 'Gerente', 'Camareira', 'Seguranca', 'Manutencao', 'Chef', 'Garcom'];
                                foreach ($cargos as $cargo): 
                                ?>
                                    <option value="<?= $cargo ?>" <?= ($funcionario['cargo'] ?? '') == $cargo ? 'selected' : '' ?>>
                                        <?= $cargo ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="salario" class="form-label">Salario (R$)</label>
                            <input type="number" step="0.01" class="form-control" id="salario" name="salario"
                                   value="<?= htmlspecialchars($funcionario['salario'] ?? '') ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="turno" class="form-label">Turno</label>
                            <select class="form-select" id="turno" name="turno">
                                <option value="">Selecione...</option>
                                <?php 
                                $turnos = ['Manha', 'Tarde', 'Noite', 'Integral'];
                                foreach ($turnos as $turno): 
                                ?>
                                    <option value="<?= $turno ?>" <?= ($funcionario['turno'] ?? '') == $turno ? 'selected' : '' ?>>
                                        <?= $turno ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="data_contratacao" class="form-label">Data de Contratacao *</label>
                            <input type="date" class="form-control" id="data_contratacao" name="data_contratacao"
                                   value="<?= htmlspecialchars($funcionario['data_contratacao'] ?? date('Y-m-d')) ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="numero_ctps" class="form-label">Número CTPS</label>
                            <input type="number" class="form-control" id="numero_ctps" name="numero_ctps"
                                   value="<?= htmlspecialchars($funcionario['numero_ctps'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Nota:</strong> Para alterar o endereco, entre em contato com o administrador do sistema.
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Salvar Alteracões
                        </button>
                        <a href="lista_funcionario.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="button" class="btn btn-danger ms-auto" onclick="confirmarExclusao(<?= $id ?>)">
                            <i class="bi bi-trash"></i> Excluir Funcionario
                        </button>
                    </div>
                </form>

                <!-- Informacões adicionais -->
                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <i class="bi bi-geo-alt"></i> Informacões de Endereco
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>CEP:</strong> <?= Formatter::formatarCEP($funcionario['cep'] ?? null) ?></p>
                                <p><strong>Logradouro:</strong> <?= htmlspecialchars($funcionario['logradouro'] ?? '-') ?></p>
                                <p><strong>Número:</strong> <?= htmlspecialchars($funcionario['numero'] ?? '-') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Bairro:</strong> <?= htmlspecialchars($funcionario['bairro'] ?? '-') ?></p>
                                <p><strong>Cidade:</strong> <?= htmlspecialchars($funcionario['cidade'] ?? '-') ?></p>
                                <p><strong>Estado:</strong> <?= htmlspecialchars($funcionario['estado'] ?? '-') ?></p>
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
            if (confirm('Tem certeza que deseja EXCLUIR este funcionario?\n\nEsta acao NÃO pode ser desfeita!')) {
                window.location.href = 'deletar_funcionario.php?id=' + id;
            }
        }
    </script>
</body>
</html>