<?php
session_start();
include 'db.php';

// CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    die("Ação não permitida.");
}

$nome       = trim($_POST['nome']);
$email      = trim($_POST['email']);
$senha      = $_POST['senha'];
$confirma   = $_POST['confirma'];
$tipo_conta = $_POST['tipo_conta'];

if (empty($nome) || empty($email) || empty($senha) || empty($confirma) || empty($tipo_conta)) {
    $_SESSION['erro'] = "Por favor, preencha todos os campos.";
    header("Location: cadastro_usuario.php");
    exit();
}

if ($senha !== $confirma) {
    $_SESSION['erro'] = "As senhas não coincidem.";
    header("Location: cadastro_usuario.php");
    exit();
}

$sql = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$sql->bind_param("s", $email);
$sql->execute();
$sql->store_result();

if ($sql->num_rows > 0) {
    $_SESSION['erro'] = "Este e-mail já está cadastrado.";
    header("Location: cadastro_usuario.php");
    exit();
}

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// Inserir
$stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo_conta) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nome, $email, $senha_hash, $tipo_conta);

if ($stmt->execute()) {

    // SESSÃO IMEDIATA
    $_SESSION['usuario_id']   = $conn->insert_id;
    $_SESSION['usuario_nome'] = $nome;
    $_SESSION['usuario_tipo'] = $tipo_conta;

    // Redirecionamento
    if ($tipo_conta === "guia") {
        header("Location: dashboard_guia.php");
    } else {
        header("Location: dashboard_turista.php");
    }
    exit();

} else {
    $_SESSION['erro'] = "Erro ao cadastrar: " . $conn->error;
    header("Location: cadastro_usuario.php");
    exit();
}
