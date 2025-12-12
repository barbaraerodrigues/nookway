<?php
session_start();

// Gera token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Mensagem de erro
$erro = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Entrar - Nookway</title>
  <link rel="stylesheet" href="css/nookway.css">
</head> 

<body>
<?php include 'templates/header.php'; ?>

<main class="container auth-page">
  <div class="auth-box">
    <h1>Entrar</h1>
    <p class="muted">Acesse sua conta para continuar.</p>
    

    <!-- Mensagem de erro formatada -->
    <?php if ($erro): ?>
      <div class="alert error" style="margin-bottom:16px; padding:12px; border-radius:12px; background:#ffe6e6; color:#b30000; text-align:center;">
        <?= htmlspecialchars($erro) ?>
      </div>
    <?php endif; ?>

    <form action="login_action.php" method="post" class="auth-form">

      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

      <label for="email">E-mail</label>
      <input type="email" id="email" name="email" placeholder="voce@email.com" required>

      <label for="senha">Senha</label>
      <input type="password" id="senha" name="senha" placeholder="Sua senha" required>

      <button type="submit" class="btn primary" style="width:100%;">Entrar</button>

      <p class="muted center">
        Não tem conta?
        <a href="cadastro_usuario.php"><strong>Crie aqui</strong></a>
      </p>

    </form>
  </div>
</main>

<?php include 'templates/footer.php'; ?>
</body>
</html>
