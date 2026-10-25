<?php
$base = '/pet-amigo-main';
require_once __DIR__ . '/../../includes/admin_auth.php';
require_once __DIR__ . '/../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$sucesso = $_SESSION['sucesso'] ?? '';
$erro = $_SESSION['erro'] ?? '';
unset($_SESSION['sucesso'], $_SESSION['erro']);

// Lógica de exclusão
if (isset($_GET['excluir'])) {
    $id_animal = (int)$_GET['excluir'];
    try {
        // Buscar imagem para deletar do disco
        $stmt = $db->prepare("SELECT imagem FROM animais WHERE id = ?");
        $stmt->execute([$id_animal]);
        $animal = $stmt->fetch();
        
        if ($animal && !empty($animal['imagem']) && $animal['imagem'] !== 'animais/petPadrao.png') {
            $arquivo = __DIR__ . '/../../assets/img/' . $animal['imagem'];
            if (file_exists($arquivo)) unlink($arquivo);
        }

        $db->prepare("DELETE FROM animais WHERE id = ?")->execute([$id_animal]);
        $_SESSION['sucesso'] = "Animal excluído com sucesso.";
    } catch (PDOException $e) {
        $_SESSION['erro'] = "Erro ao excluir animal.";
    }
    header("Location: animais.php");
    exit();
}

// Buscar animais com nome do doador
$query = "SELECT a.*, u.nome as doador_nome 
          FROM animais a 
          LEFT JOIN doadores d ON a.doador_id = d.id 
          LEFT JOIN usuarios u ON d.usuario_id = u.id 
          ORDER BY a.id DESC";
$stmt = $db->query($query);
$animais = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Animais - Admin</title>
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
                <h3>Gerenciar <span>Animais</span></h3>
                <a href="dashboard.php" class="btn" style="margin-top:1rem; font-size:0.9rem;"><i class="fas fa-arrow-left"></i> Voltar ao Painel</a>
            </div>
        </section>
    </div>

    <div class="cadastro-container" style="max-width:1000px;">
        <?php if ($sucesso): ?><div class="message success"><?= htmlspecialchars($sucesso) ?></div><?php endif; ?>
        <?php if ($erro): ?><div class="message error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>

        <div class="form-section" style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; color:#ccc; text-align:left;">
                <thead>
                    <tr style="border-bottom:2px solid #3498db;">
                        <th style="padding:10px;">Animal</th>
                        <th style="padding:10px;">Doador</th>
                        <th style="padding:10px;">Status</th>
                        <th style="padding:10px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($animais as $a): ?>
                    <tr style="border-bottom:1px solid #444;">
                        <td style="padding:10px; display:flex; align-items:center; gap:10px;">
                            <img src="<?= $base ?>/assets/img/<?= htmlspecialchars($a['imagem']) ?>" width="40" height="40" style="border-radius:50%; object-fit:cover;" onerror="this.src='<?= $base ?>/assets/img/animais/petPadrao.png'">
                            <span><?= htmlspecialchars($a['nome']) ?></span>
                        </td>
                        <td style="padding:10px;"><?= htmlspecialchars($a['doador_nome'] ?? 'Desconhecido') ?></td>
                        <td style="padding:10px; color: <?= $a['disponivel'] ? '#2ecc71' : '#e74c3c' ?>">
                            <?= $a['disponivel'] ? 'Disponível' : 'Adotado' ?>
                        </td>
                        <td style="padding:10px;">
                            <a href="animais.php?excluir=<?= $a['id'] ?>" 
                               onclick="return confirm('Excluir este animal permanentemente?');"
                               style="color:#e74c3c; font-weight:bold;">
                               <i class="fas fa-trash"></i> Excluir
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>