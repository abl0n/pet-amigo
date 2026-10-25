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

$usuario_id = $_SESSION['usuario']['id'];
$nome       = trim($_POST['nome'] ?? '');
$email      = strtolower(trim($_POST['email'] ?? ''));
$telefone   = trim($_POST['telefone'] ?? '');
$endereco   = trim($_POST['endereco'] ?? '');

// Validações
if (!$nome || !$email || !$telefone || !$endereco) {
    $_SESSION['erro'] = "Preencha todos os campos obrigatórios.";
    header("Location: minha-conta.php");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['erro'] = "E-mail inválido.";
    header("Location: minha-conta.php");
    exit();
}

try {
    // Verificar se o e-mail já está em uso por outro usuário
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
    $stmt->execute([$email, $usuario_id]);
    if ($stmt->fetch()) {
        $_SESSION['erro'] = "Este e-mail já está sendo usado por outra conta.";
        header("Location: minha-conta.php");
        exit();
    }

    // Atualizar dados
    $stmt = $db->prepare("UPDATE usuarios SET nome = ?, email = ?, telefone = ?, endereco = ? WHERE id = ?");
    $stmt->execute([$nome, $email, $telefone, $endereco, $usuario_id]);

    // Atualizar sessão
    $_SESSION['usuario']['nome']     = $nome;
    $_SESSION['usuario']['email']    = $email;
    $_SESSION['usuario']['telefone'] = $telefone;
    $_SESSION['usuario']['endereco'] = $endereco;

    $_SESSION['sucesso'] = "Dados atualizados com sucesso!";
    header("Location: minha-conta.php");
    exit();

} catch (PDOException $e) {
    $_SESSION['erro'] = "Erro ao atualizar dados. Tente novamente.";
    header("Location: minha-conta.php");
    exit();
}