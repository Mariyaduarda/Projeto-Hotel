<?php
require_once __DIR__ . '/../../database/Database.php';
require_once __DIR__ . '/../../controller/RelatorioController.php';
require_once __DIR__ . '/../../utils/Formatter.php';

use Controller\RelatorioController;

$controller = new RelatorioController();

// Buscar dados do dashboard
$dashboard = $controller->dashboard();
$topHospedes = $controller->hospedesMaisFrequentes(10);

$stats = $dashboard['sucesso'] ? $dashboard['estatisticas'] : [];
$hospedesAtivos = $dashboard['sucesso'] ? $dashboard['hospedes_ativos'] : [];
$top = $topHospedes['sucesso'] ? $topHospedes['dados'] : [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Hóspedes - Palácio Lumière</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .stat-card-primary { border-color: #0d6efd; }
        .stat-card-success { border-color: #198754; }
        .stat-card-warning { border-color: #ffc107; }
        .stat-card-info { border-color: #0dcaf0; }
        
        .top-badge {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .top-1 { background: linear-gradient(135deg, #FFD700, #FFA500); }
        .top-2 { background: linear-gradient(135deg, #C0C0C0, #808080); }
        .top-3 { background: linear-gradient(135deg, #CD7F32, #8B4513); }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <!-- Menu Lateral (Sidebar) -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="../../index.php"><img src="../../assets/img/logo.png" alt="Palácio Lumière Logo"></a>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-item">
                <a href="../../index.php"><i class="fas fa-tachometer-alt"></i> Painel</a>
            </div>
            <!-- Hóspedes Dropdown -->
            <div class="nav-item">
                <div class="dropdown-toggle" onclick="toggleDropdown(this)">
                    <span><i class="fas fa-users"></i> Hóspedes</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="dropdown-menu">
                    <a href="../../view/cadastrar_hospede.php"><i class="fas fa-plus"></i> Cadastrar</a>
                    <a href="../../view/listar_hospede.php"><i class="fas fa-list"></i> Listar</a>
                    <a href="../../view/editar_hospede.php"><i class="fas fa-edit"></i> Editar</a>
                    <a href="../../view/deletar_hospede.php"><i class="fas fa-trash"></i> Deletar</a>
                </div>
            </div>
            <!-- Funcionários Dropdown -->
            <div class="nav-item">
                <div class="dropdown-toggle" onclick="toggleDropdown(this)">
                    <span><i class="fas fa-briefcase"></i> Funcionários</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="dropdown-menu">
                    <a href="../../view/cadastrar_funcionario.php"><i class="fas fa-plus"></i> Cadastrar</a>
                    <a href="../../view/lista_funcionario.php"><i class="fas fa-list"></i> Listar</a>
                    <a href="../../view/editar_funcionario.php"><i class="fas fa-edit"></i> Editar</a>
                    <a href="../../view/deletar_funcionario.php"><i class="fas fa-trash"></i> Deletar</a>
                </div>
            </div>
            <!-- Quartos Dropdown -->
            <div class="nav-item">
                <div class="dropdown-toggle" onclick="toggleDropdown(this)">
                    <span><i class="fas fa-door-open"></i> Quartos</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="dropdown-menu">
                    <a href="../../view/cadastrar_quarto.php"><i class="fas fa-plus"></i> Cadastrar</a>
                    <a href="../../view/lista_quartos.php"><i class="fas fa-list"></i> Listar</a>
                    <a href="../../view/editar_quartos.php"><i class="fas fa-edit"></i> Editar</a>
                    <a href="../../view/deletar_quarto.php"><i class="fas fa-trash"></i> Deletar</a>
                </div>
            </div>
            <!-- Reservas Dropdown -->
            <div class="nav-item">
                <div class="dropdown-toggle" onclick="toggleDropdown(this)">
                    <span><i class="fas fa-calendar-alt"></i> Reservas</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="dropdown-menu">
                    <a href="../../criar_reserva.php"><i class="fas fa-plus"></i> Nova Reserva</a>
                    <a href="../../lista_reservas.php"><i class="fas fa-list"></i> Listar</a>
                    <a href="../../editar_reserva.php"><i class="fas fa-edit"></i> Editar</a>
                    <a href="../../deletar_reserva.php"><i class="fas fa-trash"></i> Deletar</a>
                </div>
            </div>
            <!-- Relatórios -->
            <div class="nav-item">
                <a href="relatorio_hospede.php" class="active"><i class="fas fa-chart-bar"></i> Relatórios</a>
            </div>
        </nav>
    </aside>

    <!-- Conteúdo Principal -->
    <main class="main-content">
        <header class="main-header" style="margin-bottom: 40px;">
            <h1>Relatório de Hóspedes</h1>
            <p style="color: #666; font-size: 0.9rem; margin-top: 5px;">
                <i class="fas fa-calendar-day"></i> Hoje: <?= date('d/m/Y') ?>
            </p>
        </header>
        <div class="container mt-3" style="max-width: 1400px;">
            <!-- Cards de Estatísticas -->
            <div class="summary-cards" style="margin-bottom: -20px;">
                <div class="card">
                    <div class="card-icon" style="background-color: #e3f2fd; color: #0d6efd;">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="card-info">
                        <span class="card-value"><?= $stats['total_hospedes'] ?? 0 ?></span>
                        <span class="card-label">Total de Hóspedes</span>
                    </div>
                </div>
                <div class="card">
                    <div class="card-icon" style="background-color: #d1f5e0; color: #198754;">
                        <i class="bi bi-person-check"></i>
                    </div>
                    <div class="card-info">
                        <span class="card-value"><?= $stats['hospedes_ativos'] ?? 0 ?></span>
                        <span class="card-label">Hóspedes Ativos</span>
                    </div>
                </div>
                <div class="card">
                    <div class="card-icon" style="background-color: #fff8e1; color: #ffc107;">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="card-info">
                        <span class="card-value"><?= $stats['total_reservas'] ?? 0 ?></span>
                        <span class="card-label">Total Reservas</span>
                    </div>
                </div>
                <div class="card">
                    <div class="card-icon" style="background-color: #e0f7fa; color: #0dcaf0;">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="card-info">
                        <span class="card-value">R$ <?= number_format($stats['receita_total'] ?? 0, 2, ',', '.') ?></span>
                        <span class="card-label">Receita Total</span>
                    </div>
                </div>
            </div>

            <!-- Hóspedes Ativos Agora -->
            <div class="card mb-5" style="padding-bottom: 10px;">
                <div class="card-header bg-success text-white" style="margin-bottom: 20px;">
                    <h5 class="mb-0"><i class="bi bi-door-open"></i> Hóspedes com Check-in Ativo</h5>
                </div>
                <div class="card-body" style="padding-bottom: 0;">
                    <?php if (empty($hospedesAtivos)): ?>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> Nenhum hóspede com check-in ativo no momento.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive mb-0">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Quarto</th>
                                        <th>Check-in</th>
                                        <th>Check-out</th>
                                        <th>Dias Restantes</th>
                                        <th>Contato</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($hospedesAtivos as $hospede): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($hospede['nome']) ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    Quarto <?= htmlspecialchars($hospede['numero_quarto']) ?>
                                                </span>
                                            </td>
                                            <td><?= Formatter::formatarData($hospede['data_checkin']) ?></td>
                                            <td><?= Formatter::formatarData($hospede['data_checkout']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $hospede['dias_restantes'] <= 1 ? 'danger' : 'info' ?>">
                                                    <?= $hospede['dias_restantes'] ?> dia(s)
                                                </span>
                                            </td>
                                            <td>
                                                <small>
                                                    <i class="bi bi-telephone"></i> <?= Formatter::formatarTelefone($hospede['telefone']) ?>
                                                </small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Top 10 Hóspedes Mais Frequentes -->
            <div class="card mb-5" style="padding-bottom: 10px;">
                <div class="card-header bg-primary text-white" style="margin-bottom: 20px;">
                    <h5 class="mb-0"><i class="bi bi-trophy"></i> Top 10 Hóspedes Mais Frequentes</h5>
                </div>
                <div class="card-body" style="padding-bottom: 0;">
                    <?php if (empty($top)): ?>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i> Nenhum dado disponível ainda.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive mb-0">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Nome</th>
                                        <th class="text-center">Total Reservas</th>
                                        <th class="text-end">Valor Gasto</th>
                                        <th class="text-end">Ticket Médio</th>
                                        <th>Última Visita</th>
                                        <th>Cliente Desde</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($top as $index => $hospede): ?>
                                        <tr>
                                            <td>
                                                <?php if ($index < 3): ?>
                                                    <div class="top-badge top-<?= $index + 1 ?>">
                                                        <?= $index + 1 ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="text-center fw-bold text-muted">
                                                        <?= $index + 1 ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($hospede['nome']) ?></strong>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="bi bi-envelope"></i> <?= htmlspecialchars($hospede['email']) ?>
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info fs-6">
                                                    <?= $hospede['total_reservas'] ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <strong class="text-success">
                                                    R$ <?= number_format($hospede['valor_total_gasto'], 2, ',', '.') ?>
                                                </strong>
                                            </td>
                                            <td class="text-end">
                                                R$ <?= number_format($hospede['ticket_medio'], 2, ',', '.') ?>
                                            </td>
                                            <td>
                                                <small><?= Formatter::formatarData($hospede['ultima_visita']) ?></small>
                                            </td>
                                            <td>
                                                <small><?= Formatter::formatarData($hospede['primeira_visita']) ?></small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Informações Adicionais -->
            <div class="card mb-5">
                <div class="card-body bg-light py-4">
                    <div class="row text-center g-4">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <h5 class="text-muted mb-2">Ticket Médio Geral</h5>
                            <h3 class="text-primary mb-0">R$ <?= number_format($stats['ticket_medio_geral'] ?? 0, 2, ',', '.') ?></h3>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <h5 class="text-muted mb-2">Taxa de Ocupação</h5>
                            <h3 class="text-success mb-0">
                                <?php 
                                $taxa = $stats['total_hospedes'] > 0 
                                    ? ($stats['hospedes_ativos'] / $stats['total_hospedes']) * 100 
                                    : 0;
                                echo number_format($taxa, 1) . '%';
                                ?>
                            </h3>
                        </div>
                        <div class="col-md-4">
                            <h5 class="text-muted mb-2">Média Reservas/Hóspede</h5>
                            <h3 class="text-info mb-0">
                                <?php 
                                $media = $stats['total_hospedes'] > 0 
                                    ? $stats['total_reservas'] / $stats['total_hospedes'] 
                                    : 0;
                                echo number_format($media, 1);
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<script>
function toggleDropdown(element) {
    const menu = element.nextElementSibling;
    menu.classList.toggle('show');
    element.classList.toggle('active');
}
</script>
</body>
</html>