<?php
require_once 'conexao.php';
verificarLogin();

if ($_POST) {
    // Calcula valor total
    $q = $pdo->prepare("SELECT c.preco FROM quartos q JOIN categorias c ON q.categoria_id=c.id WHERE q.id=?");
    $q->execute([$_POST['quarto_id']]);
    $precoDiaria = $q->fetchColumn();
    $dias = (strtotime($_POST['data_saida']) - strtotime($_POST['data_entrada'])) / 86400;
    $valorTotal = $precoDiaria * max(1, $dias);
    
    $stmt = $pdo->prepare("INSERT INTO reservas (hospede_id, quarto_id, data_entrada, data_saida, valor_total, observacoes) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$_POST['hospede_id'], $_POST['quarto_id'], $_POST['data_entrada'], $_POST['data_saida'], $valorTotal, $_POST['observacoes']]);
    header("Location: reservas.php");
    exit;
}

if (isset($_GET['checkin'])) {
    $r = $pdo->prepare("SELECT quarto_id FROM reservas WHERE id=?");
    $r->execute([$_GET['checkin']]);
    $quarto_id = $r->fetchColumn();
    $pdo->prepare("UPDATE reservas SET status='checkin' WHERE id=?")->execute([$_GET['checkin']]);
    $pdo->prepare("UPDATE quartos SET status='ocupado' WHERE id=?")->execute([$quarto_id]);
    header("Location: reservas.php");
    exit;
}

if (isset($_GET['checkout'])) {
    $r = $pdo->prepare("SELECT quarto_id FROM reservas WHERE id=?");
    $r->execute([$_GET['checkout']]);
    $quarto_id = $r->fetchColumn();
    $pdo->prepare("UPDATE reservas SET status='checkout' WHERE id=?")->execute([$_GET['checkout']]);
    $pdo->prepare("UPDATE quartos SET status='livre' WHERE id=?")->execute([$quarto_id]);
    header("Location: reservas.php");
    exit;
}

if (isset($_GET['cancelar'])) {
    $r = $pdo->prepare("SELECT quarto_id FROM reservas WHERE id=?");
    $r->execute([$_GET['cancelar']]);
    $quarto_id = $r->fetchColumn();
    $pdo->prepare("UPDATE reservas SET status='cancelada' WHERE id=?")->execute([$_GET['cancelar']]);
    $pdo->prepare("UPDATE quartos SET status='livre' WHERE id=?")->execute([$quarto_id]);
    header("Location: reservas.php");
    exit;
}

$reservas = $pdo->query("SELECT r.*, h.nome as hospede, q.numero as quarto, c.nome as categoria 
                         FROM reservas r 
                         JOIN hospedes h ON r.hospede_id=h.id 
                         JOIN quartos q ON r.quarto_id=q.id 
                         JOIN categorias c ON q.categoria_id=c.id 
                         ORDER BY r.data_criacao DESC")->fetchAll();
$hospedes = $pdo->query("SELECT * FROM hospedes")->fetchAll();
$quartosLivres = $pdo->query("SELECT q.*, c.nome as categoria, c.preco FROM quartos q JOIN categorias c ON q.categoria_id=c.id WHERE q.status='livre'")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Reservas</title>
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
        .table-custom { border-radius: 15px; overflow: hidden; }
        .table-custom thead { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 10px; }
        .btn-gradient:hover { color: white; }
        .badge-status { padding: 5px 12px; border-radius: 15px; font-weight: 600; font-size: 12px; }
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
            <a href="reservas.php" class="active"><i class="bi bi-calendar-check-fill"></i> Reservas</a>
            <a href="servicos.php"><i class="bi bi-gift-fill"></i> Serviços</a>
            <a href="relatorio.php"><i class="bi bi-bar-chart-fill"></i> Relatórios</a>
            <a href="logout.php" style="margin-top: 30px; color: #ff6b6b;"><i class="bi bi-box-arrow-right"></i> Sair</a>
        </div>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="bi bi-calendar-check-fill"></i> Reservas</h3>
                <a href="dashboard.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Voltar</a>
            </div>

            <div class="card card-custom mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3"><i class="bi bi-calendar-plus"></i> Nova Reserva</h5>
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Hóspede *</label>
                                <select name="hospede_id" class="form-control" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($hospedes as $h): ?>
                                        <option value="<?= $h['id'] ?>"><?= $h['nome'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Quarto Livre *</label>
                                <select name="quarto_id" class="form-control" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($quartosLivres as $q): ?>
                                        <option value="<?= $q['id'] ?>"><?= $q['numero'] ?> - <?= $q['categoria'] ?> (R$ <?= number_format($q['preco'], 2, ',', '.') ?>/dia)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Entrada *</label>
                                <input type="date" name="data_entrada" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Saída *</label>
                                <input type="date" name="data_saida" class="form-control" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-gradient w-100"><i class="bi bi-plus-circle"></i> Reservar</button>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Observações</label>
                                <textarea name="observacoes" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-custom">
                <div class="card-body p-0">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th class="p-3">Hóspede</th>
                                <th class="p-3">Quarto</th>
                                <th class="p-3">Categoria</th>
                                <th class="p-3">Entrada</th>
                                <th class="p-3">Saída</th>
                                <th class="p-3">Valor</th>
                                <th class="p-3">Status</th>
                                <th class="p-3 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($reservas as $r): 
                            $statusClass = [
                                'reservada' => 'bg-warning text-dark',
                                'checkin' => 'bg-success',
                                'checkout' => 'bg-secondary',
                                'cancelada' => 'bg-danger'
                            ];
                        ?>
                            <tr>
                                <td class="p-3"><strong><?= $r['hospede'] ?></strong></td>
                                <td class="p-3"><?= $r['quarto'] ?></td>
                                <td class="p-3"><?= $r['categoria'] ?></td>
                                <td class="p-3"><?= date('d/m/Y', strtotime($r['data_entrada'])) ?></td>
                                <td class="p-3"><?= date('d/m/Y', strtotime($r['data_saida'])) ?></td>
                                <td class="p-3"><strong>R$ <?= number_format($r['valor_total'], 2, ',', '.') ?></strong></td>
                                <td class="p-3"><span class="badge badge-status <?= $statusClass[$r['status']] ?>"><?= strtoupper($r['status']) ?></span></td>
                                <td class="p-3 text-center">
                                    <?php if ($r['status'] == 'reservada'): ?>
                                        <a href="?checkin=<?= $r['id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Realizar check-in?')">
                                            <i class="bi bi-box-arrow-in-right"></i> Check-in
                                        </a>
                                        <a href="?cancelar=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Cancelar reserva?')">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    <?php elseif ($r['status'] == 'checkin'): ?>
                                        <a href="?checkout=<?= $r['id'] ?>" class="btn btn-sm btn-warning" onclick="return confirm('Realizar check-out?')">
                                            <i class="bi bi-box-arrow-left"></i> Check-out
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($reservas)): ?>
                            <tr><td colspan="8" class="text-center p-4 text-muted">Nenhuma reserva cadastrada</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>