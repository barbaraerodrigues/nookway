<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$busca = $_GET['busca'] ?? '';

if ($_SESSION['usuario_tipo'] !== 'guia') {
    header("Location: index.php");
    exit;
}

$id_guia = $_SESSION['id'];
$msg = "";

// Atualiza status da reserva
if (isset($_GET['acao'], $_GET['id'])) {
    $acao = $_GET['acao'];
    $id_reserva = intval($_GET['id']);

    if (in_array($acao, ['confirmada', 'cancelada'])) {
        $stmt = $conn->prepare("UPDATE reservas r
            JOIN tours t ON r.id_tour = t.id
            SET r.status = ?
            WHERE r.id = ? AND t.id_guia = ?");
        $stmt->bind_param("sii", $acao, $id_reserva, $id_guia);
        $stmt->execute();
        $stmt->close();
        $msg = $acao === 'confirmada' ? "Reserva confirmada!" : "Reserva cancelada!";
    }
}
 

// ---------------- APAGAR RESERVA ----------------
if(isset($_GET['apagar'])){
    $id_reserva = intval($_GET['apagar']);

    // Só permite apagar se a reserva for de uma tour do guia e estiver cancelada
    $stmt = $conn->prepare("
        DELETE r FROM reservas r
        JOIN tours t ON r.id_tour = t.id
        WHERE r.id = ? AND t.id_guia = ? AND r.status='cancelada'
    "); 
    $stmt->bind_param("ii", $id_reserva, $id_guia);
    $stmt->execute();
    $stmt->close();

    header("Location: reservas_recebidas.php?msg=" . urlencode("Reserva apagada com sucesso!"));
    exit;
}




// Pega todas as reservas das tours do guia
$stmt = $conn->prepare("
    SELECT r.*, t.titulo, t.cidade, t.preco, t.data_inicio, t.imagem, u.nome AS turista_nome, u.id AS turista_id
    FROM reservas r
    JOIN tours t ON r.id_tour = t.id
    JOIN usuarios u ON r.id_turista = u.id
    WHERE t.id_guia = ?
    ORDER BY r.data_reservada DESC
");
$stmt->bind_param("i", $id_guia);
$stmt->execute();
$reservas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>





<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reservas Recebidas – Nookway</title>
<link rel="stylesheet" href="css/nookway.css">
<style>
.codigo { font-weight: bold; color: #000; background: #f0f0f0; padding: 2px 5px; border-radius: 4px; }
</style>
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

/* Contêiner de grid para as reservas */
.dashboard-items {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); /* Colunas automáticas */
  gap: 20px;  /* Espaçamento entre as caixinhas */
  padding: 20px;  /* Espaçamento interno */
}

/* Cada item dentro do grid (a caixinha de cada reserva) */
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

/* Ajustes para mobile (quando as reservas devem se empilhar) */
@media (max-width: 860px) {
  .dashboard-items {
    grid-template-columns: 1fr; /* No mobile, empilha todas as caixinhas */
  }
}

/* Estilo para a imagem do tour dentro da caixinha */
.dashboard-item img {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-radius: 8px;
}



</style>

<body>

<?php include 'templates/header.php'; ?>

<main class="container auth-page">
<div class="auth-box">





<h1>Reservas Recebidas</h1>


<form id="form-busca" class="auth-form" method="GET" style="margin-bottom:12px;">
  <label for="busca">Buscar tour</label>
  <input type="text" id="busca" name="busca" value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>" placeholder="" style="margin-top:8px;">
  <button type="submit" class="btn primary" style="width:100%;">Buscar</button>
</form>

<form id="form-voltar" method="GET" style="margin-bottom:12px;">
    <button type="submit" class="btn ghost" style="width:100%;">Voltar</button>
</form>



<?php if ($msg): ?>
<p class="<?= strpos($msg,'confirmada')!==false || strpos($msg,'cancelada')!==false ? 'success' : 'error' ?>">
<?= htmlspecialchars($msg) ?></p>
<?php endif; ?>

<?php if (count($reservas) === 0): ?>
    <p class="muted">Nenhuma reserva recebida ainda.</p>
<?php else: ?>
    <!-- Contêiner do grid para as reservas -->
    <div id="lista" class="dashboard-items">
    <?php foreach($reservas as $r): ?>

        <?php
        // Filtro de busca instantâneo (PHP)
        if ($busca !== '' && stripos(
            ($r['titulo'] ?? '') . ' ' . 
            ($r['cidade'] ?? '') . ' ' . 
            ($r['descricao'] ?? ''), 
            $busca
        ) === false) {
            continue; // pula este item se não bater com a busca
        }
        ?>

        <div class="dashboard-item card-item">

            <!-- Imagem -->
                <?php if(!empty($r['imagem'])): ?>
                <img src="uploads/tours/<?= htmlspecialchars($r['imagem']) ?>" class="img-tour" alt="<?= htmlspecialchars($r['titulo']) ?>">
                <?php endif; ?>


                <!-- Título da tour -->
                <h3><?= htmlspecialchars($r['titulo']) ?></h3>

                <!-- Detalhes da reserva -->
                <p><?= htmlspecialchars($r['cidade']) ?> — <?= date('d/m/Y', strtotime($r['data_inicio'])) ?></p>
                <p>Preço: € <?= number_format($r['preco'], 2, ',', '.') ?></p>

                <p><b>Turista:</b> <a href="ver_perfil.php?id=<?= $r['id_turista'] ?>"><?= htmlspecialchars($r['turista_nome']) ?></a></p>
                <p>Quantidade de pessoas: <?= intval($r['quantidade_pessoas']) ?></p>
                <p>Data reservada: <?= date('d/m/Y H:i', strtotime($r['data_reservada'])) ?></p>
                <p>Status: <?= ucfirst(htmlspecialchars($r['status'])) ?></p>
                
                <?php if ($r['codigo_confirmacao']): ?>
                    <p>Código de confirmação: <span class="codigo"><?= htmlspecialchars($r['codigo_confirmacao']) ?></span></p>
                <?php endif; ?>

                <!-- Ações de confirmação ou cancelamento -->
                <?php if ($r['status'] !== 'cancelada'): ?>
                    <p>
                        <?php if ($r['status'] === 'pendente'): ?>
                            <a href="?acao=confirmada&id=<?= $r['id'] ?>" class="btn primary" onclick="return confirm('Confirmar esta reserva?')">Confirmar</a>
                        <?php endif; ?>
                        <br><br>
                        <a href="?acao=cancelada&id=<?= $r['id'] ?>" class="btn ghost" onclick="return confirm('Cancelar esta reserva?')">Cancelar</a>
                    </p>
                <?php endif; ?>

                        <?php if($r['status'] === 'cancelada'): ?>
                        <a href="?apagar=<?= $r['id'] ?>" class="btn ghost" onclick="return confirm('Deseja realmente apagar esta reserva?')">
                        Apagar
                        </a> 
                    <?php endif; ?>

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
