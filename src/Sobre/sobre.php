<?php
session_start();
$base = '../..';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nós - PetAmigo</title>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/base.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/components.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/sobre.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

    <?php include __DIR__ . '/../../includes/header.php'; ?>

    <div class="home-container">
        <section id="home">
            <div class="content">
                <h3>Sobre o PetAmigo</h3>
                <p>Conheça nossa história e missão de conectar pets a lares cheios de amor</p>
            </div>
        </section>
    </div>

    <section class="about" id="about" style="margin-top: 2rem;">
        <div class="row">
            <div class="content">
                <h3>Nossa História</h3>
                <p>O PetAmigo nasceu do amor incondicional pelos animais e da vontade de fazer a diferença na vida de pets abandonados. Desde 2020, trabalhamos incansavelmente para resgatar, cuidar e encontrar lares amorosos para animais em situação de vulnerabilidade.</p>
                <p>Nossa equipe é composta por veterinários, voluntários e amantes de animais que dedicam suas vidas ao bem-estar dos nossos amigos de quatro patas.</p>

                <h3 style="margin-top: 2rem;">Nossa Missão</h3>
                <p>Promover a adoção responsável e consciente, educando a sociedade sobre a importância do cuidado com os animais e combatendo o abandono e maus-tratos.</p>

                <h3 style="margin-top: 2rem;">Nossos Valores</h3>
                <p>• Amor e respeito por todos os animais</p>
                <p>• Compromisso com o bem-estar animal</p>
                <p>• Transparência em todas as ações</p>
                <p>• Educação para adoção responsável</p>
            </div>
            <div class="container-image">
                <img src="<?= $base ?>/assets/img/time.png" alt="sobre-nos">
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>