<?php
$base = '/pet-amigo-main'; // Define o caminho base
require_once __DIR__ . '/../../includes/admin_auth.php'; // O SEGURANÇA
require_once __DIR__ . '/../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Puxar estatísticas para o painel
$total_usuarios = $db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$total_animais  = $db->query("SELECT COUNT(*) FROM animais")->fetchColumn();
$pedidos_pendentes = $db->query("SELECT COUNT(*) FROM adocoes WHERE status = 'pendente'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo - PetAmigo</title>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/base.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/components.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/cadastro.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/../../includes/header.php'; ?>

    <div class="home-container">
        <section id="home">
            <div class="content">
                <h3>Painel <span>Administrativo</span></h3>
                <p>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario']['nome']) ?>. Gerencie o PetAmigo.</p>
            </div>
        </section>
    </div>

    <div class="cadastro-container" style="max-width:1000px;">
        
        <!-- Cards de Resumo -->
        <div class="form-section" style="display:flex; gap:1rem; flex-wrap:wrap; text-align:center; margin-bottom:2rem;">
            <div style="flex:1; background:rgba(52,152,219,0.1); padding:1.5rem; border-radius:8px;">
                <h2><?= $total_usuarios ?></h2>
                <p>Usuários Cadastrados</p>
                <a href="usuarios.php" class="btn btn-secondary" style="margin-top:1rem; font-size:0.9rem;">Gerenciar</a>
            </div>
            
            <div style="flex:1; background:rgba(46,204,113,0.1); padding:1.5rem; border-radius:8px;">
                <h2><?= $total_animais ?></h2>
                <p>Animais no Sistema</p>
                <a href="animais.php" class="btn btn-secondary" style="margin-top:1rem; font-size:0.9rem;">Gerenciar</a>
            </div>

            <div style="flex:1; background:rgba(241,196,15,0.1); padding:1.5rem; border-radius:8px;">
                <h2><?= $pedidos_pendentes ?></h2>
                <p>Pedidos Pendentes</p>
                <a href="adocoes.php" class="btn btn-secondary" style="margin-top:1rem; font-size:0.9rem;">Aprovar/Rejeitar</a>
            </div>
        </div>

    </div>

    <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>