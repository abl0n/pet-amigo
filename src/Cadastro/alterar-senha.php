<?php
session_start();
$base = '/pet-amigo-main';
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../Login/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: minha-conta.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

$usuario_id     = $_SESSION['usuario']['id'];
$senha_atual    = $_POST['senha_atual'] ?? '';
$nova_senha     = $_POST['nova_senha'] ?? '';
$confirmar      = $_POST['confirmar_senha'] ?? '';

// Validações
if (!$senha_atual || !$nova_senha || !$confirmar) {
    $_SESSION['erro'] = "Preencha todos os campos de senha.";
    header("Location: minha-conta.php");
    exit();
}

if (strlen($nova_senha) < 6) {
    $_SESSION['erro'] = "A nova senha deve ter pelo menos 6 caracteres.";
    header("Location: minha-conta.php");
    exit();
}

if ($nova_senha !== $confirmar) {
    $_SESSION['erro'] = "A nova senha e a confirmação não coincidem.";
    header("Location: minha-conta.php");
    exit();
}

// Verificar senha atual
$stmt = $db->prepare("SELECT senha FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch();

if (!$usuario || !password_verify($senha_atual, $usuario['senha'])) {
    $_SESSION['erro'] = "Senha atual incorreta.";
    header("Location: minha-conta.php");
    exit();
}

// Gerar novo hash e atualizar
$novo_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
$stmt = $db->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
$stmt->execute([$novo_hash, $usuario_id]);

$_SESSION['sucesso'] = "Senha alterada com sucesso!";
header("Location: minha-conta.php");
exit();