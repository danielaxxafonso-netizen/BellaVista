<?php
require_once 'conexao.php';
verificarLogin();

$totalQuartos = $pdo->query("SELECT COUNT(*) FROM quartos")->fetchColumn();
$ocupados = $pdo->query("SELECT COUNT(*) FROM quartos WHERE status='ocupado'")->fetchColumn();
$livres = $pdo->query("SELECT COUNT(*) FROM quartos WHERE status='livre'")->fetchColumn();
$totalHospedes = $pdo->query("SELECT COUNT(*) FROM hospedes")->fetchColumn();
$reservasAtivas = $pdo->query("SELECT COUNT(*) FROM reservas WHERE status IN ('reservada','checkin')")->fetchColumn();
$faturamento = $pdo->query("SELECT COALESCE(SUM(valor_total),0) FROM reservas WHERE status='checkin'")->fetchColumn();
$taxaOcupacao = $totalQuartos > 0 ? round(($ocupados/$totalQuartos)*100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f5f7fa; font-family: 'Segoe UI', sans-serif; }
        .sidebar {
            background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%);
            min-height: 100vh;
            color: white;
        }
        .sidebar .logo { padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar a {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            display: block;
            text-decoration: none;
            border-left: 4px solid transparent;
            transition: all 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #f39c12;
        }
        .sidebar a i { margin-right: 10px; width: 20px; }
        .stat-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s;
            overflow: hidden;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
        }
        .bg-gradient-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .bg-gradient-2 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .bg-gradient-3 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .bg-gradient-4 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .bg-gradient-5 { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .bg-gradient-6 { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }
        .top-bar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-radius: 15px;
            margin-bottom: 25px;
        }
        .welcome-text h3 { margin: 0; color: #2c3e50; }
        .welcome-text small { color: #7f8c8d; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-2 sidebar p-0">
            <div class="logo text-center">
                <i class="bi bi-building" style="font-size: 40px;"></i>
                <h5 class="mt-2 mb-0">Hotel System</h5>
            </div>
            <a href="dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="hospedes.php"><i class="bi bi-people-fill"></i> Hóspedes</a>
            <a href="quartos.php"><i class="bi bi-door-closed-fill"></i> Quartos</a>
            <a href="categorias.php"><i class="bi bi-tags-fill"></i> Categorias</a>
            <a href="reservas.php"><i class="bi bi-calendar-check-fill"></i> Reservas</a>
            <a href="servicos.php"><i class="bi bi-gift-fill"></i> Serviços</a>
            <a href="relatorio.php"><i class="bi bi-bar-chart-fill"></i> Relatórios</a>
            <a href="logout.php" style="margin-top: 30px; color: #ff6b6b;"><i class="bi bi-box-arrow-right"></i> Sair</a>
        </div>

        <!-- CONTEÚDO -->
        <div class="col-md-10 p-4">
            <div class="top-bar d-flex justify-content-between align-items-center">
                <div class="welcome-text">
                    <h3><i class="bi bi-speedometer2"></i> Dashboard</h3>
                    <small>Bem-vindo ao sistema de gestão hoteleira</small>
                </div>
                <div>
                    <span class="badge bg-gradient-1 text-white px-3 py-2">
                        <i class="bi bi-person-circle"></i> <?= $_SESSION['usuario'] ?>
                    </span>
                </div>
            </div>

            <!-- CARDS ESTATÍSTICAS -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body d-flex justify-content-between align-items-center p-4">
                            <div>
                                <small class="text-muted">Total de Hóspedes</small>
                                <h2 class="mb-0"><?= $totalHospedes ?></h2>
                            </div>
                            <div class="icon bg-gradient-1"><i class="bi bi-people-fill"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body d-flex justify-content-between align-items-center p-4">
                            <div>
                                <small class="text-muted">Quartos Livres</small>
                                <h2 class="mb-0"><?= $livres ?></h2>
                            </div>
                            <div class="icon bg-gradient-4"><i class="bi bi-door-open-fill"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body d-flex justify-content-between align-items-center p-4">
                            <div>
                                <small class="text-muted">Quartos Ocupados</small>
                                <h2 class="mb-0"><?= $ocupados ?></h2>
                            </div>
                            <div class="icon bg-gradient-2"><i class="bi bi-door-closed-fill"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body d-flex justify-content-between align-items-center p-4">
                            <div>
                                <small class="text-muted">Reservas Ativas</small>
                                <h2 class="mb-0"><?= $reservasAtivas ?></h2>
                            </div>
                            <div class="icon bg-gradient-3"><i class="bi bi-calendar-check-fill"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body d-flex justify-content-between align-items-center p-4">
                            <div>
                                <small class="text-muted">Faturamento</small>
                                <h2 class="mb-0">R$ <?= number_format($faturamento, 2, ',', '.') ?></h2>
                            </div>
                            <div class="icon bg-gradient-5"><i class="bi bi-cash-stack"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card">
                        <div class="card-body d-flex justify-content-between align-items-center p-4">
                            <div>
                                <small class="text-muted">Taxa de Ocupação</small>
                                <h2 class="mb-0"><?= $taxaOcupacao ?>%</h2>
                            </div>
                            <div class="icon bg-gradient-6"><i class="bi bi-graph-up"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BARRA DE OCUPAÇÃO -->
            <div class="card stat-card">
                <div class="card-body p-4">
                    <h5 class="mb-3"><i class="bi bi-graph-up-arrow"></i> Ocupação Geral do Hotel</h5>
                    <div class="progress" style="height: 30px; border-radius: 15px;">
                        <div class="progress-bar bg-gradient-4" style="width: <?= ($livres/$totalQuartos)*100 ?>%">
                            Livres: <?= $livres ?>
                        </div>
                        <div class="progress-bar bg-gradient-2" style="width: <?= ($ocupados/$totalQuartos)*100 ?>%">
                            Ocupados: <?= $ocupados ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>