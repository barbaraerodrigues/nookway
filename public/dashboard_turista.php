<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'turista') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>NOOKWAY - Dashboard do Turista</title>
  <link rel="stylesheet" href="css/nookway.css">
</head>

<body>

  <?php include 'templates/header.php'; ?>

  <section class="hero">
    <div class="container">
      <div>
        <div class="kicker">Área do Turista</div>
        <h1>Olá, Viajante! Pronto(a) para explorar?</h1>
        <p class="lead">Encontre experiências incríveis e acompanhe suas reservas.</p>

        <div class="cta">
          <a href="explorar.php" class="btn primary">Explorar Tours</a>
          <a href="minhas_reservas.php" class="btn ghost">Minhas Reservas</a>
          <a href="meus_favoritos.php" class="btn primary">Meus Favoritos</a>
        </div>

      </div>
    </div>
  </section>

  <?php include 'templates/footer.php'; ?>

</body>
</html>
