<?php

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

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

$sucesso = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome        = trim($_POST['nome']);
    $especie     = $_POST['especie'];
    $raca        = trim($_POST['raca']);
    $idade       = (int) $_POST['idade'];
    $porte       = $_POST['porte'];
    $sexo        = $_POST['sexo'];
    $vacinado    = isset($_POST['vacinado']) ? 1 : 0;
    $castrado    = isset($_POST['castrado']) ? 1 : 0;
    $descricao   = trim($_POST['descricao']);
    $observacoes = trim($_POST['observacoes'] ?? '');
    $doador_id   = $doador['id'];

// Upload de imagem
$imagem = 'animais/petPadrao.png'; // fallback COM prefixo animais/
if (!empty($_FILES['imagem']['name']) && $_FILES['imagem']['error'] === 0) {
    $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (in_array($ext, $permitidas)) {
        $nomeArquivo = uniqid() . '.' . $ext;
        $pasta = __DIR__ . '/../../assets/img/animais/';

        // Criar pasta se não existir
        if (!is_dir($pasta)) {
            if (!mkdir($pasta, 0777, true)) {
                $erro = "Não foi possível criar a pasta de imagens.";
            }
        }

        // Mover arquivo
        if (empty($erro)) {
            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $pasta . $nomeArquivo)) {
                $imagem = 'animais/' . $nomeArquivo;   // ← ADICIONA prefixo animais/
            } else {
                $erro = "Falha ao salvar imagem. Verifique permissões.";
            }
        }
    } else {
        $erro = "Formato não permitido. Use JPG, PNG, GIF ou WEBP.";
    }
}

    $query = "INSERT INTO animais
              (nome, especie, raca, idade, porte, sexo, vacinado, castrado,
               descricao, observacoes, imagem, doador_id, disponivel)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
    $stmt = $db->prepare($query);

    if ($stmt->execute([$nome, $especie, $raca, $idade, $porte, $sexo,
                        $vacinado, $castrado, $descricao, $observacoes,
                        $imagem, $doador_id])) {
        $sucesso = "Animal cadastrado com sucesso! Ele já está disponível para adoção.";
        $_POST = [];
    } else {
        $erro = "Erro ao cadastrar animal. Tente novamente.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Animal - PetAmigo</title>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/base.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/components.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/cadastro.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

    <?php include __DIR__ . '/../../includes/header.php'; ?>

    <div class="cadastro-container">
        <div class="cadastro-header">
            <h2>Cadastrar <span>Animal</span></h2>
        </div>

        <?php if ($sucesso): ?>
            <div class="message success"><?= htmlspecialchars($sucesso) ?></div>
        <?php endif; ?>
        <?php if ($erro): ?>
            <div class="message error"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <div class="form-section">
            <form method="POST" enctype="multipart/form-data" class="cadastro-form">
                <div class="form-group">
                    <label for="nome">Nome do Animal *</label>
                    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="especie">Espécie *</label>
                        <select id="especie" name="especie" required>
                            <option value="">Selecione</option>
                            <option value="Cachorro" <?= ($_POST['especie'] ?? '') == 'Cachorro' ? 'selected' : '' ?>>Cachorro</option>
                            <option value="Gato"     <?= ($_POST['especie'] ?? '') == 'Gato'     ? 'selected' : '' ?>>Gato</option>
                            <option value="Outro"    <?= ($_POST['especie'] ?? '') == 'Outro'    ? 'selected' : '' ?>>Outro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="raca">Raça *</label>
                        <input type="text" id="raca" name="raca" value="<?= htmlspecialchars($_POST['raca'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="idade">Idade (anos) *</label>
                        <input type="number" id="idade" name="idade" min="0" max="30" value="<?= htmlspecialchars($_POST['idade'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="porte">Porte *</label>
                        <select id="porte" name="porte" required>
                            <option value="">Selecione</option>
                            <option value="Pequeno" <?= ($_POST['porte'] ?? '') == 'Pequeno' ? 'selected' : '' ?>>Pequeno</option>
                            <option value="Médio"   <?= ($_POST['porte'] ?? '') == 'Médio'   ? 'selected' : '' ?>>Médio</option>
                            <option value="Grande"  <?= ($_POST['porte'] ?? '') == 'Grande'  ? 'selected' : '' ?>>Grande</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="sexo">Sexo *</label>
                        <select id="sexo" name="sexo" required>
                            <option value="">Selecione</option>
                            <option value="M" <?= ($_POST['sexo'] ?? '') == 'M' ? 'selected' : '' ?>>Macho</option>
                            <option value="F" <?= ($_POST['sexo'] ?? '') == 'F' ? 'selected' : '' ?>>Fêmea</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Saúde</label>
                        <div class="checkbox-group">
                            <input type="checkbox" id="vacinado" name="vacinado" <?= isset($_POST['vacinado']) ? 'checked' : '' ?>>
                            <label for="vacinado">Vacinado</label>
                            <input type="checkbox" id="castrado" name="castrado" <?= isset($_POST['castrado']) ? 'checked' : '' ?>>
                            <label for="castrado">Castrado</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="imagem">Foto do Animal</label>
                    <input type="file" id="imagem" name="imagem" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição *</label>
                    <textarea id="descricao" name="descricao" rows="3" required placeholder="Descreva o temperamento, personalidade, hábitos..."><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="observacoes">Observações Adicionais</label>
                    <textarea id="observacoes" name="observacoes" rows="2" placeholder="Histórico de saúde, comportamentos específicos..."><?= htmlspecialchars($_POST['observacoes'] ?? '') ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" style="width:100%;">
                        <i class="fas fa-save"></i> Cadastrar Animal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>