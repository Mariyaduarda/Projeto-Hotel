<?php

require_once __DIR__ . '/../controller/FuncionarioController.php';
require_once __DIR__ . '/../model/Funcionario.php';
require_once __DIR__ . '/../model/Pessoa.php';
require_once __DIR__ . '/../database/Database.php';
require_once __DIR__ . '/../utils/Validacoes.php';
require_once __DIR__ . '/../utils/Formatter.php';

use Controller\FuncionarioController;

session_start();

$mensagem = '';
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new FuncionarioController();
    $resultado = $controller->criar($_POST);
    
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
    <title>Cadastrar Funcionario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-10 mx-auto">
                <h2 class="mb-4">Cadastrar Funcionario</h2>
                
                <?php if ($mensagem): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= htmlspecialchars($mensagem) ?>
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
                    
                    <h5 class="mt-3 mb-3">Dados Pessoais</h5>

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo *</label>
                        <input type="text" class="form-control" id="nome" name="nome" 
                               value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="cpf" class="form-label">CPF *</label>
                            <input type="text" class="form-control" id="cpf" name="cpf" 
                                   placeholder="000.000.000-00"
                                   value="<?= htmlspecialchars($_POST['cpf'] ?? '') ?>" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                            <input type="date" class="form-control" id="data_nascimento" name="data_nascimento"
                                   value="<?= htmlspecialchars($_POST['data_nascimento'] ?? '') ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="sexo" class="form-label">Sexo</label>
                            <select class="form-select" id="sexo" name="sexo">
                                <option value="">Selecione...</option>
                                <option value="M" <?= ($_POST['sexo'] ?? '') == 'M' ? 'selected' : '' ?>>Masculino</option>
                                <option value="F" <?= ($_POST['sexo'] ?? '') == 'F' ? 'selected' : '' ?>>Feminino</option>
                                <option value="Outro" <?= ($_POST['sexo'] ?? '') == 'Outro' ? 'selected' : '' ?>>Outro</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">E-mail *</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="telefone" class="form-label">Telefone *</label>
                            <input type="text" class="form-control" id="telefone" name="telefone"
                                   placeholder="(00) 00000-0000"
                                   value="<?= htmlspecialchars($_POST['telefone'] ?? '') ?>" required>
                        </div>
                    </div>

                    <hr>

                    <h5 class="mt-3 mb-3">Dados Profissionais</h5>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="cargo" class="form-label">Cargo</label>
                            <select class="form-select" id="cargo" name="cargo">
                                <option value="">Selecione...</option>
                                <?php 
                                $cargos = ['Recepcionista', 'Gerente', 'Camareira', 'Seguranca', 'Manutencao', 'Chef', 'Garcom'];
                                foreach ($cargos as $cargo): 
                                ?>
                                    <option value="<?= $cargo ?>" <?= ($_POST['cargo'] ?? '') == $cargo ? 'selected' : '' ?>>
                                        <?= $cargo ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="salario" class="form-label">Salario (R$)</label>
                            <input type="number" step="0.01" class="form-control" id="salario" name="salario"
                                   value="<?= htmlspecialchars($_POST['salario'] ?? '') ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="turno" class="form-label">Turno</label>
                            <select class="form-select" id="turno" name="turno">
                                <option value="">Selecione...</option>
                                <?php 
                                $turnos = ['Manha', 'Tarde', 'Noite', 'Integral'];
                                foreach ($turnos as $turno): 
                                ?>
                                    <option value="<?= $turno ?>" <?= ($_POST['turno'] ?? '') == $turno ? 'selected' : '' ?>>
                                        <?= $turno ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="data_contratacao" class="form-label">Data de Contratacao</label>
                            <input type="date" class="form-control" id="data_contratacao" name="data_contratacao"
                                   value="<?= htmlspecialchars($_POST['data_contratacao'] ?? date('Y-m-d')) ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="numero_ctps" class="form-label">Número CTPS</label>
                            <input type="number" class="form-control" id="numero_ctps" name="numero_ctps"
                                   value="<?= htmlspecialchars($_POST['numero_ctps'] ?? '') ?>">
                        </div>
                    </div>

                    <hr>

                    <h5 class="mt-3 mb-3">Endereco</h5>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="cep" class="form-label">CEP</label>
                            <input type="text" class="form-control" id="cep" name="cep"
                                   placeholder="00000-000"
                                   value="<?= htmlspecialchars($_POST['cep'] ?? '') ?>">
                        </div>

                        <div class="col-md-7 mb-3">
                            <label for="endereco" class="form-label">Rua/Logradouro</label>
                            <input type="text" class="form-control" id="endereco" name="endereco"
                                   value="<?= htmlspecialchars($_POST['endereco'] ?? '') ?>">
                        </div>

                        <div class="col-md-2 mb-3">
                            <label for="numero" class="form-label">Número</label>
                            <input type="text" class="form-control" id="numero" name="numero"
                                   value="<?= htmlspecialchars($_POST['numero'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="bairro" class="form-label">Bairro</label>
                            <input type="text" class="form-control" id="bairro" name="bairro"
                                   value="<?= htmlspecialchars($_POST['bairro'] ?? '') ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="cidade" class="form-label">Cidade *</label>
                            <input type="text" class="form-control" id="cidade" name="cidade"
                                   value="<?= htmlspecialchars($_POST['cidade'] ?? '') ?>" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="estado" class="form-label">Estado (UF) *</label>
                            <input type="text" class="form-control" id="estado" name="estado"
                                   placeholder="Ex: MG" maxlength="2"
                                   value="<?= htmlspecialchars($_POST['estado'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Cadastrar Funcionario
                        </button>
                        <a href="lista_funcionario.php" class="btn btn-secondary">
                            <i class="bi bi-list"></i> Ver Lista
                        </a>
                        <a href="../index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-house"></i> Voltar ao Menu
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>