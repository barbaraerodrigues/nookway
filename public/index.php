<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>NOOKWAY - Início</title>
  <link rel="stylesheet" href="css/nookway.css">
</head>

<body>

  <?php include 'templates/header.php'; ?>

  <!-- Hero -->
  <section class="hero">
    <div class="container">
      <div>
        <div class="kicker">Bem-vindo à NookWay</div>
        <h1>Descubra experiências autênticas</h1>
        <p class="lead">Conectamos viajantes e guias locais em uma plataforma moderna, simples e segura.</p>

        <div class="cta">
          <a href="explorar.php" class="btn primary">Explorar Tours</a>
          
        </div>
      </div>
    </div>
  </section>

  <?php include 'templates/footer.php'; ?>

  <script>
    document.getElementById("ano").textContent = new Date().getFullYear();
  </script>
</body>

</html>
