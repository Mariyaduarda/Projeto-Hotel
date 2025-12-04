<?php

require_once __DIR__ . '/../controller/ReservaController.php';

use Controller\ReservaController;

session_start();

// Verifica se o ID foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['mensagem_erro'] = 'ID da reserva não informado.';
    header('Location: lista_reservas.php');
    exit;
}

$id = (int)$_GET['id'];

// Instancia o controller e tenta deletar
$controller = new ReservaController();
$resultado = $controller->deletar($id);

if ($resultado['sucesso']) {
    $_SESSION['mensagem_sucesso'] = $resultado['mensagem'];
} else {
    $_SESSION['mensagem_erro'] = implode('<br>', $resultado['erros']);
}

// Redireciona de volta para a lista
header('Location: lista_reservas.php');
exit;

?>