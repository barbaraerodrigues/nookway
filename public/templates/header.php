<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Compatibilidade com versões antigas
if (isset($_SESSION['usuario_id']) && !isset($_SESSION['id'])) {
    $_SESSION['id'] = $_SESSION['usuario_id'];
}
if (isset($_SESSION['usuario_tipo']) && !isset($_SESSION['tipo_conta'])) {
    $_SESSION['tipo_conta'] = $_SESSION['usuario_tipo'];
}


$usuario_logado = $_SESSION['id'] ?? null;
$tipo_conta     = $_SESSION['tipo_conta'] ?? null;

if ($usuario_logado) {
    $pagina_destino = $tipo_conta === 'guia' ? 'dashboard_guia.php' : 'dashboard_turista.php';
} else {
    $pagina_destino = 'index.php';
}
?>
<header> 
  <div class="container nav">

    <a href="<?= htmlspecialchars($pagina_destino) ?>" class="brand">
      <span class="logo">NW</span><span>NOOKWAY</span>
    </a>


    <!---------------------------- -->

    <nav class="nav-links">
      <a href="contato.php">Contato</a>

      <?php if ($usuario_logado): ?>
          <a href="perfil.php">Meu Perfil</a>
          <a href="logout.php">Sair</a>
      <?php else: ?>
          <a href="cadastro_usuario.php">Criar Conta</a>
          <a href="login.php">Entrar</a>
      <?php endif; ?>

    </nav>
  </div>
</header>



  
  