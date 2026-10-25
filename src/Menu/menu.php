<?php
session_start();
$base = '/pet-amigo-main'; 
require_once __DIR__ . '/../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare("SELECT * FROM animais WHERE disponivel = 1 ORDER BY data_cadastro DESC");
$stmt->execute();
$animais = $stmt->fetchAll();

$base = '../..';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nossos Pets - PetAmigo</title>
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
                <h3>Conheça Nossos Pets</h3>
                <p>Encontre seu companheiro perfeito entre nossos animais disponíveis para adoção</p>
                <?php if (!isset($_SESSION['usuario'])): ?>
                    <a href="../Login/login.php" class="btn" style="margin-top:1rem;">
                        <i class="fas fa-sign-in-alt"></i> Faça login para adotar
                    </a>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <section class="menu" id="menu">
        <h2 class="title">Todos os animais <span>Disponíveis</span></h2>

        <?php if (empty($animais)): ?>
            <div style="text-align:center;padding:3rem;color:#ccc;">
                <i class="fas fa-paw" style="font-size:3rem;margin-bottom:1rem;"></i>
                <h3>Nenhum animal disponível no momento</h3>
                <p>Volte em breve para conhecer nossos novos amigos!</p>
            </div>
        <?php else: ?>
            <div class="box-container">
                <?php foreach ($animais as $animal): ?>
                <div class="box">
                    <img src="<?= $base ?>/assets/img/<?= htmlspecialchars($animal['imagem']) ?>"
                         alt="<?= htmlspecialchars($animal['nome']) ?>"
                         onerror="this.src='<?= $base ?>/assets/img/animais/petPadrao.png'"
                    <h3><?= htmlspecialchars($animal['nome']) ?></h3>
                    <p class="description"><?= htmlspecialchars($animal['descricao']) ?></p>

                    <div class="animal-details">
                        <p><i class="fas fa-paw"></i> <strong>Raça:</strong> <?= htmlspecialchars($animal['raca']) ?></p>
                        <p><i class="fas fa-birthday-cake"></i> <strong>Idade:</strong> <?= htmlspecialchars($animal['idade']) ?> anos</p>
                        <p><i class="fas fa-expand"></i> <strong>Porte:</strong> <?= htmlspecialchars($animal['porte']) ?></p>
                        <p><i class="fas fa-venus-mars"></i> <strong>Sexo:</strong>
                            <?= ($animal['sexo'] ?? '') === 'M' ? 'Macho' : (($animal['sexo'] ?? '') === 'F' ? 'Fêmea' : 'Não informado') ?>
                        </p>

                        <?php if (!empty($animal['vacinado'])): ?>
                            <p><i class="fas fa-syringe" style="color:#2ecc71;"></i> <strong>Vacinado:</strong> Sim</p>
                        <?php endif; ?>

                        <?php if (!empty($animal['castrado'])): ?>
                            <p><i class="fas fa-heart" style="color:#e74c3c;"></i> <strong>Castrado:</strong> Sim</p>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($_SESSION['usuario'])): ?>
                        <a href="../Cadastro/pedido-adocao.php?animal_id=<?= $animal['id'] ?>" class="btn btn-adotar">
                            <i class="fas fa-heart"></i> Quero Adotar
                        </a>
                    <?php else: ?>
                        <a href="../Login/login.php" class="btn btn-login">
                            <i class="fas fa-sign-in-alt"></i> Faça login para adotar
                        </a>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>