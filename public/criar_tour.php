<?php
session_start();
require_once 'db.php';

// Verifica login
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

// Verifica se é guia
if ($_SESSION['usuario_tipo'] !== 'guia') {
    header("Location: index.php");
    exit;
}

$msg = "";

// Dias da semana
$dias_semana_opcoes = [
    0 => "Domingo", 1 => "Segunda-feira", 2 => "Terça-feira",
    3 => "Quarta-feira", 4 => "Quinta-feira", 5 => "Sexta-feira", 6 => "Sábado"
];

$tour_horarios = $_POST['horarios'] ?? [];
if (!is_array($tour_horarios)) $tour_horarios = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo       = trim($_POST['titulo']);
    $descricao    = trim($_POST['descricao']);
    $cidade       = trim($_POST['cidade']);
    $categoria    = trim($_POST['categoria']);
    $preco        = floatval($_POST['preco']);
    $latitude     = floatval($_POST['latitude'] ?? 0);
    $longitude    = floatval($_POST['longitude'] ?? 0);
    $data_inicio  = $_POST['data_inicio'] ?? null;
    if ($data_inicio < date('Y-m-d')) {
        $msg = "Não é permitido criar tours em datas passadas.";
    }
    $data_fim     = $_POST['data_fim'] ?? null;
    $dias_semana  = $_POST['dias_semana'] ?? [];
    $horarios_arr = $_POST['horarios'] ?? [];
    $horarios_arr = array_filter(array_map('trim', $horarios_arr));
    $horarios_str = implode(',', $horarios_arr);

    if (!$horarios_arr) {
        $msg = "Informe pelo menos um horário válido para a tour.";
    }

    $id_guia      = $_SESSION['id'];

    // ---------- UPLOAD DA IMAGEM ----------
    $imagem_nome = null;

    if (!empty($_FILES['imagem']['name'])) {
        $ext = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $permitidas)) {
            $imagem_nome = uniqid('tour_') . '.' . $ext;
            $destino = 'uploads/tours/' . $imagem_nome;

            if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
                $msg = "Erro ao fazer upload da imagem.";
            }
        } else {
            $msg = "Formato de imagem inválido.";
        }
    }

    if (!$msg && $titulo && $descricao && $cidade && $categoria &&
        $preco > 0 && $latitude && $longitude && $data_inicio &&
        $dias_semana && $horarios_arr) {

        $dias_semana_str = implode(',', $dias_semana);
        $data_fim_val = $data_fim ?: null;

        $stmt = $conn->prepare("
            INSERT INTO tours 
            (titulo, descricao, cidade, categoria, latitude, longitude, preco,
             data_inicio, data_fim, dias_semana, horarios, imagem, id_guia, criado_em)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        $stmt->bind_param(
            "ssssdddsssssi",
            $titulo,
            $descricao,
            $cidade,
            $categoria,
            $latitude,
            $longitude,
            $preco,
            $data_inicio,
            $data_fim_val,
            $dias_semana_str,
            $horarios_str,
            $imagem_nome,
            $id_guia
        );

        if ($stmt->execute()) {
            header("Location: criar_tour.php?msg=sucesso");
            exit;
        } else {
            $msg = "Erro ao criar a tour.";
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
<title>Criar Tour</title>
<link rel="stylesheet" href="css/nookway.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

</head>
<body>

<?php include 'templates/header.php'; ?>

<main class="container auth-page criar-tour-page"> <!-- Adicionada css específica para o criar tour -->
<div class="auth-box">

<h1>Criar Nova Tour</h1>

<br><br>

<?php if(isset($_GET['msg']) && $_GET['msg'] === 'sucesso'): ?>
<p class="success">Tour criada com sucesso ✅</p>
<?php elseif($msg): ?>
<p class="error"><?= htmlspecialchars($msg) ?></p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

<label>Título</label>
<input type="text" name="titulo" placeholder="Ex: Tour pela Torre Eiffel" required>

<label>Descrição</label>
<textarea name="descricao" placeholder="Ex: Visite os principais pontos turísticos de Paris com guia experiente..." required></textarea>

<label>Localização</label>
<input type="text" name="cidade" placeholder="Ex: Rua de Paris 77" required>

<label>Categoria</label>
<input type="text" name="categoria" placeholder="Ex: Histórico, Natureza, Gastronomia" required>

<label>Preço</label>
<input type="number" name="preco" step="0.01" value="<?= htmlspecialchars($tour['preco']) ?>" required>


<!-- ------------------------------------------------------------

MOEDAS DIFERENTES 


<label>Preço</label>
<div style="display: flex; gap: 10px;">
    <select name="moeda" id="moeda" required style="flex: 0.3;">
        <option value="">Selecione a moeda</option>
        <option value="EUR">€ Euro</option>
        <option value="USD">$ Dólar</option>
        <option value="BRL">R$ Real</option>
        <option value="GBP">£ Libra</option>
        <option value="outro">Outro</option>
    </select>
    <input type="text" id="moeda-customizada" name="moeda_customizada" placeholder="Ex: JPY" style="flex: 0.3; display: none;" class="input-custom">
    <input type="number" name="preco" step="0.01" placeholder="Ex: €" required style="flex: 0.7;" class="input-custom">
</div>
<style>
.input-custom {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    transition: border-color 0.3s;
}

.input-custom:focus {
    border-color: #007bff;
    outline: none;
}
</style>

<script>
document.getElementById('moeda').addEventListener('change', function() {
    const customInput = document.getElementById('moeda-customizada');
    if (this.value === 'outro') {
        customInput.style.display = 'block';
        customInput.required = true;
    } else {
        customInput.style.display = 'none';
        customInput.required = false;
    }
});
</script> -->

<label>Imagem da tour:</label>
<div class="file-input-wrapper">
    <input type="file" name="imagem" accept="image/*" onchange="previewImagem(event)" id="file-input">
    <label for="file-input" class="btn ghost">Adicionar Imagem</label>
</div>

<div class="imagem-tour-preview">
    <img id="preview-img" src="" alt="Preview da imagem">
</div>
 
<style>
#file-input {
    display: none;
}

.file-input-wrapper label {
    display: inline-block;
    cursor: pointer;
}
</style>

<label>Data início</label>
<input type="date" name="data_inicio" required>


<label>Dias disponíveis</label>
<div class="checkbox-grid">
<?php foreach($dias_semana_opcoes as $num => $dia): ?>
<label><input type="checkbox" name="dias_semana[]" value="<?= $num ?>"> <?= $dia ?></label>
<?php endforeach; ?>
</div>

<label>Horários</label>

<button type="button" class="btn ghost" onclick="adicionarHorario()">Adicionar horário</button>

<br>

<div id="horarios-container">
<?php
if (!empty($tour_horarios) && is_array($tour_horarios)) {
    foreach ($tour_horarios as $h) {
        $h = trim($h);
        if ($h === '') continue;
        echo '<div class="horario-bloco">
                <input type="text" name="horarios[]" class="horario-input" value="'.htmlspecialchars($h).'" placeholder="Ex: 10:00">
                <button type="button" class="btn ghost" onclick="removerHorario(this)">❌</button>
              </div>';
    }
}
?>
</div>

<br><br>

<label>Clique no mapa para definir a localização</label>
<div id="map"></div>

<input type="hidden" name="latitude" id="latitude" required>
<input type="hidden" name="longitude" id="longitude" required>

<br><br>
<button class="btn primary">Criar Tour</button>
</form>

</div>
</main>

<?php include 'templates/footer.php'; ?>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
function horarioValido(valor) {
    return /^([01]\d|2[0-3]):([0-5]\d)$/.test(valor);
}

document.querySelector('form').addEventListener('submit', function (e) {
    const inputs = document.querySelectorAll('.horario-input');
    let valido = false;

    for (let input of inputs) {
        if (input.value.trim()) {
            if (!horarioValido(input.value.trim())) {
                alert('Horário inválido: ' + input.value + '. Use HH:MM');
                input.focus();
                e.preventDefault();
                return;
            }
            valido = true;
        }
    }

    if (!valido) {
        alert('Informe pelo menos um horário válido.');
        e.preventDefault();
    }
});

let map = L.map('map').setView([48.8566, 2.3522], 5);
let marker;

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

map.on('click', e => {
    document.getElementById('latitude').value = e.latlng.lat;
    document.getElementById('longitude').value = e.latlng.lng;

    if (marker) marker.setLatLng(e.latlng);
    else marker = L.marker(e.latlng, { draggable:true }).addTo(map)
        .on('dragend', () => {
            let p = marker.getLatLng();
            latitude.value = p.lat;
            longitude.value = p.lng;
        });
});

function adicionarHorario() {
    const container = document.getElementById('horarios-container');
    const inputs = container.querySelectorAll('input.horario-input');
    const ultimo = inputs[inputs.length - 1];

    if (ultimo && !ultimo.value.trim()) {
        alert('Preencha o horário antes de adicionar outro.');
        ultimo.focus();
        return;
    }

    if (ultimo && !horarioValido(ultimo.value.trim())) {
        alert('Formato inválido. Use HH:MM (ex: 09:30)');
        ultimo.focus();
        return;
    }

    const bloco = document.createElement('div');
    bloco.className = 'horario-bloco';

    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'horarios[]';
    input.className = 'horario-input';
    input.placeholder = 'Ex: 14:00';

    const btnRemover = document.createElement('button');
    btnRemover.type = 'button';
    btnRemover.className = 'btn ghost';
    btnRemover.textContent = '❌';
    btnRemover.onclick = function() { removerHorario(btnRemover); };

    bloco.appendChild(input);
    bloco.appendChild(btnRemover);
    container.appendChild(bloco);
}

function removerHorario(btn) {
    const container = document.getElementById('horarios-container');

    if (container.children.length <= 1) {
        alert('A tour precisa ter pelo menos um horário.');
        return;
    }

    btn.parentNode.remove();
}

function previewImagem(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById('preview-img');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}

</script>


</body>
</html>
