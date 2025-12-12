<?php
session_start();
require_once 'db.php';

// Verifica se ID foi passado
if(!isset($_GET['id'])){
    header("Location: index.php");
    exit;
}

$id_usuario = intval($_GET['id']);

// Busca informações do usuário
$stmt = $conn->prepare("
    SELECT nome, email, telefone, bio, data_nascimento, foto_perfil
    FROM usuarios
    WHERE id = ?
");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

if(!$usuario){
    die("Usuário não encontrado.");
}

// Se não tiver imagem, usa padrão
$foto = !empty($usuario['foto_perfil']) ? "uploads/usuarios/" . $usuario['foto_perfil'] : "img/user_default.png";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Perfil de <?= htmlspecialchars($usuario['nome']) ?> – Nookway</title>
<link rel="stylesheet" href="css/nookway.css">
</head>


<body>

<?php include 'templates/header.php'; ?>

<main class="container auth-page">
  <div class="auth-box">
    <h1>Perfil de <?= htmlspecialchars($usuario['nome']) ?></h1>

    <div style="text-align:center; margin-bottom:16px;">
        <img src="<?= $foto ?>" alt="Foto de Perfil" style="width:120px; height:120px; border-radius:50%;">
    </div>

    <p><b>Nome:</b> <?= htmlspecialchars($usuario['nome']) ?></p>
    <p><b>E-mail:</b> <?= htmlspecialchars($usuario['email']) ?></p>
    <?php if(!empty($usuario['telefone'])): ?>
    <p><b>Telefone:</b> <?= htmlspecialchars($usuario['telefone']) ?></p>
    <?php endif; ?>
    <?php if(!empty($usuario['data_nascimento'])): ?>
    <p><b>Data de Nascimento:</b> <?= htmlspecialchars($usuario['data_nascimento']) ?></p>
    <?php endif; ?>
    <?php if(!empty($usuario['bio'])): ?>
    <p><b>Biografia:</b> <br><?= nl2br(htmlspecialchars($usuario['bio'])) ?></p>
    <?php endif; ?>

    <br>
    <a href="javascript:history.back()" class="btn ghost">Voltar</a>
  </div>
</main>

<?php include 'templates/footer.php'; ?>
</body>
</html>
