<?php
require_once 'conexao.php';
verificarLogin();

if ($_POST) {
    $stmt = $pdo->prepare("INSERT INTO hospedes (nome, cpf, telefone, email, cidade) VALUES (?,?,?,?,?)");
    $stmt->execute([$_POST['nome'], $_POST['cpf'], $_POST['telefone'], $_POST['email'], $_POST['cidade']]);
    header("Location: hospedes.php");
    exit;
}

if (isset($_GET['excluir'])) {
    $pdo->prepare("DELETE FROM hospedes WHERE id=?")->execute([$_GET['excluir']]);
    header("Location: hospedes.php");
    exit;
}

$hospedes = $pdo->query("SELECT * FROM hospedes ORDER BY data_cadastro DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Hóspedes</title>
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
        .btn-gradient:hover { color: white; transform: translateY(-2px); }
        .avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: inline-flex; align-items: center; justify-content: center; font-weight: bold; }
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
            <a href="hospedes.php" class="active"><i class="bi bi-people-fill"></i> Hóspedes</a>
            <a href="quartos.php"><i class="bi bi-door-closed-fill"></i> Quartos</a>
            <a href="categorias.php"><i class="bi bi-tags-fill"></i> Categorias</a>
            <a href="reservas.php"><i class="bi bi-calendar-check-fill"></i> Reservas</a>
            <a href="servicos.php"><i class="bi bi-gift-fill"></i> Serviços</a>
            <a href="relatorio.php"><i class="bi bi-bar-chart-fill"></i> Relatórios</a>
            <a href="logout.php" style="margin-top: 30px; color: #ff6b6b;"><i class="bi bi-box-arrow-right"></i> Sair</a>
        </div>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="bi bi-people-fill"></i> Hóspedes</h3>
                <a href="dashboard.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Voltar</a>
            </div>

            <!-- FORMULÁRIO -->
            <div class="card card-custom mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3"><i class="bi bi-person-plus-fill"></i> Cadastrar Novo Hóspede</h5>
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Nome *</label>
                                <input type="text" name="nome" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">CPF *</label>
                                <input type="text" name="cpf" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Telefone</label>
                                <input type="text" name="telefone" class="form-control">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-gradient w-100"><i class="bi bi-plus-circle"></i> Adicionar</button>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Cidade</label>
                                <input type="text" name="cidade" class="form-control">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- LISTA -->
            <div class="card card-custom">
                <div class="card-body p-0">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th class="p-3">Hóspede</th>
                                <th class="p-3">CPF</th>
                                <th class="p-3">Telefone</th>
                                <th class="p-3">Email</th>
                                <th class="p-3">Cidade</th>
                                <th class="p-3 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($hospedes as $h): 
                            $iniciais = strtoupper(substr($h['nome'], 0, 1));
                        ?>
                            <tr>
                                <td class="p-3">
                                    <span class="avatar me-2"><?= $iniciais ?></span>
                                    <strong><?= $h['nome'] ?></strong>
                                </td>
                                <td class="p-3"><?= $h['cpf'] ?></td>
                                <td class="p-3"><?= $h['telefone'] ?></td>
                                <td class="p-3"><?= $h['email'] ?></td>
                                <td class="p-3"><?= $h['cidade'] ?></td>
                                <td class="p-3 text-center">
                                    <a href="?excluir=<?= $h['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Excluir este hóspede?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($hospedes)): ?>
                            <tr><td colspan="6" class="text-center p-4 text-muted">Nenhum hóspede cadastrado</td></tr>
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