<?php
session_start();
require_once 'db.php';

if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit;
}

$busca = $_GET['busca'] ?? '';

$id_usuario = $_SESSION['id'];
$msg = $_GET['msg'] ?? '';
$codigo_novo = $_GET['codigo'] ?? '';

// ---------------- CANCELAR RESERVA ----------------
if(isset($_GET['cancelar'])){
    $id_reserva = intval($_GET['cancelar']);

    $stmt = $conn->prepare("
        SELECT * FROM reservas 
        WHERE id = ? AND id_turista = ? AND status IN ('pendente','confirmada')
    ");
    $stmt->bind_param("ii", $id_reserva, $id_usuario);
    $stmt->execute();
    $reserva = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if($reserva){
        $stmt = $conn->prepare("UPDATE reservas SET status='cancelada' WHERE id=?");
        $stmt->bind_param("i", $id_reserva);
        $stmt->execute();
        $stmt->close();

        header("Location: minhas_reservas.php?msg=" . urlencode("Reserva cancelada com sucesso!"));
        exit;
    }
}

// ---------------- APAGAR RESERVA ----------------
if(isset($_GET['apagar'])){
    $id_reserva = intval($_GET['apagar']);

    $stmt = $conn->prepare("DELETE FROM reservas WHERE id = ? AND id_turista = ? AND status='cancelada'");
    $stmt->bind_param("ii", $id_reserva, $id_usuario);
    $stmt->execute();
    $stmt->close();

    header("Location: minhas_reservas.php?msg=" . urlencode("Reserva apagada com sucesso!"));
    exit;
}

 
// ---------------- BUSCAR RESERVAS ----------------
$stmt = $conn->prepare("
    SELECT 
        r.id,
        r.status,
        r.quantidade_pessoas,
        r.data_reservada,
        r.data_reserva,
        r.codigo_confirmacao,
        t.id AS id_tour,
        t.titulo,
        t.cidade,
        t.preco,
        t.imagem, 
        t.id_guia,
        u.nome AS guia_nome
    FROM reservas r
    JOIN tours t ON r.id_tour = t.id
    JOIN usuarios u ON t.id_guia = u.id
    WHERE r.id_turista = ?
    ORDER BY r.data_reservada DESC
");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$reservas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head> 
<meta charset="UTF-8">
<title>Minhas Reservas</title>
<link rel="stylesheet" href="css/nookway.css">
<style>
.success { color: green; }
.error { color: red; }
.codigo { font-weight: bold; color: #000; background: #f0f0f0; padding: 3px 6px; border-radius: 4px; }
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



/* Contêiner do grid */
.dashboard-items {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); /* Colunas automáticas */
  gap: 20px;  /* Espaçamento entre os itens */
  padding: 20px;  /* Espaçamento interno */
}

/* Cada item dentro do grid (as caixinhas de reserva) */
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

/* Estilo para o preço e informações adicionais dentro da caixa */
.dashboard-item p {
  margin: 5px 0;
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

<h1>Minhas Reservas</h1>


<form id="form-busca" class="auth-form" method="GET" style="margin-bottom:12px;">
  <label for="busca">Buscar tour</label>
  <input type="text" id="busca" name="busca" value="<?= htmlspecialchars($_GET['busca'] ?? '') ?>" placeholder="" style="margin-top:8px;">
  <button type="submit" class="btn primary" style="width:100%;">Buscar</button>
</form>

<form id="form-voltar" method="GET" style="margin-bottom:12px;">
    <button type="submit" class="btn ghost" style="width:100%;">Voltar</button>
</form>



<?php if($msg): ?>
<p class="<?= strpos($msg,'sucesso') !== false ? 'success' : 'error' ?>">
    <?= htmlspecialchars($msg) ?>
</p>
<?php endif; ?>

<?php if($codigo_novo): ?>
<p class="success">Código: <span class="codigo"><?= htmlspecialchars($codigo_novo) ?></span></p>
<?php endif; ?>








<?php if(count($reservas) === 0): ?>
    <p class="muted">Você ainda não fez nenhuma reserva.</p>
<?php else: ?>
    <!-- Contêiner de grid -->
  
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

            <?php if(!empty($r['imagem'])): ?>
            <img src="uploads/tours/<?= htmlspecialchars($r['imagem']) ?>" class="img-tour" alt="<?= htmlspecialchars($r['titulo']) ?>">
            <?php endif; ?>


                <!-- Cada item de reserva -->
                <h3><?= htmlspecialchars($r['titulo']) ?></h3>
                <p class="muted"><?= htmlspecialchars($r['cidade']) ?></p>

                <p><b>Data reservada:</b> <?= date('d/m/Y H:i', strtotime($r['data_reservada'])) ?></p>
                <p><b>Preço unitário:</b> € <?= number_format($r['preco'], 2, ',', '.') ?></p>
                <p><b>Quantidade:</b> <?= (int)$r['quantidade_pessoas'] ?> pessoa(s)</p>
                <p><b>Total:</b> € <?= number_format($r['preco'] * $r['quantidade_pessoas'], 2, ',', '.') ?></p>
                <p><b>Status:</b> <?= ucfirst(htmlspecialchars($r['status'])) ?></p>

                <?php if($r['codigo_confirmacao']): ?>
                    <p><b>Código de confirmação:</b> <span class="codigo"><?= htmlspecialchars($r['codigo_confirmacao']) ?></span></p>
                <?php endif; ?>

                <p><b>Guia:</b> <a href="ver_perfil.php?id=<?= $r['id_guia'] ?>"><?= htmlspecialchars($r['guia_nome']) ?></a></p>

                <br><br> 

                <a href="tour.php?id=<?= $r['id_tour'] ?>" class="btn primary">Ver tour</a>
                <br>
 
                <?php if($r['status'] === 'pendente' || $r['status'] === 'confirmada'): ?>
                    <a 
                    href="?cancelar=<?= $r['id'] ?>" 
                    class="btn ghost"
                    onclick="return confirm('Deseja realmente cancelar esta reserva?')">
                    Cancelar
                    </a> 
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
