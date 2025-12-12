<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>NOOKWAY - Termos de Uso</title>
  <link rel="stylesheet" href="css/nookway.css">
</head>

<body>

  <?php include 'templates/header.php'; ?>

  <main class="container" style="padding-block: 32px;">
    <h1 style="margin-bottom: 24px; text-align: center;">Termos de Uso</h1>


      <p class="lead">
        Ao utilizar o NOOKWAY, você concorda em seguir nossas regras e diretrizes. Nossos serviços são oferecidos de forma transparente e esperamos que todos os usuários respeitem as normas para garantir uma experiência segura e agradável.
      </p>
    </section>


      <p class="lead">
        É proibido utilizar a plataforma para fins ilegais, compartilhar conteúdo impróprio ou prejudicar outros usuários.
      </p>
    </section>

      <p class="lead">
        O NOOKWAY se reserva o direito de alterar estes termos a qualquer momento. Sempre que houver mudanças significativas, notificaremos os usuários.
      </p>
    </section>

    <div class="cta" style="display: flex; gap: 16px; justify-content: center; margin-top: 16px;">
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
