<?php
session_start();
require_once 'db.php';

if(!isset($_SESSION['usuario_id'])){
    header("Location: login.php");
    exit;
}

$busca = $_GET['busca'] ?? '';

$usuario_id = $_SESSION['usuario_id'];

// ---- REMOVER FAVORITO ----
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remover_favorito'])) {

    $id_tour = intval($_POST['id_tour']);

    $stmt = $conn->prepare("DELETE FROM favoritos WHERE id_turista = ? AND id_tour = ?");
    $stmt->bind_param("ii", $usuario_id, $id_tour);

    if ($stmt->execute()) {
        header("Location: meus_favoritos.php?msg=removed");
        exit;
    } else {
        echo "Erro ao remover favorito: " . $conn->error;
    }

    $stmt->close();
}

// ---- BUSCAR FAVORITOS DO USUÁRIO ----
$sql = "SELECT t.id, t.titulo, t.descricao, t.imagem, t.cidade, t.preco
        FROM favoritos f
        JOIN tours t ON f.id_tour = t.id
        WHERE f.id_turista = $usuario_id
        ORDER BY f.criado_em DESC";

$result = $conn->query($sql);
$favoritos = $result->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Meus Favoritos – Nookway</title>
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

/* Contêiner do grid */
.dashboard-items {
  display: grid; 
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); /* Colunas automáticas */
  gap: 20px;  /* Espaçamento entre as caixas */
  padding: 20px;  /* Espaçamento interno */
}

/* Cada item dentro do grid (as caixinhas de favorito) */
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

/* Estilo para a imagem do tour dentro da caixinha */
.dashboard-item img {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-radius: 8px;
}

/* Estilo para o preço e informações adicionais dentro da caixa */
.dashboard-item p {
  margin: 5px 0;
}

/* Ajustes para mobile (quando os favoritos devem se empilhar) */
@media (max-width: 860px) {
  .dashboard-items {
    grid-template-columns: 1fr; /* No mobile, empilha todos os favoritos */
  }
}


</style>

<body>

<?php include 'templates/header.php'; ?>

<main class="container auth-page">
<div class="auth-box">

<h1>Meus Favoritos</h1>

<form id="form-busca" class="auth-form" method="GET" style="margin-bottom:12px;">
  <label for="busca">Buscar Tour</label>
  <input type="text" id="busca" name="busca" value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>" placeholder="" style="margin-top:8px;">
  <button type="submit" class="btn primary" style="width:100%;">Buscar</button>
</form>

<form id="form-voltar" method="GET" style="margin-bottom:12px;">
    <button type="submit" class="btn ghost" style="width:100%;">Voltar</button>
</form>



<?php if(isset($_GET['msg']) && $_GET['msg'] === "removed"): ?>
    <p class="success">Favorito removido com sucesso!</p>
<?php endif; ?>

<?php if(count($favoritos) == 0): ?>
    <p class="muted">Você ainda não favoritou nenhuma tour.</p>
<?php else: ?>
    <!-- Contêiner de grid para favoritos -->
<div id="lista" class="dashboard-items">
    <?php foreach($favoritos as $f): ?>
        <?php 
        if ($busca !== '' && stripos($f['titulo'].' '.$f['cidade'].' '.$f['descricao'], $busca) === false) {
            continue; // pula este favorito
        }
        ?>
        <div class="dashboard-item card-item">

        
                <!-- Imagem -->
                <?php if(!empty($f['imagem'])): ?>
                    <img src="uploads/tours/<?= htmlspecialchars($f['imagem']) ?>" class="img-tour" alt="<?= htmlspecialchars($f['titulo']) ?>">
                <?php endif; ?>

                <!-- Título -->
                <h3><?= htmlspecialchars($f['titulo']) ?></h3>

                <!-- Descrição -->
                <p><?= htmlspecialchars($f['descricao']) ?></p>

                <!-- Cidade e preço -->
                <p><strong>Cidade:</strong> <?= htmlspecialchars($f['cidade']) ?></p>
                <p><strong>Preço:</strong> € <?= number_format($f['preco'],2,',','.') ?></p>

                <!-- Botões -->
                <p>
                    <a href="tour.php?id=<?= $f['id'] ?>" class="btn primary">Ver tour</a>
                    <br>


                    <form action="" method="POST" style="display:inline;">
                        <input type="hidden" name="id_tour" value="<?= $f['id'] ?>">
                        <button type="submit" name="remover_favorito" class="btn ghost">
                            Remover
                        </button>
                    </form>
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
    const items = document.querySelectorAll('#lista .dashboard-item'); // 
    items.forEach(item => {
        const texto = item.innerText.toLowerCase();
        item.style.display = q === '' || texto.includes(q) ? '' : 'none';
    });
});

</script>


<?php include 'templates/footer.php'; ?>

</body>
</html>
