<?php
session_start();
require_once 'db.php';

// Impede acesso sem login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Busca informações do usuário
$stmt = $conn->prepare("SELECT nome, email, telefone, bio, data_nascimento, foto_perfil FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    die("Erro ao carregar informações do usuário.");
}

// Se não tiver imagem, usa padrão
$foto = !empty($usuario['foto_perfil']) ? "uploads/usuarios/" . $usuario['foto_perfil'] : "img/user_default.png";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Meu Perfil - Nookway</title>
<link rel="stylesheet" href="css/nookway.css">
</head>


<style>
.auth-box {
  width: 100%;  /* Ocupa toda a largura disponível */
  max-width: 1400px;  /* Define o limite máximo da largura da caixa (ajustar conforme necessário) */
  padding: 30px;
  background-color: white;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

/* ABAS */
.tabs {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 16px;
}

.tab-btn {
  padding: 10px 20px;
  border: 1px solid #ddd;
  background: #fff5f5;
  border-radius: 12px 12px 0 0;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.tab-btn:hover {
  background: #fff0f0;
}

.tab-btn.active {
  background: var(--primary);
  color: #fff;
  border-color: var(--primary);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* CONTEÚDO DAS ABAS */
.tab {
  display: none;
  background: #ffffff;
  padding: 20px;
  border-radius: 0 12px 12px 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
  margin-top: -1px;
  animation: fadeIn 0.3s ease-in-out;
}

.tab.active {
  display: block;
}

/* Animação suave ao trocar de aba */
@keyframes fadeIn {
  from {opacity: 0; transform: translateY(10px);}
  to {opacity: 1; transform: translateY(0);}
}

/* Formulários dentro das abas */
.auth-form label {
  display: block;
  margin-top: 12px;
  font-weight: 500;
}

.auth-form input,
.auth-form textarea {
  width: 100%;
  padding: 10px;
  margin-top: 6px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 14px;
  box-sizing: border-box;
}

.auth-form button {
  margin-top: 16px;
  border-radius: 12px;
}

/* Foto de perfil */
.foto-area {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 16px;
}

.foto-perfil-preview {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #ddd;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

/* Responsividade */
@media (max-width: 860px) {
  .tabs {
    flex-direction: column;
  }

  .tab-btn {
    width: 100%;
  }
}


</style>

<body>
<?php include 'templates/header.php'; ?>

<main class="container auth-page">
  <div class="auth-box">

    <h1>Meu Perfil</h1>
    <p class="muted">Gerencie suas informações e personalize sua experiência.</p>

    <!-- ABAS -->
    <div class="tabs">
        <button class="tab-btn active" data-target="tab-info">Informações</button>
        <button class="tab-btn" data-target="tab-foto">Foto de Perfil</button>
        <button class="tab-btn" data-target="tab-config">Configurações</button>
    </div>

    <div class="tabs-content">

      <!-- Aba 1: Informações -->
      <div class="tab active" id="tab-info">
        <form action="atualizar_perfil.php" method="post" class="auth-form">
          <input type="hidden" name="acao" value="info">

          <label>Nome Completo</label>
          <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>

          <label>E-mail</label>
          <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>

          <label>Telefone</label>
          <input type="text" name="telefone" value="<?= htmlspecialchars($usuario['telefone'] ?? '') ?>">

          <label>Data de Nascimento</label>
          <input type="date" name="data_nascimento" value="<?= htmlspecialchars($usuario['data_nascimento'] ?? '') ?>">

          <label>Biografia</label>
          <textarea name="bio" rows="4" placeholder="Fale um pouco sobre você..."><?= htmlspecialchars($usuario['bio'] ?? '') ?></textarea>

          <button type="submit" class="btn primary" style="width:100%;">Salvar</button>
        </form>
      </div>

      <!-- Aba 2: Foto -->
      <div class="tab" id="tab-foto">
        <div class="foto-area" style="text-align:center; margin-bottom:16px;">
          <img src="<?= $foto ?>" alt="Foto de Perfil" class="foto-perfil-preview" style="width:120px; height:120px; border-radius:50%;">
        </div>

        <form action="atualizar_perfil.php" method="post" enctype="multipart/form-data" class="auth-form">
          <input type="hidden" name="acao" value="foto">

          <label>Trocar foto</label>
          <input type="file" name="foto" accept="image/*" required>

          <button type="submit" class="btn primary" style="width:100%;">Atualizar Foto</button>
        </form>
      </div>

      <!-- Aba 3: Configurações -->
      <div class="tab" id="tab-config">
        <form action="atualizar_perfil.php" method="post" class="auth-form">
          <input type="hidden" name="acao" value="senha">

          <label>Senha Atual</label>
          <input type="password" name="senha_atual">

          <label>Nova Senha</label>
          <input type="password" name="nova_senha">

          <label>Confirmar Nova Senha</label>
          <input type="password" name="confirmar_senha">

          <button type="submit" class="btn primary" style="width:100%;">Alterar Senha</button>
        </form>

        <hr style="margin:20px 0;">

        <form action="atualizar_perfil.php" method="post">
          <input type="hidden" name="acao" value="deletar">
          <button class="btn danger" style="width:100%;" onclick="return confirm('Tem certeza que deseja excluir sua conta?')">Excluir Minha Conta</button>
        </form>
      </div>

    </div>
  </div>
</main>

<script>
// Lógica das abas
const buttons = document.querySelectorAll('.tab-btn');
const tabs = document.querySelectorAll('.tab');

buttons.forEach(btn => {
  btn.addEventListener('click', () => {
    buttons.forEach(b => b.classList.remove('active'));
    tabs.forEach(t => t.classList.remove('active'));

    btn.classList.add('active');
    document.getElementById(btn.dataset.target).classList.add('active');
  });
});
</script>

<?php include 'templates/footer.php'; ?>
</body>
</html>
