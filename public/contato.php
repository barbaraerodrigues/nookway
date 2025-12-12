<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>NOOKWAY - Contato</title>
  <link rel="stylesheet" href="css/nookway.css">
</head>

<body>

  <?php include 'templates/header.php'; ?>

  <main class="container auth-page">
    
    <div class="auth-box">
      <h1>Fale Conosco</h1>
      <p class="muted center">Tem dúvidas, sugestões ou quer falar diretamente com a equipe do NOOKWAY? Use contatos rápidos.</p>
      

      <?php /*

      <p class="muted center">Tem dúvidas, sugestões ou quer falar diretamente com a equipe do NOOKWAY? Use o formulário abaixo ou os contatos rápidos.</p>


        <form action="enviar_contato.php" method="post" class="auth-form">
          <label for="nome">Nome completo</label>
          <input type="text" id="nome" name="nome" placeholder="Seu nome" required>

          <label for="email">E-mail</label>
          <input type="email" id="email" name="email" placeholder="seu@email.com" required>

          <label for="assunto">Assunto</label>
          <input type="text" id="assunto" name="assunto" placeholder="Assunto" required>

       
          <label for="mensagem">Mensagem</label>
          <textarea id="mensagem" name="mensagem" placeholder="Sua Mensagem" rows="3"></textarea>



          <button type="submit" class="btn primary" style="width:100%;">Enviar Mensagem</button>
        </form>

          <h5 class="muted center">OU</h5> 

          <a href="mailto:noookway@gmail.com?subject=Contato&body=Olá!" class="btn ghost">noookway@gmail.com</a>
      */ ?>


      <div class="cta" style="display: flex; gap: 16px; justify-content: center; margin-top: 16px; flex-direction: column; align-items: center;">
        <a href="tel:+5531000000000000" class="btn ghost" >+55 (31) 0000-0000</a>
        
        <a href="https://mail.google.com/mail/?view=cm&fs=1&to=noookway@gmail.com&su=Contato&body=Olá" class="btn ghost" target="_blank"> noookway@gmail.com</a>

      </div>


    </div>

  </main>

  <?php include 'templates/footer.php'; ?>

  <script>
    document.getElementById("ano").textContent = new Date().getFullYear();
  </script>
</body>
</html>
