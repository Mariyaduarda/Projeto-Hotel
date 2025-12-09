<?php
require_once __DIR__ . '/Database.php';  // Mudou de '/../Database.php' para '/Database.php'

use database\Database;

$db = new Database();
$conn = $db->getConnection();

echo "Limpando banco de dados...\n";

try {
    // Desabilita verificação de chave estrangeira temporariamente
    $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // Limpa as tabelas na ordem inversa das dependências
    $conn->exec("TRUNCATE TABLE reserva");
    echo " Tabela reserva limpa.\n";
    
    $conn->exec("TRUNCATE TABLE funcionario");
    echo " Tabela funcionario limpa.\n";
    
    $conn->exec("TRUNCATE TABLE hospede");
    echo " Tabela hospede limpa.\n";
    
    $conn->exec("TRUNCATE TABLE quarto");
    echo " Tabela quarto limpa.\n";
    
    $conn->exec("TRUNCATE TABLE pessoa");
    echo " Tabela pessoa limpa.\n";
    
    $conn->exec("TRUNCATE TABLE endereco");
    echo " Tabela endereco limpa.\n";
    
    // Reabilita verificação de chave estrangeira
    $conn->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "\n Banco de dados limpo com sucesso!\n\n";
    
} catch (Exception $e) {
    echo " Erro ao limpar banco: " . $e->getMessage() . "\n";
}