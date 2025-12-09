<?php
require_once __DIR__ . '/../Database.php';

use database\Database;

$db = new Database();
$conn = $db->getConnection();

$sql = "
INSERT INTO reserva (valor_reserva, data_reserva, data_checkin_previsto, data_checkout_previsto, status, id_funcionario, id_hospede, id_quarto) VALUES
(450.00, '2024-10-01', '2024-10-15', '2024-10-18', 'confirmada', 16, 1, 1),
(660.00, '2024-10-05', '2024-10-20', '2024-10-23', 'confirmada', 17, 2, 2),
(1350.00, '2024-09-20', '2024-10-10', '2024-10-13', 'finalizada', 18, 3, 3),
(750.00, '2024-11-01', '2024-11-15', '2024-11-20', 'confirmada', 16, 4, 4),
(1440.00, '2024-10-28', '2024-11-10', '2024-11-13', 'confirmada', 17, 5, 5);
";

try {
    $conn->exec($sql);
    echo " Tabela reserva povoada com sucesso.\n";
} catch (Exception $e) {
    echo " Erro ao popular tabela reserva: " . $e->getMessage() . "\n";
}