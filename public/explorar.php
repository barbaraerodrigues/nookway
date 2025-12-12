
<?php

session_start();
require_once 'db.php';

// Busca todas as tours
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$query = "SELECT * FROM tours";

if ($busca !== '') {
    $busca_escaped = $conn->real_escape_string($busca);
    $query .= " WHERE titulo LIKE '%$busca_escaped%' OR cidade LIKE '%$busca_escaped%' OR categoria LIKE '%$busca_escaped%' OR descricao LIKE '%$busca_escaped%'";
}

$query .= " ORDER BY criado_em DESC";
$res = $conn->query($query);
$atividades = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

// Monta array para o JS do mapa (evita problemas com escaping)
$markers = [];
function simbolo_moeda($codigo) {
    $map = [
        'EUR' => '€',
        'USD' => '$', 
        'BRL' => 'R$',
        'GBP' => '£'
    ];
    return $map[$codigo] ?? $codigo; // se for custom (JPY) retorna JPY
}



foreach ($atividades as $a) {
    $lat = $a['latitude'] ?? null;
    $lng = $a['longitude'] ?? null;
    if ($lat && $lng) {
        $markers[] = [
            'id' => $a['id'],
            'titulo' => $a['titulo'],
            'cidade' => $a['cidade'],
            'preco' => $a['preco'],
            'moeda' => $a['moeda'] ?? ($a['moeda_customizada'] ?? ''),
            'lat' => (float)$lat,
            'lng' => (float)$lng,
            'imagem' => $a['imagem'] ? ('uploads/tours/' . $a['imagem']) : null
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Explorar – Nookway</title>
  <link rel="stylesheet" href="css/nookway.css">

  <!-- Leaflet --> 
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

  <style>
    .tabs { display:flex; gap:12px; margin-top:12px; }
    .tab-btn {
      padding:10px 14px; border-radius:999px; border: 1px solid rgba(0,0,0,0.06);
      background: transparent; cursor:pointer; font-weight:600;
    }
    .tab-btn.active { background: linear-gradient(180deg,var(--primary),var(--primary-600)); color:#111; border:none; }

    .atividade-lista { display:grid; gap:18px; margin-top:18px; }
    .atividade-card {
      display:flex; gap:18px; background:#fff; padding:18px; border-radius:14px; align-items:flex-start;
      text-decoration: none; color: inherit; transition: transform .18s, box-shadow .18s, border-color .18s;
      border: 1px solid #e6e6e6;
      box-shadow: 0 4px 12px rgba(16,24,40,0.04);
    }
    /* borda muda ao passar mouse (mesmo efeito do campo de busca ao focar) */
    .atividade-card:hover { transform: translateY(-4px); box-shadow: 0 10px 24px rgba(16,24,40,0.06); border-color: var(--primary); }

    .atividade-img {
      width:300px; height:250px; object-fit:cover; border-radius:12px; flex-shrink:0;
      background:#f6f6f6; display:block;
    }

    .atividade-info {
      flex:1;
      display:flex;
      flex-direction:column;
      min-width:0; /* evita overflow de texto */
    }

    .atividade-info h3 { margin:0 0 6px 0; font-size:1.1rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .atividade-info .meta { color:var(--muted); margin-bottom:8px; font-size:0.95rem; }
    .atividade-info .descricao { color:#444; margin:0 0 8px 0; font-size:0.95rem; line-height:1.3; overflow:hidden; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; line-clamp: 3; }
    .preco { color:var(--primary); font-weight:700; display:block; margin-top:8px; font-size:1rem; }

    .card-actions { margin-top:auto; display:flex; gap:8px; align-items:center; }

    @media(max-width:860px){
      .atividade-card{flex-direction:column; text-align:center; align-items:center}
      .atividade-img{width:100%; height:220px;}
      .atividade-info { width:100%; }
      .card-actions { justify-content:center; width:100%; }
    }

    .auth-box { width:100%; max-width:1200px; padding:30px; border-radius:12px; }
  </style>
</head>
<body>

<?php include 'templates/header.php'; ?>

<main class="container auth-page">
  <div class="auth-box">
    <h1>Explorar Experiências</h1>
    <p class="muted">Descubra atividades locais e veja no mapa o que está ao seu redor.</p>

    
    <form id="form-busca" class="auth-form" method="GET" style="margin-bottom:12px;">
      <label for="busca">Buscar por cidade, título ou categoria</label>
      <input type="text" id="busca" name="busca" placeholder="Ex: Porto, TukTuk, Gastronomia" style="margin-top:8px;">
        <button type="submit" class="btn primary" style="width:100%;">Buscar
        </button>
    </form>


       
        
        <!-- Botão Voltar -->
        <form id="form-voltar" method="GET">
            <button type="submit" class="btn ghost" style="width:100%;">
                Voltar
            </button>
        </form>



<!---------------------------------------------->

    <div class="tabs" role="tablist" aria-label="Visualização">
        <button class="tab-btn ghost" data-target="tab-lista" role="tab">Lista</button>
        <button class="tab-btn ghost" data-target="tab-mapa" role="tab">Mapa</button>
    </div>

    <div class="tabs-content">
      <div class="tab active" id="tab-lista" role="tabpanel">
        <?php if (count($atividades) === 0): ?>
          <p class="muted" style="text-align:center; margin-top:18px;">Nenhuma atividade cadastrada ainda.</p>
        <?php else: ?>


          <div id="lista" class="atividade-lista">
             


            <?php foreach($atividades as $a):
                $moedaBanco = $a['moeda'] ?? null;
                $moedaCustom = $a['moeda_customizada'] ?? null;

                if ($moedaBanco && $moedaBanco !== '') {
                    $currency = simbolo_moeda($moedaBanco);
                } elseif ($moedaCustom && $moedaCustom !== '') {
                    $currency = $moedaCustom;
                } else {
                    $currency = '€'; //////////////////////////////////////////////////////////// padrão
}

                $preco_fmt = number_format($a['preco'], 2, ',', '.');
                $img = $a['imagem'] ? 'uploads/tours/' . $a['imagem'] : 'img/default_tour.jpg';
            ?>
              <article class="atividade-card" aria-labelledby="titulo-<?= $a['id'] ?>">
            

                <?php if (!empty($a['imagem'])): ?>
                  <img src="uploads/tours/<?= htmlspecialchars($a['imagem']) ?>" alt="<?= htmlspecialchars($a['titulo']) ?>" class="atividade-img">

                  
                <?php else: ?>
                  <div class="atividade-img" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #999;">Sem imagem</div>
                <?php endif; ?>
                 
                
                
                
                <div class="atividade-info">
                  <h3 id="titulo-<?= $a['id'] ?>"><?= htmlspecialchars($a['titulo']) ?></h3>
                  <div class="meta"><?= htmlspecialchars($a['cidade']) ?> — <?= htmlspecialchars($a['categoria']) ?></div>
                  <div class="descricao"><?= htmlspecialchars(mb_substr($a['descricao'],0,240)) ?><?= (mb_strlen($a['descricao'])>240) ? '...' : '' ?></div>
                  <div class="card-actions">
                    <span class="preco"><?= htmlspecialchars($currency) ?> <?= $preco_fmt ?></span>
                    <div style="margin-left:auto">
                      <a href="tour.php?id=<?= $a['id'] ?>" class="btn primary">Ver detalhes</a>
                    </div>
                  </div>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="tab" id="tab-mapa" role="tabpanel" style="display:none;">
        <div id="mapa" style="width:100%; height:520px; border-radius:12px; margin-top:12px;"></div>
      </div>
    </div>
  </div>
</main>

<?php include 'templates/footer.php'; ?>

<script>
/* Abas funcionais */
const tabBtns = document.querySelectorAll('.tab-btn');
const tabs = document.querySelectorAll('.tab');
tabBtns.forEach(btn=>{
  btn.addEventListener('click', ()=>{
    tabBtns.forEach(b=>b.classList.remove('active'));
    tabs.forEach(t=>t.style.display = 'none');
    btn.classList.add('active');
    document.getElementById(btn.dataset.target).style.display = '';
    if (btn.dataset.target === 'tab-mapa' && window._map) {
      setTimeout(()=> window._map.invalidateSize(), 200);
    }
  });
});




/* Busca: mantém o formulário antigo (submit) mas também filtra instantaneamente enquanto digita */

// Preenche o campo com o que veio do PHP (GET)
const buscaInput = document.getElementById('busca');
buscaInput.value = "<?= htmlspecialchars($busca) ?>";

// Filtro instantâneo
buscaInput.addEventListener('input', function(){
  const q = this.value.trim().toLowerCase();
  const cards = document.querySelectorAll('#lista .atividade-card');
  cards.forEach(c=>{
    const texto = c.innerText.toLowerCase();
    c.style.display = q === '' || texto.includes(q) ? '' : 'none';
  });
});





/* Dados do PHP para o mapa */
const tours = <?= json_encode($markers, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>;

/* Leaflet map */
const map = L.map('mapa').setView([41.1579, -8.6291], 13);
window._map = map;
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(map);
const markers = L.layerGroup().addTo(map);

tours.forEach(t => {
  if (!t.lat || !t.lng) return;
  const marker = L.marker([t.lat, t.lng]).addTo(markers);

  const url = 'tour.php?id=' + encodeURIComponent(t.id);
  const thumb = t.imagem ? ('<img src="'+ t.imagem +'" style="width:200px;height:110px;object-fit:cover;border-radius:8px;display:block;margin-bottom:8px;">') : '';
  const moeda = t.moeda ? t.moeda : '';
  const preco = (typeof t.preco !== 'undefined' && t.preco !== null) ? Number(t.preco).toLocaleString('pt-BR',{minimumFractionDigits:2, maximumFractionDigits:2}) : '';
  const popupHtml = '<div style="max-width:260px;">'+ thumb +
    '<strong style="display:block;margin-bottom:6px;">' + escapeHtml(t.titulo) + '</strong>' +
    '<div style="color:#6b7280; margin-bottom:8px;">' + escapeHtml(t.cidade) + '</div>' +
    '<div style="margin-bottom:8px;"><strong style="color:var(--primary)">' + escapeHtml(moeda) + ' ' + preco + '</strong></div>' +
    '<a href="'+ url +'" class="btn primary" style="display:inline-block;padding:8px 10px;border-radius:8px;text-decoration:none;color:inherit;">Ver detalhes</a>' +
    '</div>';

  // popup que não fecha automaticamente ao clicar no mapa (permite clicar no botão dentro)
  marker.bindPopup(popupHtml, { maxWidth: 300, autoClose: false, closeOnClick: false, closeButton: true });

  // abre popup ao clicar no marcador (não navega diretamente)
  marker.on('click', function(e){
    this.openPopup();
  });
});

/* util: escape simples para textos em popup */
function escapeHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');
}
</script>
</body>
</html>