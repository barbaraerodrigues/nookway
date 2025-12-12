<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'guia') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>NOOKWAY - Dashboard do Guia</title>
  <link rel="stylesheet" href="css/nookway.css">
</head>

<body>

  <?php include 'templates/header.php'; ?> 

  <section class="hero">
    <div class="container">
      <div>
        <div class="kicker">Área do Guia</div>
        <h1>Olá, Guia! Pronto(a) para acompanhar?</h1>
        <p class="lead">Gerencie suas experiências, tours e perfil profissional.</p>

        <div class="cta">
          <a href="criar_tour.php" class="btn primary">Criar Novo Tour</a>
          <a href="gerenciar_tours.php" class="btn ghost">Gerenciar Tours</a>
          <a href="reservas_recebidas.php" class="btn primary">Reservas Recebidas</a>
        </div>

      </div>
    </div>
  </section>

  <?php include 'templates/footer.php'; ?>

</body>
</html>
