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

// Buscar animal pelo ID
$id = (int) ($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT * FROM animais WHERE id = ? AND doador_id = ?");
$stmt->execute([$id, $doador['id']]);
$animal = $stmt->fetch();

if (!$animal) {
    header("Location: meus-animais.php");
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

    // Upload de imagem (se enviada)
    $imagem = $animal['imagem']; // mantém a atual por padrão
    if (!empty($_FILES['imagem']['name']) && $_FILES['imagem']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($ext, $permitidas)) {
            $nomeArquivo = uniqid() . '.' . $ext;
            $pasta = __DIR__ . '/../../assets/img/animais/';
            if (!is_dir($pasta)) mkdir($pasta, 0777, true);

            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $pasta . $nomeArquivo)) {
                // Deletar imagem antiga se não for petPadrao.png
                if ($animal['imagem'] && $animal['imagem'] !== 'animais/petPadrao.png') {
                    $arquivoAntigo = __DIR__ . '/../../assets/img/' . $animal['imagem'];
                    if (file_exists($arquivoAntigo)) unlink($arquivoAntigo);
                }
                $imagem = 'animais/' . $nomeArquivo;
            } else {
                $erro = "Erro ao fazer upload da imagem.";
            }
        } else {
            $erro = "Formato inválido. Use JPG, PNG, GIF ou WEBP.";
        }
    }

    if (empty($erro)) {
        $sql = "UPDATE animais SET
                    nome = ?, especie = ?, raca = ?, idade = ?, porte = ?,
                    sexo = ?, vacinado = ?, castrado = ?, descricao = ?,
                    observacoes = ?, imagem = ?
                WHERE id = ? AND doador_id = ?";
        $stmt = $db->prepare($sql);
        $ok = $stmt->execute([
            $nome, $especie, $raca, $idade, $porte, $sexo,
            $vacinado, $castrado, $descricao, $observacoes, $imagem,
            $id, $doador['id']
        ]);

        if ($ok) {
            $sucesso = "Animal atualizado com sucesso!";
            // Recarrega dados atualizados
            $stmt = $db->prepare("SELECT * FROM animais WHERE id = ?");
            $stmt->execute([$id]);
            $animal = $stmt->fetch();
        } else {
            $erro = "Erro ao atualizar animal.";
        }
    }
}


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Animal - PetAmigo</title>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/base.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/components.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/cadastro.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

    <?php include __DIR__ . '/../../includes/header.php'; ?>

    <div class="cadastro-container">
        <div class="cadastro-header">
            <h2>Editar <span>Animal</span></h2>
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
                    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($animal['nome']) ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="especie">Espécie *</label>
                        <select id="especie" name="especie" required>
                            <option value="">Selecione</option>
                            <option value="Cachorro" <?= $animal['especie'] == 'Cachorro' ? 'selected' : '' ?>>Cachorro</option>
                            <option value="Gato" <?= $animal['especie'] == 'Gato' ? 'selected' : '' ?>>Gato</option>
                            <option value="Outro" <?= $animal['especie'] == 'Outro' ? 'selected' : '' ?>>Outro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="raca">Raça *</label>
                        <input type="text" id="raca" name="raca" value="<?= htmlspecialchars($animal['raca']) ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="idade">Idade (anos) *</label>
                        <input type="number" id="idade" name="idade" min="0" max="30" value="<?= htmlspecialchars($animal['idade']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="porte">Porte *</label>
                        <select id="porte" name="porte" required>
                            <option value="">Selecione</option>
                            <option value="Pequeno" <?= $animal['porte'] == 'Pequeno' ? 'selected' : '' ?>>Pequeno</option>
                            <option value="Médio" <?= $animal['porte'] == 'Médio' ? 'selected' : '' ?>>Médio</option>
                            <option value="Grande" <?= $animal['porte'] == 'Grande' ? 'selected' : '' ?>>Grande</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="sexo">Sexo *</label>
                        <select id="sexo" name="sexo" required>
                            <option value="">Selecione</option>
                            <option value="M" <?= $animal['sexo'] == 'M' ? 'selected' : '' ?>>Macho</option>
                            <option value="F" <?= $animal['sexo'] == 'F' ? 'selected' : '' ?>>Fêmea</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Saúde</label>
                        <div class="checkbox-group">
                            <input type="checkbox" id="vacinado" name="vacinado" <?= $animal['vacinado'] ? 'checked' : '' ?>>
                            <label for="vacinado">Vacinado</label>
                            <input type="checkbox" id="castrado" name="castrado" <?= $animal['castrado'] ? 'checked' : '' ?>>
                            <label for="castrado">Castrado</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Foto Atual</label>
                    <div class="imagem-atual">
                        <img src="<?= $base ?>/assets/img/<?= htmlspecialchars($animal['imagem']) ?>"
                             alt="<?= htmlspecialchars($animal['nome']) ?>"
                             onerror="this.src='<?= $base ?>/assets/img/animais/petPadrao.png'">
                    </div>
                </div>

                <div class="form-group">
                    <label for="imagem">Nova Foto (opcional)</label>
                    <input type="file" id="imagem" name="imagem" accept="image/*">
                    <small style="color:#999;margin-top:0.5rem;">Deixe em branco para manter a foto atual.</small>
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição *</label>
                    <textarea id="descricao" name="descricao" rows="3" required><?= htmlspecialchars($animal['descricao']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="observacoes">Observações Adicionais</label>
                    <textarea id="observacoes" name="observacoes" rows="2"><?= htmlspecialchars($animal['observacoes'] ?? '') ?></textarea>
                </div>

                <div class="form-actions">
                    <a href="meus-animais.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>