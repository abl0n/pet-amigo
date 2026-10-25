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

$usuario_id = $_SESSION['usuario']['id'];

// Buscar dados para exibir na confirmação
$stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    session_destroy();
    header("Location: ../Login/login.php");
    exit();
}

// Verificar se é doador
$stmt = $db->prepare("SELECT * FROM doadores WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$doador = $stmt->fetch();

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar'])) {
    $senha_confirmacao = $_POST['senha_confirmacao'] ?? '';

    if (!password_verify($senha_confirmacao, $usuario['senha'])) {
        $erro = "Senha incorreta. A conta não foi excluída.";
    } else {
        try {
            $db->beginTransaction();

            // Se for doador, excluir imagens dos animais antes de deletar
            if ($doador) {
                $stmt = $db->prepare("SELECT imagem FROM animais WHERE doador_id = ?");
                $stmt->execute([$doador['id']]);
                $animais = $stmt->fetchAll();

                foreach ($animais as $animal) {
                    if (!empty($animal['imagem']) && $animal['imagem'] !== 'animais/petPadrao.png') {
                        $arquivo = __DIR__ . '/../../assets/img/' . $animal['imagem'];
                        if (file_exists($arquivo)) unlink($arquivo);
                    }
                }
            }

            // Excluir usuário — CASCADE cuidará de:
            // - doadores (e consequentemente animais -> adocoes -> avaliacoes)
            // - adocoes do usuário
            // - avaliacoes do usuário
            $stmt = $db->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([$usuario_id]);

            $db->commit();

            // Destruir sessão
            session_destroy();
            header("Location: ../Home/home.php?conta_excluida=1");
            exit();

        } catch (PDOException $e) {
            $db->rollBack();
            $erro = "Erro ao excluir conta: " . $e->getMessage();
        }
    }
}


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Conta - PetAmigo</title>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/base.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/components.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/cadastro.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

    <?php include __DIR__ . '/../../includes/header.php'; ?>

    <div class="cadastro-container" style="max-width:600px;">
        <div class="form-section" style="text-align:center;">
            <i class="fas fa-exclamation-triangle" style="font-size:4rem;color:#e74c3c;margin-bottom:1rem;"></i>
            <h2 style="margin-bottom:1rem;">Excluir Conta Permanentemente</h2>

            <div style="background:rgba(231,76,60,0.1);border:1px solid #e74c3c;border-radius:8px;padding:1rem;margin-bottom:1.5rem;text-align:left;">
                <p style="color:#e74c3c;font-weight:bold;margin-bottom:0.5rem;">
                    <i class="fas fa-info-circle"></i> O que será removido:
                </p>
                <ul style="color:#ccc;margin-left:1.5rem;">
                    <li>Seus dados pessoais (nome, e-mail, telefone, endereço)</li>
                    <li>Histórico de pedidos de adoção</li>
                    <li>Avaliações realizadas</li>
                    <?php if ($doador): ?>
                        <li><strong>Animais cadastrados por você (<?= htmlspecialchars($usuario['nome']) ?>)</strong></li>
                    <?php endif; ?>
                </ul>
                <p style="color:#e74c3c;margin-top:1rem;font-weight:bold;">
                    Essa ação NÃO pode ser desfeita.
                </p>
            </div>

            <?php if ($erro): ?>
                <div class="message error" style="text-align:left;"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <p style="color:#ccc;margin-bottom:1rem;">
                Para confirmar, digite sua senha abaixo:
            </p>

            <form method="POST">
                <div class="form-group" style="text-align:left;">
                    <label for="senha_confirmacao">Senha *</label>
                    <input type="password" id="senha_confirmacao" name="senha_confirmacao" required autocomplete="current-password">
                </div>

                <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;margin-top:2rem;">
                    <a href="minha-conta.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                    <button type="submit" name="confirmar" value="1" class="btn"
                            style="background:linear-gradient(135deg,#e74c3c,#c0392b);">
                        <i class="fas fa-trash"></i> Excluir Definitivamente
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>