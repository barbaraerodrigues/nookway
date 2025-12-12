<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Gera token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Recebe e limpa erro
$erro = $_SESSION['erro'] ?? null;
unset($_SESSION['erro']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Criar Conta - Nookway</title>
  <link rel="stylesheet" href="css/nookway.css">
</head> 

<body>

<?php include 'templates/header.php'; ?>

<main class="container auth-page">
  <div class="auth-box">
    <h1>Criar Conta</h1>
    <p class="muted">Cadastre-se para explorar experiências únicas.</p>

    <!-- Mensagem de erro formatada -->
    <?php if ($erro): ?>
      <div class="alert error" style="margin-bottom:16px; padding:12px; border-radius:12px; background:#ffe6e6; color:#b30000; text-align:center;">
        <?= htmlspecialchars($erro) ?>
      </div>
    <?php endif; ?>

    <form action="cadastrar.php" method="post" class="auth-form">

      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

      <label for="nome">Nome completo</label>
      <input type="text" id="nome" name="nome" placeholder="Seu nome" required>

      <label for="email">E-mail</label>
      <input type="email" id="email" name="email" placeholder="voce@email.com" required>

      <label for="senha">Senha</label>
      <input type="password" id="senha" name="senha" placeholder="Crie uma senha" required>

      <label for="confirma">Confirmar senha</label>
      <input type="password" id="confirma" name="confirma" placeholder="Repita a senha" required>

      <label for="tipo_conta">Tipo de Conta</label>
      <select id="tipo_conta" name="tipo_conta" required>
        <option value="">Selecione...</option>
        <option value="turista">Sou Turista</option>
        <option value="guia">Sou Guia</option>
      </select>

      <button type="submit" class="btn primary" style="width:100%;">Cadastrar</button>

      <p class="muted center">
        Já tem conta?
        <a href="login.php"><strong>Entre aqui</strong></a>
      </p>
    </form>

  </div>
</main>

<?php include 'templates/footer.php'; ?>

<script>
  document.getElementById("ano").textContent = new Date().getFullYear();
</script>

</body>
</html>
