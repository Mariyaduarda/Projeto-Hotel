<?php

require_once __DIR__ . '/../controller/FuncionarioController.php';

use Controller\FuncionarioController;

session_start();

// Verifica se o ID foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['mensagem_erro'] = 'ID do funcionario nao informado.';
    header('Location: lista_funcionario.php');
    exit;
}

$id = (int)$_GET['id'];

// Instancia o controller e tenta deletar
$controller = new FuncionarioController();
$resultado = $controller->deletar($id);

if ($resultado['sucesso']) {
    $_SESSION['mensagem_sucesso'] = $resultado['mensagem'];
} else {
    $_SESSION['mensagem_erro'] = implode('<br>', $resultado['erros']);
}

// Redireciona de volta para a lista
header('Location: lista_funcionario.php');
exit;

?>