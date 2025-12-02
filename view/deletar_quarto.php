<?php

use controller\QuartoController;
use model\Quarto;
use database\database;

require_once __DIR__ . '/../controller/QuartoController.php';
require_once __DIR__ . '/../model/Quarto.php';
require_once __DIR__ . '/../database/Database.php';
require_once __DIR__ . '/../utils/Validacoes.php';
require_once __DIR__ . '/../utils/Formatter.php';

$id = $_GET['id'] ?? 0;
$controller = new QuartoController();
$resultado = $controller->deletar((int)$id);

if ($resultado['sucesso']) {
    header('Location: lista_quartos.php?msg=success');
} else {
    header('Location: lista_quartos.php?msg=error');
}
exit;
?>