<?php
require_once 'conexao.php';

if ($_POST) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario=? AND senha=?");
    $stmt->execute([$_POST['usuario'], $_POST['senha']]);
    if ($stmt->fetch()) {
        $_SESSION['usuario'] = $_POST['usuario'];
        header("Location: dashboard.php");
        exit;
    }
    $erro = "Usuário ou senha inválidos";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            border: none;
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .login-header i { font-size: 60px; margin-bottom: 15px; }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn-login:hover { transform: translateY(-2px); color: white; }
        .form-control { border-radius: 10px; padding: 12px 15px; border: 2px solid #e0e0e0; }
        .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,0.2); }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card login-card">
                <div class="login-header">
                    <i class="bi bi-building"></i>
                    <h2 class="mb-0">Hotel System</h2>
                    <small>Sistema de Gestão Hoteleira</small>
                </div>
                <div class="card-body p-5">
                    <?php if (isset($erro)): ?>
                        <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= $erro ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="bi bi-person"></i> Usuário</label>
                            <input type="text" name="usuario" class="form-control" placeholder="Digite seu usuário" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="bi bi-lock"></i> Senha</label>
                            <input type="password" name="senha" class="form-control" placeholder="Digite sua senha" required>
                        </div>
                        <button type="submit" class="btn btn-login w-100">
                            <i class="bi bi-box-arrow-in-right"></i> Entrar
                        </button>
                    </form>
                    <div class="text-center mt-4">
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> Usuário: <strong>admin</strong> | Senha: <strong>admin123</strong>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>