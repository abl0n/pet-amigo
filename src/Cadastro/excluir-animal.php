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

// Verificar se é doador
$stmt = $db->prepare("SELECT * FROM doadores WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario']['id']]);
$doador = $stmt->fetch();

if (!$doador) {
    header("Location: cadastro-doador.php");
    exit();
}

$id = (int) ($_GET['id'] ?? 0);

// Buscar animal
$stmt = $db->prepare("SELECT * FROM animais WHERE id = ? AND doador_id = ?");
$stmt->execute([$id, $doador['id']]);
$animal = $stmt->fetch();

if (!$animal) {
    $_SESSION['erro'] = "Animal não encontrado ou não pertence a você.";
    header("Location: meus-animais.php");
    exit();
}

// Se confirmado, deletar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar'])) {
    try {
        $db->beginTransaction();

        // Deletar adoções ligadas
        $db->prepare("DELETE FROM adocoes WHERE animal_id = ?")->execute([$id]);

        // Deletar imagem física (se não for petPadrao)
        if ($animal['imagem'] && $animal['imagem'] !== 'animais/petPadrao.png') {
            $arquivo = __DIR__ . '/../../assets/img/' . $animal['imagem'];
            if (file_exists($arquivo)) unlink($arquivo);
        }

        // Deletar animal
        $db->prepare("DELETE FROM animais WHERE id = ?")->execute([$id]);

        $db->commit();
        $_SESSION['sucesso'] = "Animal excluído com sucesso.";
        header("Location: meus-animais.php");
        exit();

    } catch (PDOException $e) {
        $db->rollBack();
        $erro = "Erro ao excluir: " . $e->getMessage();
    }
}


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Animal - PetAmigo</title>
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
            <h2 style="margin-bottom:1rem;">Confirmar Exclusão</h2>
            <p style="color:#ccc;margin-bottom:2rem;">
                Tem certeza que deseja excluir o animal <strong><?= htmlspecialchars($animal['nome']) ?></strong>?
                Essa ação <strong>não pode ser desfeita</strong> e todos os pedidos de adoção ligados serão removidos.
            </p>

            <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;margin-top:2rem;">
                <a href="meus-animais.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <form method="POST" style="display:inline;">
                    <button type="submit" name="confirmar" value="1" class="btn"
                            style="background:linear-gradient(135deg,#e74c3c,#c0392b);">
                        <i class="fas fa-trash"></i> Sim, excluir
                    </button>
                </form>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>