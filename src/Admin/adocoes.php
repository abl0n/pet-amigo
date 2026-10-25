<?php
$base = '/pet-amigo-main';
require_once __DIR__ . '/../../includes/admin_auth.php';
require_once __DIR__ . '/../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$sucesso = $_SESSION['sucesso'] ?? '';
$erro = $_SESSION['erro'] ?? '';
unset($_SESSION['sucesso'], $_SESSION['erro']);

// Lógica de Aprovar/Rejeitar
if (isset($_GET['acao']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $acao = $_GET['acao'];

    try {
        $db->beginTransaction();

        if ($acao === 'aprovar') {
            // Aprova a adoção
            $db->prepare("UPDATE adocoes SET status = 'aprovada' WHERE id = ?")->execute([$id]);
            // O animal já foi marcado como indisponivel na hora do pedido, então não precisa mexer aqui.
            $_SESSION['sucesso'] = "Adoção aprovada com sucesso!";
        } elseif ($acao === 'rejeitar') {
            // Rejeita a adoção
            $db->prepare("UPDATE adocoes SET status = 'rejeitada' WHERE id = ?")->execute([$id]);
            
            // Volta o animal para disponível
            $stmt = $db->prepare("SELECT animal_id FROM adocoes WHERE id = ?");
            $stmt->execute([$id]);
            $adocao = $stmt->fetch();
            
            if ($adocao) {
                $db->prepare("UPDATE animais SET disponivel = 1 WHERE id = ?")->execute([$adocao['animal_id']]);
            }
            $_SESSION['sucesso'] = "Adoção rejeitada. O animal voltou a ficar disponível.";
        }

        $db->commit();
    } catch (PDOException $e) {
        $db->rollBack();
        $_SESSION['erro'] = "Erro ao processar adoção.";
    }
    header("Location: adocoes.php");
    exit();
}

// Buscar todas as adoções com dados do animal e do adotante
$query = "SELECT ad.*, a.nome as animal_nome, a.imagem as animal_imagem, u.nome as usuario_nome, u.email as usuario_email 
          FROM adocoes ad
          JOIN animais a ON ad.animal_id = a.id
          JOIN usuarios u ON ad.usuario_id = u.id
          ORDER BY ad.data_pedido DESC";
$stmt = $db->query($query);
$adocoes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Adoções - Admin</title>
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
                <h3>Aprovar <span>Adoções</span></h3>
                <a href="dashboard.php" class="btn" style="margin-top:1rem; font-size:0.9rem;"><i class="fas fa-arrow-left"></i> Voltar ao Painel</a>
            </div>
        </section>
    </div>

    <div class="cadastro-container" style="max-width:1000px;">
        <?php if ($sucesso): ?><div class="message success"><?= htmlspecialchars($sucesso) ?></div><?php endif; ?>
        <?php if ($erro): ?><div class="message error"><?= htmlspecialchars($erro) ?></div><?php endif; ?>

        <?php if (empty($adocoes)): ?>
            <div class="form-section" style="text-align:center;">
                <p>Nenhum pedido de adoção realizado ainda.</p>
            </div>
        <?php else: ?>
            <div class="form-section" style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; color:#ccc; text-align:left;">
                    <thead>
                        <tr style="border-bottom:2px solid #3498db;">
                            <th style="padding:10px;">Animal</th>
                            <th style="padding:10px;">Adotante</th>
                            <th style="padding:10px;">Status</th>
                            <th style="padding:10px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($adocoes as $ad): ?>
                        <tr style="border-bottom:1px solid #444;">
                            <td style="padding:10px; display:flex; align-items:center; gap:10px;">
                                <img src="<?= $base ?>/assets/img/<?= htmlspecialchars($ad['animal_imagem']) ?>" width="40" height="40" style="border-radius:50%; object-fit:cover;" onerror="this.src='<?= $base ?>/assets/img/animais/petPadrao.png'">
                                <span><?= htmlspecialchars($ad['animal_nome']) ?></span>
                            </td>
                            <td style="padding:10px;">
                                <?= htmlspecialchars($ad['usuario_nome']) ?><br>
                                <small style="color:#999;"><?= htmlspecialchars($ad['usuario_email']) ?></small>
                            </td>
                            <td style="padding:10px;">
                                <?php if ($ad['status'] === 'pendente'): ?>
                                    <span style="color:#f1c40f; font-weight:bold;">Pendente</span>
                                <?php elseif ($ad['status'] === 'aprovada'): ?>
                                    <span style="color:#2ecc71; font-weight:bold;">Aprovada</span>
                                <?php else: ?>
                                    <span style="color:#e74c3c; font-weight:bold;">Rejeitada</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:10px;">
                                <?php if ($ad['status'] === 'pendente'): ?>
                                    <a href="adocoes.php?acao=aprovar&id=<?= $ad['id'] ?>" style="color:#2ecc71; font-weight:bold; margin-right:10px;">
                                        <i class="fas fa-check"></i> Aprovar
                                    </a>
                                    <a href="adocoes.php?acao=rejeitar&id=<?= $ad['id'] ?>" style="color:#e74c3c; font-weight:bold;">
                                        <i class="fas fa-times"></i> Rejeitar
                                    </a>
                                <?php else: ?>
                                    <span style="color:#666; font-size:0.8rem;">Processado</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>