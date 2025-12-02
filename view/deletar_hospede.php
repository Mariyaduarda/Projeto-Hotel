<?php

require_once __DIR__ . '/../controller/HospedeController.php';

use Controller\HospedeController;

session_start();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['mensagem_erro'] = 'ID do hóspede não informado.';
    header('Location: listar_hospede.php');
    exit;
}

$id = (int)$_GET['id'];

$controller = new HospedeController();
$resultado = $controller->deletar($id);

if ($resultado['sucesso']) {
    $_SESSION['mensagem_sucesso'] = $resultado['mensagem'];
} else {
    $_SESSION['mensagem_erro'] = implode('<br>', $resultado['erros']);
}

header('Location: listar_hospede.php');
exit;

?>