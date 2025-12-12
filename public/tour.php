<?php
session_start();
require_once 'db.php';

/* ------------------- VERIFICA ID DA TOUR ------------------- */
if(!isset($_GET['id'])){
    header('Location: explorar.php');
    exit;
}

$id_tour = intval($_GET['id']);

/* ------------------- BUSCA A TOUR ------------------- */
$stmt = $conn->prepare("SELECT * FROM tours WHERE id = ?");
$stmt->bind_param("i", $id_tour);
$stmt->execute();
$tour = $stmt->get_result()->fetch_assoc();
$stmt->close();

if(!$tour){
    echo "Tour não encontrada!";
    exit;
}

/* ------------------- USUÁRIO LOGADO ------------------- */
$id_usuario = $_SESSION['id'] ?? null;
$tipo_conta = $_SESSION['tipo_conta'] ?? null;

/* ------------------- MENSAGENS ------------------- */
$msg = $_GET['msg'] ?? '';
$msg_type = $_GET['type'] ?? '';

/* ------------------- RESERVAS EXISTENTES ------------------- */
$reserva_status = null;
if($id_usuario && $tipo_conta === 'turista'){
    $stmt = $conn->prepare("
        SELECT status 
        FROM reservas 
        WHERE id_tour = ? AND id_turista = ?
        ORDER BY id DESC
        LIMIT 1
    ");
    $stmt->bind_param("ii", $id_tour, $id_usuario);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if($res){
        $reserva_status = $res['status']; // 'ativa', 'cancelada', etc.
    }
    $stmt->close();
}

/* ------------------- AVALIAÇÕES ------------------- */
$stmt = $conn->prepare("
    SELECT a.*, u.nome
    FROM avaliacoes a
    JOIN usuarios u ON a.id_turista = u.id
    WHERE a.id_tour = ?
    ORDER BY a.id DESC
");
$stmt->bind_param("i", $id_tour);
$stmt->execute();
$avaliacoes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/* ------------------- FAVORITOS ------------------- */
$ja_favoritou = false;
if($id_usuario){
    $stmt = $conn->prepare("SELECT id FROM favoritos WHERE id_tour = ? AND id_turista = ?");
    $stmt->bind_param("ii", $id_tour, $id_usuario);
    $stmt->execute();
    $ja_favoritou = $stmt->get_result()->num_rows > 0;
    $stmt->close();
}

/* ------------------- DATAS / HORÁRIOS ------------------- */
$dias_semana = array_filter(explode(',', $tour['dias_semana']));
$horarios_disponiveis = array_filter(explode(',', $tour['horarios']));

$data_inicio = $tour['data_inicio'];
$data_fim = $tour['data_fim'] ?: date('Y-m-d', strtotime('+1 year')); // Se não tiver data fim, usa +1 ano

// Gera todas as datas disponíveis a partir dos dias da semana
$dias_disponiveis = [];
$current = strtotime($data_inicio);
$end = strtotime($data_fim);

while($current <= $end){
    $w = date('w', $current);
    if(in_array($w, $dias_semana)){
        $dias_disponiveis[] = date('Y-m-d', $current);
    }
    $current = strtotime('+1 day', $current);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($tour['titulo']) ?></title>
<link rel="stylesheet" href="css/nookway.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
.atividade-img{max-width:100%;border-radius:12px;margin-bottom:12px}
.success{color:green}
.error{color:red}
.btn-option{margin:4px}
.btn-option.active{background:var(--primary);color:#000}
.popup-bg{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);display:none;align-items:center;justify-content:center}
.popup{background:#fff;padding:20px;border-radius:12px;width:90%;max-width:350px;text-align:center}



/* Botões de horário */
.horario {
    display: inline-block;
    min-width: 80px;       /* largura mínima fixa */
    padding: 8px 12px;     /* padding consistente */
    margin: 4px;            /* espaço entre botões */
    border-radius: 12px;
    border: 1.5px solid #d9d9d9;
    background: linear-gradient(180deg, var(--bnt ), #ffffffff);
    color: var(--text);
    font-weight: 500;       /* mantém tamanho do texto */
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
    box-sizing: border-box;
    text-align: center;
}

/* Botão selecionado */
.horario.active {
    background-color: var(--primary);
    color: #ffffffff;
    font-weight: 500;       /* mantém igual ao normal */
    border-color: var(--primary);
}

/* Estilos para o container principal */
.auth-box {
  width: 100%;  /* Ocupa toda a largura disponível */
  max-width: 1400px;  /* Define o limite máximo da largura da caixa (ajustar conforme necessário) */
  padding: 30px;
  background-color: white;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}
 

/* Estilos para os cards de avaliacao */
.atividade-card {
    background: #fff;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    transition: transform 0.18s, box-shadow 0.18s;
}

.atividade-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

.atividade-card p {
    margin: 6px 0;
    font-size: 0.95rem;
    color: #333333ff;
}

.atividade-card b {
    color: var(--primary);
}


</style>
</head>


<body>
<?php include 'templates/header.php'; ?>

<div class="criar-tour-page">
    <div class="auth-box">


<div class="tour-img-container">
    <img src="<?= $tour['imagem'] ? 'uploads/tours/'.$tour['imagem'] : 'img/default_tour.jpg' ?>" class="tour-img">
</div>



<h1><?= htmlspecialchars($tour['titulo']) ?></h1>
<p class="muted"><?= htmlspecialchars($tour['cidade']) ?> — <?= htmlspecialchars($tour['categoria']) ?></p>

<p><?= nl2br(htmlspecialchars($tour['descricao'])) ?></p>
<p><b>Preço:</b> € <?= number_format($tour['preco'],2,',','.') ?></p>

<?php if($msg): ?>
<p class="<?= $msg_type === 'success' ? 'success' : 'error' ?>"><?= htmlspecialchars($msg) ?></p>
<?php endif; ?>

<?php if($tipo_conta === 'turista'): ?>

<form method="post" action="favoritar.php" style="display:inline-block;margin-right:8px">
<input type="hidden" name="id_tour" value="<?= $id_tour ?>">
<?php if(!$ja_favoritou): ?>
<button class="btn ghost">Favoritar ❤️</button>
<?php else: ?>
<button class="btn ghost" name="desfavoritar" value="1">Remover Favorito ❌</button>
<?php endif; ?>
</form>


<form method="POST" action="reservar.php">
<input type="hidden" name="id_tour" value="<?= $id_tour ?>">
<input type="hidden" name="dia_escolhido" id="dia_escolhido">
<input type="hidden" name="horario" id="horario">

<h3>Escolha o dia</h3>
<input type="text" id="calendario" placeholder="Selecione a data" readonly style="padding:8px;border-radius:6px;border:1px solid #ccc;width:200px">

<h3>Escolha o horário</h3>
<div id="horarios-container">
    <?php foreach($horarios_disponiveis as $h): ?>
    <button type="button" class="horario" onclick="selecionarHorario(this,'<?= $h ?>')"><?= $h ?></button>
    <?php endforeach; ?>
</div>

<br><br>

<label>Quantidade de pessoas</label>
<input type="number" name="quantidade_pessoas" min="1" value="1" required>

<br><br>
<button class="btn primary">Reservar</button>
</form>

<br><br>
<a href="minhas_reservas.php" class="btn primary">Ver minhas reservas</a>
<br><br>
<a href="meus_favoritos.php" class="btn primary">Ver meus favoritos</a>

<h3 style="margin-top:20px">Avaliar</h3>
<form method="post" action="avaliar.php">
<input type="hidden" name="id_tour" value="<?= $id_tour ?>">
<select name="nota" required>
<option value="5">5 ⭐</option>
<option value="4">4 ⭐</option> 
<option value="3">3 ⭐</option>
<option value="2">2 ⭐</option>
<option value="1">1 ⭐</option>
</select>
<textarea name="comentario" required></textarea>
<button class="btn primary">Enviar Avaliação</button>
</form>

<?php else: ?>
<button class="btn primary" onclick="abrirPopup()">Reservar</button>
<?php endif; ?>

<h3 style="margin-top:30px">Avaliações</h3>


<?php if(!$avaliacoes): ?>
<p class="muted">Nenhuma avaliação ainda.</p>
<?php else: ?>
<div class="dashboard-items"> <!-- cria um grid para os cards -->
<?php foreach($avaliacoes as $a): ?>
    <div class="atividade-card">
        <p><b><?= htmlspecialchars($a['nome']) ?></b> — <?= (int)$a['nota'] ?> ⭐</p>
        <p><?= nl2br(htmlspecialchars($a['comentario'])) ?></p>
    </div>
<?php endforeach; ?>
</div>
<?php endif; ?>


</div>
</main>

<div class="popup-bg" id="popup-login">
<div class="popup">
<h2>Faça login</h2>
<p>Entre ou crie uma conta para reservar.</p>
<a href="login.php" class="btn primary">Entrar</a>
<a href="cadastro_usuario.php" class="btn primary">Criar conta</a>
<br><br>
<button class="btn ghost" onclick="fecharPopup()">Fechar</button>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
const diasDisponiveis = <?= json_encode($dias_disponiveis) ?>;

flatpickr("#calendario", {
    dateFormat: "Y-m-d",
    enable: diasDisponiveis,
    onChange: function(selectedDates, dateStr){
        document.getElementById('dia_escolhido').value = dateStr;
    }
});

function selecionarHorario(btn, h) {
    document.getElementById('horario').value = h;

    // Remove 'active' de todos os botões
    document.querySelectorAll('.horario').forEach(b => b.classList.remove('active'));

    // Adiciona 'active' ao botão clicado
    btn.classList.add('active');
}


function abrirPopup(){document.getElementById('popup-login').style.display='flex'}
function fecharPopup(){document.getElementById('popup-login').style.display='none'}
</script>

</body>
</html> 
