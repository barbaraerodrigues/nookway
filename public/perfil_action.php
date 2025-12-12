<?php
session_start();
include 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['usuario_id'];

// Verificação CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Ação não permitida.");
}

$nome = trim($_POST['nome']);
$email = trim($_POST['email']);
$telefone = trim($_POST['telefone'] ?? '');
$localizacao = trim($_POST['localizacao'] ?? '');
$bio = trim($_POST['bio'] ?? '');
$data_nascimento = $_POST['data_nascimento'] ?? null;

// ====================== FOTO DE PERFIL ==========================
$foto_final = null;

if (!empty($_FILES['foto']['name'])) {

    $upload_dir = "uploads/usuarios/";
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {

        $foto_final = $upload_dir . "user_" . $id . "." . $ext;

        move_uploaded_file($_FILES['foto']['tmp_name'], $foto_final);
    }
}

// Atualizar DB
$query = "UPDATE usuarios 
          SET nome=?, email=?, telefone=?, localizacao=?, bio=?, data_nascimento=?";

if ($foto_final) $query .= ", foto_perfil='$foto_final'";

$query .= " WHERE id=?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ssssssi", $nome, $email, $telefone, $localizacao, $bio, $data_nascimento, $id);
$stmt->execute();
$stmt->close();

// Atualiza sessão
$_SESSION['usuario_nome'] = $nome;

header("Location: perfil.php");
exit();
