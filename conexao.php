<?php
$pdo = new PDO('mysql:host=localhost;dbname=hotel_db;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
session_start();

function verificarLogin() {
    if (!isset($_SESSION['usuario'])) {
        header("Location: index.php");
        exit;
    }
}

function uploadFoto($arquivo) {
    if ($arquivo['error'] !== UPLOAD_ERR_OK) return 'default.jpg';
    
    $pasta = 'uploads/';
    if (!is_dir($pasta)) mkdir($pasta, 0777, true);
    
    $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    $permitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($ext, $permitidos)) return 'default.jpg';
    
    $nome = uniqid() . '.' . $ext;
    $destino = $pasta . $nome;
    
    if (move_uploaded_file($arquivo['tmp_name'], $destino)) {
        return $nome;
    }
    return 'default.jpg';
}
?>