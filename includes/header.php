<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$base = $base ?? '/pet-amigo-main'; // Garante o caminho base
?>
<header class="header">
    <section>
        <a href="<?= $base ?>/index.php" class="logo">
            <img src="<?= $base ?>/assets/img/petlogo.png" alt="logo PetAmigo">
        </a>

        <nav class="navbar main-menu">
            <a href="<?= $base ?>/src/Home/home.php"><i class="fas fa-home"></i> Home</a>
            <a href="<?= $base ?>/src/Sobre/sobre.php"><i class="fas fa-paw"></i> Sobre</a>
            <a href="<?= $base ?>/src/Menu/menu.php"><i class="fas fa-paw"></i> Menu</a>
            <a href="<?= $base ?>/src/Avaliacoes/avaliacoes.php"><i class="fas fa-star"></i> Avaliações</a>
        </nav>

        <nav class="navbar right-menu">
            <?php if (isset($_SESSION['usuario'])): ?>
                
                <!-- Se for admin, mostra o Painel Admin -->
                <?php if (isset($_SESSION['usuario']['tipo']) && $_SESSION['usuario']['tipo'] === 'admin'): ?>
                    <a href="<?= $base ?>/src/Admin/dashboard.php" style="color: #f1c40f;">
                        <i class="fas fa-user-shield"></i> Painel Admin
                    </a>
                <?php endif; ?>

                <a href="<?= $base ?>/src/Cadastro/meus-animais.php">
                    <i class="fas fa-paw"></i> Meus Animais
                </a>
                
                <a href="<?= $base ?>/src/Cadastro/minha-conta.php">
                    <i class="fas fa-user"></i> Minha Conta
                </a>

                <a href="<?= $base ?>/src/Login/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Sair (<?= htmlspecialchars(explode(' ', $_SESSION['usuario']['nome'])[0]) ?>)
                </a>
                
            <?php else: ?>
                <a href="<?= $base ?>/src/Cadastro/cadastrogeral.php">
                    <i class="fas fa-user-plus"></i> Cadastro
                </a>
                <a href="<?= $base ?>/src/Login/login.php">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            <?php endif; ?>
        </nav>

        <div class="icon">
            <img width="30" height="30"
                 src="https://img.icons8.com/ios-glyphs/30/ffffff/search--v1.png"
                 alt="buscar">
        </div>
    </section>
</header>