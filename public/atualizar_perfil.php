<?php
session_start();
require_once 'db.php'; // corrigido de 'conexao.php'

// Só permite POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: perfil.php");
    exit();
}

// Impede acesso sem login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$acao = $_POST['acao'] ?? '';

switch ($acao) {

    // Atualizar informações pessoais
    case 'info':
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $telefone = trim($_POST['telefone'] ?? '');
        $data_nascimento = $_POST['data_nascimento'] ?? null;
        $bio = trim($_POST['bio'] ?? '');

        if (!$nome || !$email) {
            $_SESSION['erro'] = "Nome e e-mail são obrigatórios.";
            header("Location: perfil.php");
            exit();
        }

        $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ?, telefone = ?, data_nascimento = ?, bio = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $nome, $email, $telefone, $data_nascimento, $bio, $usuario_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['usuario_nome'] = $nome;
        header("Location: perfil.php");
        exit();
        break;

    // Atualizar foto de perfil
    case 'foto':
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] != 0) {
            $_SESSION['erro'] = "Erro no upload da foto.";
            header("Location: perfil.php");
            exit();
        }

        $arquivo = $_FILES['foto'];
        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
        $permitidos = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($extensao, $permitidos)) {
            $_SESSION['erro'] = "Formato de imagem não permitido.";
            header("Location: perfil.php");
            exit();
        }

        // Pasta de uploads
        $pasta = "uploads/usuarios/";
        if (!is_dir($pasta)) mkdir($pasta, 0755, true);

        $nome_arquivo = "user_{$usuario_id}_" . time() . "." . $extensao;
        $destino = $pasta . $nome_arquivo;

        if (move_uploaded_file($arquivo['tmp_name'], $destino)) {
            // Atualiza no banco
            $stmt = $conn->prepare("UPDATE usuarios SET foto_perfil = ? WHERE id = ?");
            $stmt->bind_param("si", $nome_arquivo, $usuario_id);
            $stmt->execute();
            $stmt->close();
        } else {
            $_SESSION['erro'] = "Falha ao mover arquivo.";
        }

        header("Location: perfil.php");
        exit();
        break;

    // Alterar senha
    case 'senha':
        $senha_atual = $_POST['senha_atual'] ?? '';
        $nova_senha = $_POST['nova_senha'] ?? '';
        $confirmar_senha = $_POST['confirmar_senha'] ?? '';

        if (!$senha_atual || !$nova_senha || !$confirmar_senha) {
            $_SESSION['erro'] = "Preencha todos os campos de senha.";
            header("Location: perfil.php");
            exit();
        }

        // Busca senha atual
        $stmt = $conn->prepare("SELECT senha FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();
        $stmt->close();

        if (!$usuario || !password_verify($senha_atual, $usuario['senha'])) {
            $_SESSION['erro'] = "Senha atual incorreta.";
            header("Location: perfil.php");
            exit();
        }

        if ($nova_senha !== $confirmar_senha) {
            $_SESSION['erro'] = "A nova senha e confirmação não coincidem.";
            header("Location: perfil.php");
            exit();
        }

        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
        $stmt->bind_param("si", $senha_hash, $usuario_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['erro'] = "Senha atualizada com sucesso.";
        header("Location: perfil.php");
        exit();
        break;

    // Deletar conta
    case 'deletar':
        // Remove foto
        $stmt = $conn->prepare("SELECT foto_perfil FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();
        $stmt->close();

        if ($usuario && !empty($usuario['foto_perfil'])) {
            $arquivo = "uploads/usuarios/" . $usuario['foto_perfil'];
            if (file_exists($arquivo)) unlink($arquivo);
        }

        // Deleta usuário
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $stmt->close();

        // Destrói sessão
        session_unset();
        session_destroy();

        header("Location: index.php");
        exit();
        break;

    default:
        header("Location: perfil.php");
        exit();
        break;
}
?>
