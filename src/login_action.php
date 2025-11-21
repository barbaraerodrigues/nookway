<?php
session_start();

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// POR AGORA: login fake pra começar o projeto.
if ($email === 'teste@teste.com' && $senha === '1234') {
    $_SESSION['user'] = $email;
    header("Location: ../public/explorar.php");
    exit;
} else {
    echo "Login inválido";
}
