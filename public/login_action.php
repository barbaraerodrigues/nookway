<?php
session_start();
include 'db.php'; 

// CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    $_SESSION['login_error'] = "Ação não permitida.";
    header("Location: login.php");
    exit();
}

$email = trim($_POST['email']);
$senha = $_POST['senha'];

if (empty($email) || empty($senha)) {
    $_SESSION['login_error'] = "Preencha todos os campos.";
    header("Location: login.php");
    exit();
}

// Verificação
$stmt = $conn->prepare("SELECT id, senha, nome, tipo_conta FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $_SESSION['login_error'] = "E-mail ou senha incorretos.";
    header("Location: login.php");
    exit();
}

$stmt->bind_result($id, $senha_hash, $nome, $tipo_conta);
$stmt->fetch();

if (!password_verify($senha, $senha_hash)) {
    $_SESSION['login_error'] = "E-mail ou senha incorretos.";
    header("Location: login.php");
    exit();
}

// LOGIN OK → salva sessão
$_SESSION['usuario_id']   = $id;
$_SESSION['usuario_nome'] = $nome;
$_SESSION['usuario_tipo'] = $tipo_conta;

// Redirecionamento por tipo
if ($tipo_conta === "guia") {
    header("Location: dashboard_guia.php");
} else {
    header("Location: dashboard_turista.php");
}
exit();
