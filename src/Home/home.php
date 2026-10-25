<?php
session_start();
$base = '/pet-amigo-main';
$conta_excluida = isset($_GET['conta_excluida']);
require_once __DIR__ . '/../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Buscar 6 animais disponíveis
$stmt = $db->prepare("SELECT * FROM animais WHERE disponivel = 1 ORDER BY data_cadastro DESC LIMIT 6");
$stmt->execute();
$animais = $stmt->fetchAll();


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetAmigo - Adote um Amigo</title>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/base.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/components.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/home.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

    <?php include __DIR__ . '/../../includes/header.php'; ?>

    <div class="home-container">
        <section id="home">
            <div class="content">
                <h3>Adote Um Amigo de Quatro Patas</h3>
                <p>Transforme a vida de um pet e ganhe um companheiro leal.
                Adotar é um ato de amor que muda histórias.</p>
                <a href="../Menu/menu.php" class="btn">Conheça nossos pets</a>
            </div>

            <?php if ($conta_excluida): ?>
                <div class="message success" style="max-width:600px;margin:1rem auto;">
                    Sua conta foi excluída com sucesso. Esperamos te ver novamente!
                </div>
            <?php endif; ?>
        </section>
    </div>

    <section class="about" id="about">
        <h2 class="title">Sobre <span>Nós</span></h2>
        <div class="row">
            <div class="container-image">
                <img src="<?= $base ?>/assets/img/cachorrobig.png" alt="sobre-nos">
            </div>
            <div class="content">
                <h3>Nossos Melhores Amigos</h3>
                <p>No nosso espaço, cada animal é tratado como parte da família. Cuidamos com carinho, atenção e dedicação, buscando sempre oferecer um lar cheio de amor e segurança.</p>
                <p>Acreditamos que adotar é mais do que um gesto de compaixão: é transformar vidas. Tanto a vida do pet quanto a do humano que o acolhe se enchem de alegria, aprendizado e cumplicidade.</p>
                <p>Nosso objetivo é conectar pessoas e animais, criando histórias felizes e lares cheios de amor. Aqui, cada patinha importa e cada olhar é valorizado.</p>
                <a href="../Sobre/sobre.php" class="btn">Saiba mais</a>
            </div>
        </div>
    </section>

    <section class="menu" id="menu">
        <h2 class="title">Nossos animais <span>Disponíveis</span></h2>

        <?php if (empty($animais)): ?>
            <div style="text-align:center;padding:3rem;color:#ccc;">
                <i class="fas fa-paw" style="font-size:3rem;margin-bottom:1rem;"></i>
                <h3>Nenhum animal disponível no momento</h3>
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
                    <p><strong>Raça:</strong> <?= htmlspecialchars($animal['raca']) ?></p>
                    <p><strong>Idade:</strong> <?= htmlspecialchars($animal['idade']) ?> anos</p>
                    <?php if (isset($_SESSION['usuario'])): ?>
                        <a href="../Cadastro/pedido-adocao.php?animal_id=<?= $animal['id'] ?>" class="btn">Quero adotar</a>
                    <?php else: ?>
                        <a href="../Login/login.php" class="btn">Faça login para adotar</a>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>