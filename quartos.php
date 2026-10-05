<?php
require_once 'conexao.php';
verificarLogin();

if ($_POST) {
    $foto = uploadFoto($_FILES['foto'] ?? ['error' => 1]);
    $stmt = $pdo->prepare("INSERT INTO quartos (numero, categoria_id, andar, capacidade, descricao, foto) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$_POST['numero'], $_POST['categoria_id'], $_POST['andar'], $_POST['capacidade'], $_POST['descricao'], $foto]);
    header("Location: quartos.php");
    exit;
}

if (isset($_GET['excluir'])) {
    $q = $pdo->prepare("SELECT foto FROM quartos WHERE id=?");
    $q->execute([$_GET['excluir']]);
    $foto = $q->fetchColumn();
    if ($foto && $foto != 'default.jpg' && file_exists('uploads/'.$foto)) {
        unlink('uploads/'.$foto);
    }
    $pdo->prepare("DELETE FROM quartos WHERE id=?")->execute([$_GET['excluir']]);
    header("Location: quartos.php");
    exit;
}

$quartos = $pdo->query("SELECT q.*, c.nome as categoria, c.preco FROM quartos q JOIN categorias c ON q.categoria_id=c.id ORDER BY q.numero")->fetchAll();
$categorias = $pdo->query("SELECT * FROM categorias")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Quartos</title>
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
        .btn-gradient:hover { color: white; transform: translateY(-2px); }
        
        .quarto-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            background: white;
        }
        .quarto-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
        .quarto-img {
            height: 200px;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 15px;
            border-radius: 20px;
            color: white;
            font-weight: 600;
            font-size: 12px;
        }
        .status-livre { background: #10b981; }
        .status-ocupado { background: #ef4444; }
        .status-manutencao { background: #f59e0b; }
        .quarto-body { padding: 20px; }
        .quarto-numero {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        .quarto-categoria {
            display: inline-block;
            padding: 3px 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            font-size: 12px;
        }
        .quarto-preco {
            font-size: 20px;
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
            <a href="quartos.php" class="active"><i class="bi bi-door-closed-fill"></i> Quartos</a>
            <a href="categorias.php"><i class="bi bi-tags-fill"></i> Categorias</a>
            <a href="reservas.php"><i class="bi bi-calendar-check-fill"></i> Reservas</a>
            <a href="servicos.php"><i class="bi bi-gift-fill"></i> Serviços</a>
            <a href="relatorio.php"><i class="bi bi-bar-chart-fill"></i> Relatórios</a>
            <a href="logout.php" style="margin-top: 30px; color: #ff6b6b;"><i class="bi bi-box-arrow-right"></i> Sair</a>
        </div>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="bi bi-door-closed-fill"></i> Quartos</h3>
                <a href="dashboard.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Voltar</a>
            </div>

            <!-- FORMULÁRIO -->
            <div class="card card-custom mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3"><i class="bi bi-plus-circle-fill"></i> Cadastrar Novo Quarto</h5>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Número *</label>
                                <input type="text" name="numero" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Categoria *</label>
                                <select name="categoria_id" class="form-control" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($categorias as $c): ?>
                                        <option value="<?= $c['id'] ?>"><?= $c['nome'] ?> - R$ <?= number_format($c['preco'], 2, ',', '.') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">Andar</label>
                                <input type="text" name="andar" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Capacidade</label>
                                <input type="number" name="capacidade" class="form-control" value="2">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-image"></i> Foto do Quarto</label>
                                <input type="file" name="foto" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Descrição</label>
                                <textarea name="descricao" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-gradient"><i class="bi bi-plus-circle"></i> Cadastrar Quarto</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- GRID DE QUARTOS COM FOTOS -->
            <div class="row g-4">
            <?php foreach ($quartos as $q): ?>
                <div class="col-md-4">
                    <div class="quarto-card">
                        <div class="quarto-img" style="background-image: url('uploads/<?= $q['foto'] ?: 'default.jpg' ?>')">
                            <span class="status-badge status-<?= $q['status'] ?>">
                                <?= strtoupper($q['status']) ?>
                            </span>
                        </div>
                        <div class="quarto-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <div class="quarto-numero">Quarto <?= $q['numero'] ?></div>
                                    <span class="quarto-categoria"><?= $q['categoria'] ?></span>
                                </div>
                                <div class="quarto-preco">R$ <?= number_format($q['preco'], 2, ',', '.') ?></div>
                            </div>
                            <p class="text-muted small mb-2">
                                <i class="bi bi-building"></i> Andar: <?= $q['andar'] ?: '-' ?> | 
                                <i class="bi bi-people"></i> Capacidade: <?= $q['capacidade'] ?>
                            </p>
                            <p class="small mb-3"><?= $q['descricao'] ?: 'Sem descrição' ?></p>
                            <a href="?excluir=<?= $q['id'] ?>" class="btn btn-sm btn-danger w-100" onclick="return confirm('Excluir este quarto?')">
                                <i class="bi bi-trash"></i> Excluir
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($quartos)): ?>
                <div class="col-12 text-center p-5 text-muted">Nenhum quarto cadastrado</div>
            <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>