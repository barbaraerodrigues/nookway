<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['usuario_tipo'] !== 'guia') {
    header("Location: index.php");
    exit;
}

$id_guia = $_SESSION['id'];
$id_tour = intval($_GET['id'] ?? 0);

if ($id_tour <= 0) {
    header("Location: gerenciar_tours.php");
    exit; 
}

$msg = "";

$dias_semana_opcoes = [
    0 => "Domingo", 1 => "Segunda-feira", 2 => "Terça-feira",
    3 => "Quarta-feira", 4 => "Quinta-feira", 5 => "Sexta-feira", 6 => "Sábado"
];

/* ---------- BUSCA TOUR ---------- */
$stmt = $conn->prepare("SELECT * FROM tours WHERE id = ? AND id_guia = ?");
$stmt->bind_param("ii", $id_tour, $id_guia);
$stmt->execute();
$tour = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$tour) {
    header("Location: gerenciar_tours.php");
    exit;
}

/* ---------- ESTADO INICIAL ---------- */
$tour_dias_semana = array_filter(explode(',', $tour['dias_semana'] ?? ''));
$tour_horarios = array_filter(array_map('trim', explode(',', $tour['horarios'] ?? '')));

/* ---------- POST ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo      = trim($_POST['titulo']);
    $descricao   = trim($_POST['descricao']);
    $cidade      = trim($_POST['cidade']);
    $categoria   = trim($_POST['categoria']);
    $preco       = floatval($_POST['preco']);
    $latitude    = floatval($_POST['latitude']);
    $longitude   = floatval($_POST['longitude']);
    $data_inicio = $_POST['data_inicio'];
    $data_fim    = $_POST['data_fim'] ?: null;
    $dias_semana = $_POST['dias_semana'] ?? [];

    if ($data_inicio < date('Y-m-d')) {
        $msg = "Não é permitido definir a data de início no passado.";
    }

    $horarios_arr = $_POST['horarios'] ?? [];
    $horarios_arr = array_filter(array_map('trim', $horarios_arr));
    $horarios = implode(',', $horarios_arr);

    if (!$msg && empty($horarios_arr)) {
        $msg = "Informe pelo menos um horário válido.";
    }

    /* ---------- UPLOAD ---------- */
    $imagem_nome = $tour['imagem'];

    if (!empty($_FILES['imagem']['name'])) {
        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg','jpeg','png','webp'];

        if (in_array($ext, $permitidas)) {
            $imagem_nome = uniqid('tour_') . '.' . $ext;
            move_uploaded_file($_FILES['imagem']['tmp_name'], 'uploads/tours/'.$imagem_nome);
        } else {
            $msg = "Formato de imagem inválido.";
        }
    }

    if (
        !$msg &&
        $titulo && $descricao && $cidade && $categoria &&
        $preco > 0 && $latitude && $longitude &&
        $data_inicio && $dias_semana && $horarios
    ) {

        $dias_semana_str = implode(',', $dias_semana);

        $stmt = $conn->prepare("
            UPDATE tours SET
                titulo=?, descricao=?, cidade=?, categoria=?,
                latitude=?, longitude=?, preco=?,
                data_inicio=?, data_fim=?, dias_semana=?, horarios=?, imagem=?
            WHERE id=? AND id_guia=?
        ");

        $stmt->bind_param(
            "ssssdddsssssii",
            $titulo, $descricao, $cidade, $categoria,
            $latitude, $longitude, $preco,
            $data_inicio, $data_fim,
            $dias_semana_str, $horarios, $imagem_nome,
            $id_tour, $id_guia
        );

        if ($stmt->execute()) {
            $msg = "Tour atualizada com sucesso!";
            $tour_dias_semana = $dias_semana;
            $tour_horarios = $horarios_arr;
        } else {
            $msg = "Erro ao atualizar a tour.";
        }

        $stmt->close();
    } elseif (!$msg) {
        $msg = "Preencha todos os campos obrigatórios.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Editar Tour</title>
<link rel="stylesheet" href="css/nookway.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
#map{height:320px;border-radius:12px;margin:10px 0 20px}
.checkbox-grid{display:flex;flex-wrap:wrap;gap:10px}
.preview{max-width:100%;border-radius:12px;margin-bottom:10px}
.horario-bloco{display:flex;gap:8px;margin-bottom:6px}
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
</style>

<body>

<?php include 'templates/header.php'; ?>

<div class="criar-tour-page">
    <div class="auth-box">

<h1>Editar Tour</h1>

<?php if ($msg): ?>
<p class="<?= str_contains($msg,'sucesso') ? 'success' : 'error' ?>">
<?= htmlspecialchars($msg) ?>
</p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

<label>Título</label>
<input type="text" name="titulo" value="<?= htmlspecialchars($tour['titulo']) ?>" required>

<label>Descrição</label>
<textarea name="descricao" required><?= htmlspecialchars($tour['descricao']) ?></textarea>

<label>Localização</label>
<input type="text" name="cidade" value="<?= htmlspecialchars($tour['cidade']) ?>" required>


<label>Categoria</label>
<input type="text" name="categoria" value="<?= htmlspecialchars($tour['categoria']) ?>" required>

<label>Preço</label>
<input type="number" name="preco" step="0.01" value="<?= htmlspecialchars($tour['preco']) ?>" required>


<img class="preview" src="<?= $tour['imagem'] ? 'uploads/tours/'.$tour['imagem'] : 'img/default_tour.jpg' ?>">
<input type="file" name="imagem" accept="image/*">

<input type="date" name="data_inicio" value="<?= $tour['data_inicio'] ?>" required>
<input type="date" name="data_fim" value="<?= $tour['data_fim'] ?>">

<div class="checkbox-grid">
<?php foreach ($dias_semana_opcoes as $n=>$d): ?>
<label>
<input type="checkbox" name="dias_semana[]" value="<?= $n ?>" <?= in_array($n,$tour_dias_semana)?'checked':'' ?>>
<?= $d ?>
</label>
<?php endforeach; ?>
</div>

<label>Horários:</label>
<button type="button" class="btn ghost" onclick="adicionarHorario()">Adicionar horário</button>

<div id="horarios-container">
<?php foreach ($tour_horarios as $h): ?>
<div class="horario-bloco">
<input type="text" name="horarios[]" class="horario-input" value="<?= htmlspecialchars($h) ?>" placeholder="HH:MM">
<button type="button" class="btn ghost" onclick="removerHorario(this)">❌</button>
</div>
<?php endforeach; ?>
</div>

<div id="map"></div>
<input type="hidden" name="latitude" id="latitude" value="<?= $tour['latitude'] ?>">
<input type="hidden" name="longitude" id="longitude" value="<?= $tour['longitude'] ?>">

<button class="btn primary">Atualizar Tour</button>
</form>

</div>
</main>

<?php include 'templates/footer.php'; ?>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
function horarioValido(v){return /^([01]\d|2[0-3]):[0-5]\d$/.test(v)}

function adicionarHorario(){
    const c=document.getElementById('horarios-container')
    const i=c.querySelectorAll('.horario-input')
    const u=i[i.length-1]
    if(u && (!u.value.trim() || !horarioValido(u.value))) { alert('Preencha ou corrija o horário anterior (HH:MM)'); u.focus(); return; }
    c.insertAdjacentHTML('beforeend',
    `<div class="horario-bloco">
        <input type="text" name="horarios[]" class="horario-input" placeholder="HH:MM">
        <button type="button" class="btn ghost" onclick="removerHorario(this)">❌</button>
    </div>`)
}

function removerHorario(b){
    const container=document.getElementById('horarios-container')
    if(container.children.length<=1){ alert('A tour precisa ter pelo menos um horário.'); return; }
    b.parentNode.remove()
}

// Validação do submit
document.querySelector('form').addEventListener('submit', function(e){
    const inputs=document.querySelectorAll('.horario-input')
    let valido=false
    for(let input of inputs){
        if(input.value.trim()){
            if(!horarioValido(input.value.trim())){ alert('Horário inválido: '+input.value+'. Use HH:MM'); input.focus(); e.preventDefault(); return; }
            valido=true
        }
    }
    if(!valido){ alert('Informe pelo menos um horário válido.'); e.preventDefault(); }
})

let map=L.map('map').setView([<?= $tour['latitude'] ?>,<?= $tour['longitude'] ?>],13)
let m=L.marker([<?= $tour['latitude'] ?>,<?= $tour['longitude'] ?>],{draggable:true}).addTo(map)
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map)
m.on('dragend',()=>{latitude.value=m.getLatLng().lat;longitude.value=m.getLatLng().lng})
map.on('click',e=>{m.setLatLng(e.latlng);latitude.value=e.latlng.lat;longitude.value=e.latlng.lng})
</script>

</body>
</html>
