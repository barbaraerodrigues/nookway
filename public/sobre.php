<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>NOOKWAY - Sobre</title>
  <link rel="stylesheet" href="css/nookway.css">
</head>

<body>

  <?php include 'templates/header.php'; ?>

  <!-- Sobre Nós -->
  <main class="container" style="padding-block: 16px;">
    <h1 style="margin-bottom: 12px; text-align: center;">Sobre a NOOKWAY</h1>


      <p class="lead">
        O NOOKWAY conecta turistas a experiências autênticas e personalizadas, permitindo descobrir destinos de forma única e prática. Nossa missão é transformar cada viagem em memórias inesquecíveis.
      </p>
    </section>


      <p class="lead">
        Cada rota é pensada para você explorar o lado mais verdadeiro de cada destino, com guias locais apaixonados e aventuras exclusivas.
      </p>
    </section>

    <div class="cta" style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
      <a href="contato.php" class="btn primary">Fale Conosco</a>
      <a href="explorar.php" class="btn ghost">Explorar</a>
    </div>
  </main>

  <?php include 'templates/footer.php'; ?>

  <script>
    document.getElementById("ano").textContent = new Date().getFullYear();
  </script>
</body>

</html>
