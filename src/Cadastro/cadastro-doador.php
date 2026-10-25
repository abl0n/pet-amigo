<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Verificar se já é doador
$stmt = $db->prepare("SELECT * FROM doadores WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario']['id']]);
$doador = $stmt->fetch();

if ($doador) {
    header("Location: cadastro-animal.php");
    exit();
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cpf = trim($_POST['cpf'] ?? '');
    $usuario_id = $_SESSION['usuario']['id'];

    if (empty($cpf)) {
        $erro = "CPF é obrigatório.";
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO doadores (usuario_id, cpf) VALUES (?, ?)");
            $stmt->execute([$usuario_id, $cpf]);
            $_SESSION['sucesso'] = "Cadastro como doador realizado com sucesso!";
            header("Location: cadastro-animal.php");
            exit();
        } catch (PDOException $e) {
            $erro = str_contains($e->getMessage(), 'Duplicate')
                  ? "Este CPF já está cadastrado."
                  : "Erro ao cadastrar como doador.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Doador - PetAmigo</title>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/base.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/components.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/cadastro.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

    <?php include __DIR__ . '/../../includes/header.php'; ?>

    <div class="cadastro-container">
        <div class="cadastro-header">
            <h2>Tornar-se <span>Doador</span></h2>
        </div>

        <div class="form-section">
            <div class="info-box">
                <h3><i class="fas fa-info-circle"></i> Benefícios de ser um Doador:</h3>
                <p>✓ Cadastrar animais para adoção</p>
                <p>✓ Acompanhar o processo de adoção</p>
                <p>✓ Receber suporte da nossa equipe</p>
                <p>✓ Contribuir para um mundo com menos animais abandonados</p>
            </div>

            <?php if ($erro): ?>
                <div class="message error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <form method="POST" class="cadastro-form">
                <div class="form-group">
                    <label for="cpf">CPF *</label>
                    <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" maxlength="14" required>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="termo" name="termo" required>
                    <label for="termo">Concordo com os termos e responsabilidades de ser um doador</label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" style="width:100%;">
                        <i class="fas fa-heart"></i> Tornar-me Doador
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include __DIR__ . '/../../includes/footer.php'; ?>

    <script>
    document.getElementById('cpf').addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g, '');
        if (v.length <= 11) {
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        }
        e.target.value = v;
    });
    </script>
</body>
</html>