<?php
session_start();
$base = '/pet-amigo-main'; 
require_once __DIR__ . '/../../config/database.php';

$erro = '';
$sucesso = $_SESSION['sucesso'] ?? '';
unset($_SESSION['sucesso']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email && $senha) {
        $database = new Database();
        $db = $database->getConnection();

        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario'] = [
                    'id'       => $usuario['id'],
                    'nome'     => $usuario['nome'],
                    'email'    => $usuario['email'],
                    'telefone' => $usuario['telefone'],
                    'endereco' => $usuario['endereco'],
                    'tipo'     => $usuario['tipo'] // <-- ADICIONE ESTA LINHA
                ];
            header("Location: ../Home/home.php");
            exit();
        } else {
            $erro = "E-mail ou senha incorretos.";
        }
    } else {
        $erro = "Preencha todos os campos.";
    }
}

$base = '../..';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PetAmigo</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/base.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/components.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/login.css">
</head>
<body>
    <?php include __DIR__ . '/../../includes/header.php'; ?>

    <main class="container">
        <form method="POST">
            <h1>Login PetAmigo</h1>

            <?php if ($erro): ?>
                <div class="message error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>
            <?php if ($sucesso): ?>
                <div class="message success"><?= htmlspecialchars($sucesso) ?></div>
            <?php endif; ?>

            <div class="input-box">
                <input placeholder="E-mail" type="email" name="email" required autocomplete="email">
                <i class="bx bxs-user"></i>
            </div>

            <div class="input-box">
                <input placeholder="Senha" type="password" name="senha" required autocomplete="current-password">
                <i class="bx bxs-lock-alt"></i>
            </div>

            <div class="remember-forgot">
                <label><input type="checkbox"> Lembrar senha</label>
                <a href="#">Esqueci a senha</a>
            </div>

            <button type="submit" class="login">Login</button>

            <div class="register-link">
                <p>Não tem uma conta? <a href="../Cadastro/cadastrogeral.php">Cadastre-se</a></p>
            </div>
        </form>
    </main>
</body>
</html>