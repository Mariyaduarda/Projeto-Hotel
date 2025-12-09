<?php
require_once __DIR__ . '/../Database.php';

use database\Database;

$db = new Database();
$conn = $db->getConnection();

try {
    // Limpa a tabela funcionario
    $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
    $conn->exec("TRUNCATE TABLE funcionario");
    $conn->exec("SET FOREIGN_KEY_CHECKS = 1");

    // Insere os funcionários
    $sql = "
    INSERT INTO funcionario (id_pessoa, cargo, salario, data_contratacao, numero_ctps, turno)
    VALUES
    (16, 'Gerente', 8500.00, '2021-03-15', 123456, 'Integral'),
    (17, 'Assistente Administrativo', 3200.00, '2022-07-10', 234567, 'Manhã'),
    (18, 'Recepcionista', 2500.00, '2023-01-05', 345678, 'Tarde'),
    (19, 'Técnico de Som', 4200.50, '2020-11-20', 456789, 'Noite'),
    (20, 'Produtor de Eventos', 5300.00, '2021-09-01', 567890, 'Integral'),
    (21, 'Auxiliar de Limpeza', 1800.00, '2024-02-12', 678901, 'Manhã'),
    (22, 'Segurança', 3100.00, '2022-04-18', 789012, 'Noite'),
    (23, 'Fotógrafo', 4500.00, '2023-09-30', 801234, 'Eventos'),
    (24, 'Camaroteiro', 2700.00, '2024-06-01', 912345, 'Tarde'),
    (25, 'Designer Gráfico', 4000.00, '2021-12-22', 102345, 'Manhã');
    ";

    $conn->exec($sql);
    echo " Tabela funcionario povoada com sucesso.\n";

} catch (Exception $e) {
    echo " Erro ao popular tabela funcionario: " . $e->getMessage() . "\n";
}
