<?php
session_start();
$base = '../..';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliações - PetAmigo</title>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/base.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/components.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/avaliacoes.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

    <?php include __DIR__ . '/../../includes/header.php'; ?>

    <div class="home-container">
        <section id="home">
            <div class="content">
                <h3>Avaliações dos Nossos Clientes</h3>
                <p>Veja o que dizem sobre suas experiências de adoção conosco</p>
            </div>
        </section>
    </div>

    <section class="reviews">
        <h2 class="title">Nossos <span>Depoimentos</span></h2>

        <div class="rating-summary">
            <div class="average-rating">4.8</div>
            <div class="total-reviews">Baseado em 127 avaliações</div>
            <div class="rating-stats">
                <div class="stat-item">
                    <div class="stat-number">98%</div>
                    <div class="stat-label">Recomendam</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">250+</div>
                    <div class="stat-label">Adoções</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">4.9</div>
                    <div class="stat-label">Suporte</div>
                </div>
            </div>
        </div>

        <div class="review-container">
            <div class="review-box">
                <div class="review-header">
                    <img class="review-avatar" src="<?= $base ?>/assets/img/dos.png" alt="Maria">
                    <div class="review-info">
                        <h4>Maria Silva</h4>
                        <div class="review-stars">★★★★★</div>
                        <div class="review-date">Há 2 semanas</div>
                    </div>
                </div>
                <p class="review-text">"Adotei meu Golden há 6 meses e foi a melhor decisão da minha vida. O processo foi muito transparente e a equipe super atenciosa!"</p>
            </div>

            <div class="review-box">
                <div class="review-header">
                    <img class="review-avatar" src="<?= $base ?>/assets/img/dos.png" alt="João">
                    <div class="review-info">
                        <h4>João Santos</h4>
                        <div class="review-stars">★★★★★</div>
                        <div class="review-date">Há 1 mês</div>
                    </div>
                </div>
                <p class="review-text">"Adotamos um Labrador e a experiência foi incrível. Todo o suporte pós-adoção fez toda a diferença na adaptação."</p>
            </div>

            <div class="review-box">
                <div class="review-header">
                    <img class="review-avatar" src="<?= $base ?>/assets/img/dos.png" alt="Ana">
                    <div class="review-info">
                        <h4>Ana Costa</h4>
                        <div class="review-stars">★★★★☆</div>
                        <div class="review-date">Há 2 meses</div>
                    </div>
                </div>
                <p class="review-text">"Processo muito organizado e humano. Sinto que fizemos parte de algo especial ao dar um lar para um animal necessitado."</p>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>