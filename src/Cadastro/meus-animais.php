<?php
session_start();
$base = '/pet-amigo-main';
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../Login/login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare("SELECT * FROM doadores WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario']['id']]);
$doador = $stmt->fetch();

$animais = [];
if ($doador) {
    $stmt = $db->prepare("SELECT * FROM animais WHERE doador_id = ? ORDER BY data_cadastro DESC");
    $stmt->execute([$doador['id']]);
    $animais = $stmt->fetchAll();
}

$sucesso = $_SESSION['sucesso'] ?? '';
$erro = $_SESSION['erro'] ?? '';
unset($_SESSION['sucesso'], $_SESSION['erro']);


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Animais - PetAmigo</title>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/base.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/components.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/menu.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

    <?php include __DIR__ . '/../../includes/header.php'; ?>

    <div class="home-container">
        <section id="home">
            <div class="content">
            <h3>Meus Animais Cadastrados</h3>
            <p>Gerencie os animais que você cadastrou para adoção</p>

        </div>
        </section>
    </div>

    <section class="menu" id="menu">
        <?php if ($sucesso): ?>
            <div class="message success"><?= htmlspecialchars($sucesso) ?></div>
        <?php endif; ?>
        <?php if ($erro): ?>
            <div class="message error"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <div style="text-align:center;margin-bottom:2rem;">
            <?php if ($doador): ?>
                <a href="cadastro-animal.php" class="btn"><i class="fas fa-plus"></i> Cadastrar Novo Animal</a>
            <?php else: ?>
                <a href="cadastro-doador.php" class="btn"><i class="fas fa-heart"></i> Torne-se um Doador</a>
            <?php endif; ?>
        </div>

        <?php if (empty($animais) && $doador): ?>
            <div style="text-align:center;padding:3rem;color:#ccc;">
                <i class="fas fa-paw" style="font-size:3rem;margin-bottom:1rem;"></i>
                <h3>Nenhum animal cadastrado</h3>
                <p>Comece cadastrando seu primeiro animal para adoção!</p>
                <a href="cadastro-animal.php" class="btn">Cadastrar Primeiro Animal</a>
            </div>
        <?php elseif (empty($animais)): ?>
            <div style="text-align:center;padding:3rem;color:#ccc;">
                <i class="fas fa-heart" style="font-size:3rem;margin-bottom:1rem;"></i>
                <h3>Você ainda não é um doador</h3>
                <p>Torne-se um doador para cadastrar animais!</p>
                <a href="cadastro-doador.php" class="btn">Tornar-se Doador</a>
            </div>
        <?php else: ?>
            <div class="box-container">
                <?php foreach ($animais as $animal): ?>
                <div class="box">
                    <img src="<?= $base ?>/assets/img/<?= htmlspecialchars($animal['imagem']) ?>"
                         alt="<?= htmlspecialchars($animal['nome']) ?>"
                         onerror="this.src='<?= $base ?>/assets/img/animais/petPadrao.png'">
                    <h3><?= htmlspecialchars($animal['nome']) ?></h3>
                    <p class="description"><?= htmlspecialchars($animal['descricao']) ?></p>
                    <p><strong>Raça:</strong> <?= htmlspecialchars($animal['raca']) ?></p>
                    <p><strong>Idade:</strong> <?= htmlspecialchars($animal['idade']) ?> anos</p>
                    <p><strong>Porte:</strong> <?= htmlspecialchars($animal['porte']) ?></p>
                    <p><strong>Status:</strong>
                        <span style="color: <?= $animal['disponivel'] ? '#2ecc71' : '#e74c3c' ?>">
                            <?= $animal['disponivel'] ? 'Disponível' : 'Adotado' ?>
                        </span>
                    </p>

                    <div class="animal-actions">
                        <a href="editar-animal.php?id=<?= $animal['id'] ?>" class="btn-action btn-edit">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="excluir-animal.php?id=<?= $animal['id'] ?>" class="btn-action btn-delete">
                            <i class="fas fa-trash"></i> Excluir
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>