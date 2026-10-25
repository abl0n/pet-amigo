<?php
$base = '/pet-amigo-main';
require_once __DIR__ . '/../../includes/admin_auth.php'; // Segurança
require_once __DIR__ . '/../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$sucesso = $_SESSION['sucesso'] ?? '';
$erro = $_SESSION['erro'] ?? '';
unset($_SESSION['sucesso'], $_SESSION['erro']);

// Lógica de exclusão
if (isset($_GET['excluir'])) {
    $id_excluir = (int)$_GET['excluir'];
    
    if ($id_excluir === $_SESSION['usuario']['id']) {
        $_SESSION['erro'] = "Você não pode excluir sua própria conta de administrador.";
    } else {
        try {
            // O banco deleta os animais e adoções automaticamente (CASCADE)
            $stmt = $db->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([$id_excluir]);
            $_SESSION['sucesso'] = "Usuário excluído com sucesso.";
        } catch (PDOException $e) {
            $_SESSION['erro'] = "Erro ao excluir usuário.";
        }
    }
    header("Location: usuarios.php");
    exit();
}

// Buscar todos os usuários
$stmt = $db->query("SELECT * FROM usuarios ORDER BY data_cadastro DESC");
$usuarios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Usuários - Admin</title>
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
                <h3>Gerenciar <span>Usuários</span></h3>
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
                        <th style="padding:10px;">ID</th>
                        <th style="padding:10px;">Nome</th>
                        <th style="padding:10px;">E-mail</th>
                        <th style="padding:10px;">Tipo</th>
                        <th style="padding:10px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                    <tr style="border-bottom:1px solid #444;">
                        <td style="padding:10px;"><?= $u['id'] ?></td>
                        <td style="padding:10px;"><?= htmlspecialchars($u['nome']) ?></td>
                        <td style="padding:10px;"><?= htmlspecialchars($u['email']) ?></td>
                        <td style="padding:10px;">
                            <span style="color: <?= $u['tipo'] === 'admin' ? '#f1c40f' : '#ccc' ?>">
                                <?= $u['tipo'] ?>
                            </span>
                        </td>
                        <td style="padding:10px;">
                            <?php if ($u['id'] !== $_SESSION['usuario']['id']): ?>
                                <a href="usuarios.php?excluir=<?= $u['id'] ?>" 
                                   onclick="return confirm('Tem certeza que deseja excluir este usuário? Todos os animais e adoções dele serão apagados.');"
                                   style="color:#e74c3c; font-weight:bold;">
                                   <i class="fas fa-trash"></i> Excluir
                                </a>
                            <?php else: ?>
                                <span style="color:#666; font-size:0.8rem;">(Você)</span>
                            <?php endif; ?>
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