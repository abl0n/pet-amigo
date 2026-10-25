<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Buscar animal
$animal_id = (int) ($_GET['animal_id'] ?? 0);
$stmt = $db->prepare("SELECT * FROM animais WHERE id = ? AND disponivel = 1");
$stmt->execute([$animal_id]);
$animal = $stmt->fetch();

if (!$animal) {
    header("Location: ../Menu/menu.php");
    exit();
}

// Buscar dados do usuário logado
$stmt = $db->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario']['id']]);
$usuario = $stmt->fetch();

$sucesso = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id  = $_SESSION['usuario']['id'];
    $animal_id   = $animal['id'];
    $motivacao   = trim($_POST['motivacao'] ?? '');
    $experiencia = $_POST['experiencia'] ?? '';
    $termo       = isset($_POST['termo']) ? 1 : 0;

    if (!$termo) {
        $erro = "Você precisa concordar com os termos de adoção responsável.";
    } elseif (empty($motivacao) || empty($experiencia)) {
        $erro = "Preencha todos os campos obrigatórios.";
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO adocoes (usuario_id, animal_id, motivacao, experiencia) VALUES (?, ?, ?, ?)");
            $stmt->execute([$usuario_id, $animal_id, $motivacao, $experiencia]);

            // Marcar animal como indisponível
            $db->prepare("UPDATE animais SET disponivel = 0 WHERE id = ?")->execute([$animal_id]);

            $sucesso = "Pedido de adoção enviado com sucesso! Entraremos em contato em breve.";
        } catch (PDOException $e) {
            $erro = "Erro ao enviar pedido. Tente novamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido de Adoção - PetAmigo</title>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/base.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/components.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/cadastro.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

    <?php include __DIR__ . '/../../includes/header.php'; ?>

    <div class="cadastro-container" style="max-width:900px;">
        <?php if ($sucesso): ?>
            <div class="form-section" style="text-align:center;">
                <i class="fas fa-check-circle" style="font-size:4rem;color:#2ecc71;margin-bottom:1rem;"></i>
                <h2 style="margin-bottom:1rem;">Pedido Enviado!</h2>
                <p style="color:#ccc;margin-bottom:2rem;"><?= htmlspecialchars($sucesso) ?></p>
                <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
                    <a href="../Home/home.php" class="btn">Voltar para Home</a>
                    <a href="../Menu/menu.php" class="btn btn-secondary">Ver outros animais</a>
                </div>
            </div>
        <?php else: ?>
            <h2 class="title" style="text-align:center;margin-bottom:2rem;">Pedido de <span>Adoção</span></h2>

            <div class="form-section" style="margin-bottom:2rem;display:flex;gap:2rem;align-items:center;flex-wrap:wrap;">
                <<img src="<?= $base ?>/assets/img/<?= htmlspecialchars($animal['imagem']) ?>"
                     alt="<?= htmlspecialchars($animal['nome']) ?>"
                     style="width:200px;height:200px;object-fit:cover;border-radius:10px;"
                     onerror="this.src='<?= $base ?>/assets/img/animais/petPadrao.png'"
                <div style="flex:1;min-width:250px;">
                    <h3 style="color:#3498db;margin-bottom:1rem;"><?= htmlspecialchars($animal['nome']) ?></h3>
                    <p><strong>Espécie:</strong> <?= htmlspecialchars($animal['especie']) ?></p>
                    <p><strong>Raça:</strong> <?= htmlspecialchars($animal['raca']) ?></p>
                    <p><strong>Idade:</strong> <?= htmlspecialchars($animal['idade']) ?> anos</p>
                    <p><strong>Porte:</strong> <?= htmlspecialchars($animal['porte']) ?></p>
                    <p style="margin-top:1rem;color:#ccc;"><?= htmlspecialchars($animal['descricao']) ?></p>
                </div>
            </div>

            <?php if ($erro): ?>
                <div class="message error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <div class="form-section">
                <form method="POST" class="cadastro-form">
                    <div class="form-group">
                        <label>Nome</label>
                        <input type="text" value="<?= htmlspecialchars($usuario['nome']) ?>" readonly>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>E-mail</label>
                            <input type="email" value="<?= htmlspecialchars($usuario['email']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Telefone</label>
                            <input type="text" value="<?= htmlspecialchars($usuario['telefone']) ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Endereço</label>
                        <textarea readonly><?= htmlspecialchars($usuario['endereco']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="motivacao">Por que você quer adotar <?= htmlspecialchars($animal['nome']) ?>? *</label>
                        <textarea id="motivacao" name="motivacao" required placeholder="Conte sua motivação..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="experiencia">Experiência com animais *</label>
                        <select id="experiencia" name="experiencia" required>
                            <option value="">Selecione</option>
                            <option value="Nenhuma">Nenhuma</option>
                            <option value="Pouca">Pouca</option>
                            <option value="Moderada">Moderada</option>
                            <option value="Muita">Muita</option>
                        </select>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="termo" name="termo" required>
                        <label for="termo">
                            <strong>Concordo com os Termos de Adoção Responsável:</strong><br>
                            • Fornecerei todos os cuidados necessários<br>
                            • Garantirei alimentação e cuidados veterinários<br>
                            • Oferecerei ambiente seguro e amoroso<br>
                            • Não abandonarei o animal<br>
                            • Comprometo-me por toda a vida do animal
                        </label>
                    </div>

                    <div class="form-actions">
                        <a href="../Menu/menu.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Enviar Pedido
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>