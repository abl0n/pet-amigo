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

// Buscar dados atualizados
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

// Contar adoções do usuário
$stmt = $db->prepare("SELECT COUNT(*) as total FROM adocoes WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$total_adocoes = $stmt->fetch()['total'];

// Contar animais cadastrados (se doador)
$total_animais = 0;
if ($doador) {
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM animais WHERE doador_id = ?");
    $stmt->execute([$doador['id']]);
    $total_animais = $stmt->fetch()['total'];
}

$sucesso = $_SESSION['sucesso'] ?? '';
$erro    = $_SESSION['erro'] ?? '';
unset($_SESSION['sucesso'], $_SESSION['erro']);


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Conta - PetAmigo</title>
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
                <h3>Minha <span>Conta</span></h3>
                <p>Gerencie seus dados pessoais, segurança e conta</p>
            </div>
        </section>
    </div>

    <div class="cadastro-container" style="max-width:900px;">

        <?php if ($sucesso): ?>
            <div class="message success"><?= htmlspecialchars($sucesso) ?></div>
        <?php endif; ?>
        <?php if ($erro): ?>
            <div class="message error"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <!-- Painel de resumo -->
        <div class="form-section" style="display:flex;gap:2rem;flex-wrap:wrap;align-items:center;margin-bottom:2rem;">
            <div style="width:90px;height:90px;border-radius:50%;background:linear-gradient(135deg,#3498db,#2980b9);display:flex;align-items:center;justify-content:center;font-size:2.5rem;color:#fff;flex-shrink:0;">
                <i class="fas fa-user"></i>
            </div>
            <div style="flex:1;min-width:200px;">
                <h3 style="color:#3498db;margin-bottom:0.3rem;"><?= htmlspecialchars($usuario['nome']) ?></h3>
                <p style="color:#ccc;"><?= htmlspecialchars($usuario['email']) ?></p>
                <p style="color:#999;font-size:0.9rem;">
                    <i class="fas fa-calendar"></i>
                    Membro desde <?= date('d/m/Y', strtotime($usuario['data_cadastro'])) ?>
                </p>
            </div>
            <div style="display:flex;gap:1rem;flex-wrap:wrap;">
                <div style="text-align:center;padding:0.8rem 1.2rem;background:rgba(52,152,219,0.1);border-radius:8px;">
                    <div style="font-size:1.5rem;color:#3498db;font-weight:bold;"><?= $total_adocoes ?></div>
                    <div style="font-size:0.8rem;color:#ccc;">Adoções</div>
                </div>
                <?php if ($doador): ?>
                <div style="text-align:center;padding:0.8rem 1.2rem;background:rgba(46,204,113,0.1);border-radius:8px;">
                    <div style="font-size:1.5rem;color:#2ecc71;font-weight:bold;"><?= $total_animais ?></div>
                    <div style="font-size:0.8rem;color:#ccc;">Animais</div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Formulário de edição -->
        <div class="form-section" style="margin-bottom:2rem;">
            <h3 style="color:#3498db;margin-bottom:1.5rem;">
                <i class="fas fa-user-edit"></i> Editar Dados
            </h3>

            <form method="POST" action="atualizar-conta.php" class="cadastro-form">
                <div class="form-group">
                    <label for="nome">Nome Completo *</label>
                    <input type="text" id="nome" name="nome"
                           value="<?= htmlspecialchars($usuario['nome']) ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">E-mail *</label>
                        <input type="email" id="email" name="email"
                               value="<?= htmlspecialchars($usuario['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="telefone">Telefone *</label>
                        <input type="tel" id="telefone" name="telefone"
                               value="<?= htmlspecialchars($usuario['telefone']) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="endereco">Endereço Completo *</label>
                    <textarea id="endereco" name="endereco" rows="3" required><?= htmlspecialchars($usuario['endereco']) ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>

        <!-- Alterar senha -->
        <div class="form-section" style="margin-bottom:2rem;">
            <h3 style="color:#f39c12;margin-bottom:1.5rem;">
                <i class="fas fa-lock"></i> Alterar Senha
            </h3>

            <form method="POST" action="alterar-senha.php" class="cadastro-form">
                <div class="form-group">
                    <label for="senha_atual">Senha Atual *</label>
                    <input type="password" id="senha_atual" name="senha_atual" required autocomplete="current-password">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="nova_senha">Nova Senha *</label>
                        <input type="password" id="nova_senha" name="nova_senha" minlength="6" required autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label for="confirmar_senha">Confirmar Nova Senha *</label>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" minlength="6" required autocomplete="new-password">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn"
                            style="background:linear-gradient(135deg,#f39c12,#e67e22);">
                        <i class="fas fa-key"></i> Alterar Senha
                    </button>
                </div>
            </form>
        </div>

        <!-- Zona de perigo -->
        <div class="form-section" style="border:2px solid #e74c3c;">
            <h3 style="color:#e74c3c;margin-bottom:1rem;">
                <i class="fas fa-exclamation-triangle"></i> Zona de Perigo
            </h3>
            <p style="color:#ccc;margin-bottom:1.5rem;">
                Ao excluir sua conta, todos os seus dados serão removidos permanentemente,
                incluindo pedidos de adoção<?= $doador ? ' e animais cadastrados' : '' ?>.
                <strong style="color:#e74c3c;">Essa ação não pode ser desfeita.</strong>
            </p>
            <a href="excluir-conta.php" class="btn"
               style="background:linear-gradient(135deg,#e74c3c,#c0392b);">
                <i class="fas fa-trash"></i> Excluir Minha Conta
            </a>
        </div>

    </div>

    <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>