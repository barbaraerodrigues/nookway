<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>NOOKWAY - Política de Privacidade</title>
  <link rel="stylesheet" href="css/nookway.css">
</head>

<body>

  <?php include 'templates/header.php'; ?>

  <main class="container" style="padding-block: 32px;">
    <h1 style="margin-bottom: 24px; text-align: center;">Política de Privacidade</h1>

      <p class="lead">
        O NOOKWAY valoriza sua privacidade e se compromete a proteger seus dados pessoais. Todas as informações coletadas são usadas exclusivamente para melhorar a experiência do usuário e oferecer serviços personalizados.
      </p>
    </section>

      <p class="lead">
        Não compartilhamos seus dados com terceiros sem seu consentimento, exceto quando exigido por lei ou para cumprir obrigações legais.
      </p>
    </section>

      <p class="lead">
        Para mais informações sobre como tratamos seus dados ou para exercer seus direitos, entre em contato com nosso suporte.
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
