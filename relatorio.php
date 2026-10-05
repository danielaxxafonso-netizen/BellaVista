<?php
require_once 'conexao.php';
verificarLogin();

$totalQuartos = $pdo->query("SELECT COUNT(*) FROM quartos")->fetchColumn();
$livres = $pdo->query("SELECT COUNT(*) FROM quartos WHERE status='livre'")->fetchColumn();
$ocupados = $pdo->query("SELECT COUNT(*) FROM quartos WHERE status='ocupado'")->fetchColumn();
$manutencao = $pdo->query("SELECT COUNT(*) FROM quartos WHERE status='manutencao'")->fetchColumn();
$taxaOcupacao = $totalQuartos > 0 ? round(($ocupados/$totalQuartos)*100, 1) : 0;

$faturamento = $pdo->query("SELECT COALESCE(SUM(valor_total),0) FROM reservas WHERE status='checkin'")->fetchColumn();
$totalReservas = $pdo->query("SELECT COUNT(*) FROM reservas")->fetchColumn();
$totalHospedes = $pdo->query("SELECT COUNT(*) FROM hospedes")->fetchColumn();

// Categorias mais usadas
$categorias = $pdo->query("SELECT c.nome, COUNT(q.id) as total FROM categorias c LEFT JOIN quartos q ON c.id=q.categoria_id GROUP BY c.id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f5f7fa; font-family: 'Segoe UI', sans-serif; }
        .sidebar { background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%); min-height: 100vh; color: white; }
        .sidebar .logo { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar a { color: rgba(255,255,255,0.8); padding: 12px 20px; display: block; text-decoration: none; border-left: 4px solid transparent; transition: all 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.1); color: white; border-left-color: #f39c12; }
        .sidebar a i { margin-right: 10px; width: 20px; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
        .big-number { font-size: 48px; font-weight: bold; }
        .bg-gradient-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .bg-gradient-2 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .bg-gradient-3 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .bg-gradient-4 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .bg-gradient-5 { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .bg-gradient-6 { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }
        .stat-box { color: white; border-radius: 15px; padding: 25px; text-align: center; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar p-0">
            <div class="logo text-center">
                <i class="bi bi-building" style="font-size: 40px;"></i>
                <h5 class="mt-2 mb-0">Hotel System</h5>
            </div>
            <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="hospedes.php"><i class="bi bi-people-fill"></i> Hóspedes</a>
            <a href="quartos.php"><i class="bi bi-door-closed-fill"></i> Quartos</a>
            <a href="categorias.php"><i class="bi bi-tags-fill"></i> Categorias</a>
            <a href="reservas.php"><i class="bi bi-calendar-check-fill"></i> Reservas</a>
            <a href="servicos.php"><i class="bi bi-gift-fill"></i> Serviços</a>
            <a href="relatorio.php" class="active"><i class="bi bi-bar-chart-fill"></i> Relatórios</a>
            <a href="logout.php" style="margin-top: 30px; color: #ff6b6b;"><i class="bi bi-box-arrow-right"></i> Sair</a>
        </div>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="bi bi-bar-chart-fill"></i> Relatório de Ocupação</h3>
                <a href="dashboard.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Voltar</a>
            </div>

            <!-- CARDS PRINCIPAIS -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stat-box bg-gradient-1">
                        <i class="bi bi-building" style="font-size: 40px;"></i>
                        <div class="big-number mt-2"><?= $totalQuartos ?></div>
                        <div>Total de Quartos</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box bg-gradient-4">
                        <i class="bi bi-door-open-fill" style="font-size: 40px;"></i>
                        <div class="big-number mt-2"><?= $livres ?></div>
                        <div>Quartos Livres</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box bg-gradient-2">
                        <i class="bi bi-door-closed-fill" style="font-size: 40px;"></i>
                        <div class="big-number mt-2"><?= $ocupados ?></div>
                        <div>Quartos Ocupados</div>
                    </div>
                </div>
            </div>

            <!-- TAXA DE OCUPAÇÃO -->
            <div class="card card-custom mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3"><i class="bi bi-graph-up-arrow"></i> Taxa de Ocupação</h5>
                    <div class="d-flex align-items-center mb-3">
                        <div class="big-number text-primary me-3"><?= $taxaOcupacao ?>%</div>
                        <div class="flex-grow-1">
                            <div class="progress" style="height: 25px; border-radius: 15px;">
                                <div class="progress-bar bg-gradient-4" style="width: <?= ($livres/$totalQuartos)*100 ?>%">Livres <?= $livres ?></div>
                                <div class="progress-bar bg-gradient-2" style="width: <?= ($ocupados/$totalQuartos)*100 ?>%">Ocupados <?= $ocupados ?></div>
                                <?php if ($manutencao > 0): ?>
                                    <div class="progress-bar bg-gradient-5" style="width: <?= ($manutencao/$totalQuartos)*100 ?>%">Manutenção <?= $manutencao ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- OUTRAS MÉTRICAS -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card card-custom">
                        <div class="card-body p-4 text-center">
                            <i class="bi bi-cash-stack text-success" style="font-size: 40px;"></i>
                            <h5 class="mt-2">Faturamento</h5>
                            <div class="big-number text-success">R$ <?= number_format($faturamento, 2, ',', '.') ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-custom">
                        <div class="card-body p-4 text-center">
                            <i class="bi bi-calendar-check-fill text-primary" style="font-size: 40px;"></i>
                            <h5 class="mt-2">Total de Reservas</h5>
                            <div class="big-number text-primary"><?= $totalReservas ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-custom">
                        <div class="card-body p-4 text-center">
                            <i class="bi bi-people-fill text-warning" style="font-size: 40px;"></i>
                            <h5 class="mt-2">Total de Hóspedes</h5>
                            <div class="big-number text-warning"><?= $totalHospedes ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DISTRIBUIÇÃO POR CATEGORIA -->
            <div class="card card-custom">
                <div class="card-body p-4">
                    <h5 class="mb-3"><i class="bi bi-pie-chart-fill"></i> Distribuição por Categoria</h5>
                    <?php foreach ($categorias as $c): 
                        $percent = $totalQuartos > 0 ? round(($c['total']/$totalQuartos)*100, 1) : 0;
                    ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <strong><?= $c['nome'] ?></strong>
                                <span><?= $c['total'] quartos (<?= $percent ?>%)</span>
                            </div>
                            <div class="progress" style="height: 20px; border-radius: 10px;">
                                <div class="progress-bar bg-gradient-1" style="width: <?= $percent ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>