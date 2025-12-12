<?php
session_start();
require_once 'db.php';

if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit;
}

$busca = $_GET['busca'] ?? '';

if($_SESSION['usuario_tipo'] !== 'guia'){
    header("Location: index.php");
    exit;
}

$id_guia = $_SESSION['id'];
$msg = $_GET['msg'] ?? '';

// ---------------- EXCLUIR TOUR ----------------
if(isset($_GET['excluir'])){
    $id_tour = intval($_GET['excluir']);
    $stmt = $conn->prepare("SELECT * FROM tours WHERE id = ? AND id_guia = ?");
    $stmt->bind_param("ii", $id_tour, $id_guia);
    $stmt->execute();
    $tour = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if($tour){
        $stmt = $conn->prepare("DELETE FROM tours WHERE id = ?");
        $stmt->bind_param("i", $id_tour);
        if($stmt->execute()){
            $msg = "Tour excluída com sucesso!";
        } else {
            $msg = "Erro ao excluir a tour.";
        }
        $stmt->close();
    }
}

// ---------------- BUSCAR TOURS ----------------
$stmt = $conn->prepare("
    SELECT * FROM tours
    WHERE id_guia = ?
    ORDER BY data_inicio DESC
");
$stmt->bind_param("i", $id_guia);
$stmt->execute();
$tours = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Gerenciar Minhas Tours – Nookway</title>
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


/* Contêiner do grid para os tours */
.dashboard-items {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); /* Colunas automáticas */
  gap: 20px;  /* Espaçamento entre as caixinhas */
  padding: 20px;  /* Espaçamento interno */
}

/* Cada item dentro do grid (a caixinha de cada tour) */
.dashboard-item {
  background-color: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  text-align: center;
  height: auto;  /* Altura automática conforme o conteúdo */
}

/* Estilo para o título dentro de cada caixinha */
.dashboard-item h3 {
  font-size: 18px;
  color: var(--primary); /* Ou a cor de sua escolha */
}

/* Estilo para os detalhes da tour dentro da caixinha */
.dashboard-item p {
  margin: 5px 0;
}

/* Ajustes para mobile (quando as tours devem se empilhar) */
@media (max-width: 860px) {
  .dashboard-items {
    grid-template-columns: 1fr; /* No mobile, empilha todas as caixinhas */
  }
}



</style>

<body>
<?php include 'templates/header.php'; ?>

<main class="container auth-page">
<div class="auth-box">

<h1>Minhas Tours</h1>

<form id="form-busca" class="auth-form" method="GET" style="margin-bottom:12px;">
  <label for="busca">Buscar Tour</label>
  <input type="text" id="busca" name="busca" value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>" placeholder="" style="margin-top:8px;">
  <button type="submit" class="btn primary" style="width:100%;">Buscar</button>
</form>

<form id="form-voltar" method="GET" style="margin-bottom:12px;">
    <button type="submit" class="btn ghost" style="width:100%;">Voltar</button>
</form>




<?php if($msg): ?>
<p class="<?= strpos($msg,'sucesso')!==false || strpos($msg,'excluída')!==false ? 'success' : 'error' ?>">
<?= htmlspecialchars($msg) ?></p>
<?php endif; ?>

<?php if(count($tours) == 0): ?>
    <p class="muted">Você ainda não criou nenhuma tour.</p>
<?php else: ?>
    <!-- Contêiner de grid para tours -->
    <div id="lista" class="dashboard-items">
        <?php foreach($tours as $t): ?>
         <?php 
         if ($busca !== '' && stripos($t['titulo'].' '.$t['cidade'].' '.$t['descricao'], $busca) === false) {
    continue; // pula este tour
}
?>         
            <div class="dashboard-item card-item">

 
            <!-- Imagem -->
                <?php if(!empty($t['imagem'])): ?>
                 <img src="uploads/tours/<?= htmlspecialchars($t['imagem']) ?>" class="img-tour" alt="<?= htmlspecialchars($t['titulo']) ?>">
                <?php endif; ?>


                <!-- Título da tour -->
                <h3><?= htmlspecialchars($t['titulo']) ?></h3>

                <!-- Detalhes da tour --> 
                <p><?= htmlspecialchars($t['cidade']) ?> — <?= date('d/m/Y', strtotime($t['data_inicio'])) ?><?php if($t['data_fim']) echo ' até ' . date('d/m/Y', strtotime($t['data_fim'])); ?></p>
                <p>Preço: € <?= number_format($t['preco'], 2, ',', '.') ?></p>
                <p>Status: <?= strtotime($t['data_inicio']) < time() ? 'Ativa' : 'Ativa' ?></p>

                <!-- Botões de ação -->
                <p>
                    <br><br>
                    <a href="editar_tour.php?id=<?= $t['id'] ?>" class="btn primary">Editar</a>
                    <br><br>
                    <a href="?excluir=<?= $t['id'] ?>" class="btn ghost" onclick="return confirm('Deseja realmente excluir esta tour?')">Excluir</a>
                </p>
            </div> 
        <?php endforeach; ?>
    </div>
<?php endif; ?>


</div>
</main>

<script>

const buscaInput = document.getElementById('busca');
buscaInput.value = "<?= htmlspecialchars($busca) ?>";

buscaInput.addEventListener('input', function() {
    const q = this.value.trim().toLowerCase();
    const items = document.querySelectorAll('#lista .card-item'); // substituir pelo nome da classe de cada card
    items.forEach(item => {
        const texto = item.innerText.toLowerCase();
        item.style.display = q === '' || texto.includes(q) ? '' : 'none';
    });
});

</script>





<?php include 'templates/footer.php'; ?>
</body>
</html>
