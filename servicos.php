<?php
require_once 'conexao.php';
verificarLogin();

if ($_POST) {
    $stmt = $pdo->prepare("INSERT INTO servicos (nome, descricao, preco, icone) VALUES (?,?,?,?)");
    $stmt->execute([$_POST['nome'], $_POST['descricao'], $_POST['preco'], $_POST['icone']]);
    header("Location: servicos.php");
    exit;
}

if (isset($_GET['excluir'])) {
    $pdo->prepare("DELETE FROM servicos WHERE id=?")->execute([$_GET['excluir']]);
    header("Location: servicos.php");
    exit;
}

$servicos = $pdo->query("SELECT * FROM servicos")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Serviços</title>
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
        .btn-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 10px; }
        .btn-gradient:hover { color: white; }
        .servico-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s;
            text-align: center;
            border-top: 4px solid;
            border-image: linear-gradient(135deg, #667eea 0%, #764ba2 100%) 1;
        }
        .servico-card:hover { transform: translateY(-5px); }
        .servico-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin: 0 auto 15px;
        }
        .servico-preco {
            font-size: 24px;
            font-weight: bold;
            color: #10b981;
        }
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
            <a href="servicos.php" class="active"><i class="bi bi-gift-fill"></i> Serviços</a>
            <a href="relatorio.php"><i class="bi bi-bar-chart-fill"></i> Relatórios</a>
            <a href="logout.php" style="margin-top: 30px; color: #ff6b6b;"><i class="bi bi-box-arrow-right"></i> Sair</a>
        </div>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="bi bi-gift-fill"></i> Serviços Adicionais</h3>
                <a href="dashboard.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Voltar</a>
            </div>

            <div class="card card-custom mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3"><i class="bi bi-plus-circle-fill"></i> Novo Serviço</h5>
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Nome *</label>
                                <input type="text" name="nome" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Preço *</label>
                                <input type="number" step="0.01" name="preco" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Ícone</label>
                                <select name="icone" class="form-control">
                                    <option value="bi-gift">🎁 Presente</option>
                                    <option value="bi-cup-hot">☕ Café</option>
                                    <option value="bi-basket">🧺 Lavanderia</option>
                                    <option value="bi-snow">❄️ Frigobar</option>
                                    <option value="bi-car-front">🚗 Estacionamento</option>
                                    <option value="bi-heart">❤️ Spa</option>
                                    <option value="bi-wifi">📶 WiFi</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Descrição</label>
                                <input type="text" name="descricao" class="form-control">
                            </div>
                            <div class="col-12">
                                <button class="btn btn-gradient"><i class="bi bi-plus-circle"></i> Cadastrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-4">
            <?php foreach ($servicos as $s): ?>
                <div class="col-md-4">
                    <div class="servico-card">
                        <div class="servico-icon"><i class="bi <?= $s['icone'] ?>"></i></div>
                        <h5><?= $s['nome'] ?></h5>
                        <p class="text-muted small"><?= $s['descricao'] ?: 'Serviço adicional do hotel' ?></p>
                        <div class="servico-preco">R$ <?= number_format($s['preco'], 2, ',', '.') ?></div>
                        <div class="mt-3">
                            <a href="?excluir=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Excluir?')">
                                <i class="bi bi-trash"></i> Excluir
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>