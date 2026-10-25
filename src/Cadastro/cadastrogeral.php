<?php
session_start();
$base = '/pet-amigo-main';
require_once __DIR__ . '/../../config/database.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    $nome     = trim($_POST['nome']);
    $email    = trim($_POST['email']);
    $senha    = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $telefone = trim($_POST['telefone']);
    $endereco = trim($_POST['endereco']);

    try {
        $stmt = $db->prepare("INSERT INTO usuarios (nome, email, senha, telefone, endereco) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $senha, $telefone, $endereco]);
        $_SESSION['sucesso'] = "Cadastro realizado com sucesso! Faça login para continuar.";
        header("Location: ../Login/login.php");
        exit();
    } catch (PDOException $e) {
        $erro = str_contains($e->getMessage(), 'Duplicate')
              ? "Este e-mail já está cadastrado."
              : "Erro ao cadastrar. Tente novamente.";
    }
}


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - PetAmigo</title>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/base.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/components.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/cadastro.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

    <?php include __DIR__ . '/../../includes/header.php'; ?>

    <div class="cadastro-container">
        <div class="cadastro-header">
            <h2>Cadastro <span>PetAmigo</span></h2>
            <p>Crie sua conta para começar a adotar ou doar</p>
        </div>

        <?php if ($erro): ?>
            <div class="message error"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <div class="form-section">
            <form method="POST" class="cadastro-form">
                <div class="form-group">
                    <label for="nome">Nome Completo *</label>
                    <input type="text" id="nome" name="nome" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">E-mail *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha *</label>
                        <input type="password" id="senha" name="senha" minlength="6" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="telefone">Telefone *</label>
                    <input type="tel" id="telefone" name="telefone" required>
                </div>

                <div class="form-group">
                    <label for="endereco">Endereço Completo *</label>
                    <textarea id="endereco" name="endereco" rows="3" required></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Cadastrar</button>
                </div>
            </form>
        </div>

        <p style="text-align:center;margin-top:1rem;color:#ccc;">
            Já tem cadastro? <a href="../Login/login.php" style="color:#3498db;">Faça login aqui</a>
        </p>
    </div>

    <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>