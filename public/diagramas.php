<?php
// diagrama.php - gera visualização dos Diagramas (Caso de Uso + Classes)
// Coloque em public/ e abra no navegador.
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Diagramas - Casos de Uso & Classes (Nookway)</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
  :root{
    --bg:#f6f8fb; --card:#fff; --muted:#6b7280; --accent:#60a5fa; --accent-2:#93c5fd;
    --text:#0f172a; --box:#e6eefc;
  }
  html,body{height:100%; margin:0; font-family:Inter,system-ui,Segoe UI,Roboto,"Helvetica Neue",Arial; background:linear-gradient(180deg,#f8fbff 0%, #f6f8fb 100%);}
  .wrap{max-width:1200px;margin:28px auto;padding:20px;}
  header{display:flex;align-items:center;gap:16px;margin-bottom:18px;}
  header h1{margin:0;font-size:20px;color:var(--text);}
  header p{margin:0;color:var(--muted);font-size:13px;}
  .grid{display:grid;grid-template-columns: 1fr 420px; gap:18px;}
  .card{background:var(--card);border-radius:12px;padding:16px;box-shadow:0 6px 18px rgba(12,18,36,0.06);}
  .toolbar{display:flex;gap:8px;align-items:center;margin-bottom:12px;}
  .btn{background:transparent;border:1px solid #e6eefc;padding:8px 12px;border-radius:8px;cursor:pointer;font-weight:600;color:var(--text);}
  .btn.active{background:linear-gradient(90deg,var(--accent),var(--accent-2));color:#022; border: none; box-shadow:0 6px 18px rgba(96,165,250,0.18);}
  .legend{display:flex;gap:10px;flex-wrap:wrap;margin-top:8px}
  .legend .chip{padding:6px 8px;border-radius:999px;background:#f1f5f9;color:var(--muted);font-size:13px}
  .side{display:flex;flex-direction:column;gap:12px;}
  .section-title{font-weight:700;color:var(--text);margin-bottom:8px;}
  .list{font-size:14px;color:var(--muted);line-height:1.5;}
  .list b{color:var(--text)}
  /* SVG area */
  .canvas-wrap{background:linear-gradient(180deg,#ffffff,#fbfdff);border-radius:10px;padding:12px; min-height:560px; overflow:auto;}
  svg{width:100%;height:620px;display:block;}
  /* small screens */
  @media (max-width:980px){ .grid{grid-template-columns:1fr;} svg{height:780px;} .side{order:2} }
  /* actor text */
  .actor{font-weight:600;fill:#05204a}
  .ucase{fill:#fff;stroke:#cfe5ff;stroke-width:2;filter:drop-shadow(0 6px 14px rgba(3, 102, 214, 0.06));}
  .ucase-text{font-size:13px;fill:#032a4a;font-weight:600}
  .link-line{stroke:#9fbff0;stroke-width:2;stroke-linecap:round}
  .class-box{fill:#fff;stroke:#e6eefc;stroke-width:1;border-radius:6px}
  .class-title{font-weight:700; font-size:13px}
  .class-attr{font-size:12px;fill:#334155}
  .small-muted{color:var(--muted);font-size:13px}
</style>
</head>
<body>
<div class="wrap">
  <header>
    <div style="width:48px;height:48px;border-radius:10px;background:linear-gradient(135deg,var(--accent),var(--accent-2));display:flex;align-items:center;justify-content:center;color:#022;font-weight:700">NW</div>
    <div>
      <h1>Diagramas — Casos de Uso & Diagrama de Classes</h1>
      <p>Visualização automática gerada a partir das funcionalidades do seu projeto (public/)</p>
    </div>
  </header>

  <div class="grid">
    <div class="card">
      <div class="toolbar">
        <button id="showUse" class="btn active" onclick="show('use')">Casos de Uso</button>
        <button id="showClass" class="btn" onclick="show('class')">Diagrama de Classes</button>
        <div style="flex:1"></div>
        <div class="legend">
          <div class="chip">Turista</div>
          <div class="chip">Guia</div>
          <div class="chip">Ambos</div>
        </div>
      </div>

      <div class="canvas-wrap card-body">
        <!-- SVG Use Case -->
        <div id="useCaseWrap">
<svg viewBox="0 0 1100 720" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Diagrama de Casos de Uso">
  <defs>
    <linearGradient id="g1" x1="0" x2="1"><stop offset="0" stop-color="#e6f2ff"/><stop offset="1" stop-color="#fff"/></linearGradient>
  </defs>

  <!-- Actors (left and right) -->
  <!-- Turista actor -->
  <g transform="translate(40,90)" class="actor-wrap">
    <circle cx="28" cy="28" r="22" fill="#fff" stroke="#bfe0ff" stroke-width="2"/>
    <text x="28" y="34" text-anchor="middle" class="actor">T</text>
    <text x="80" y="18" font-size="13" fill="#0b2b44" font-weight="700">Turista</text>
    <text x="80" y="36" font-size="12" fill="#6b7280">Explora, reserva, avalia</text>
  </g>

  <!-- Guia actor -->
  <g transform="translate(985,90)" class="actor-wrap" style="transform-origin: right;">
    <circle cx="28" cy="28" r="22" fill="#fff" stroke="#ffd9bf" stroke-width="2"/>
    <text x="28" y="34" text-anchor="middle" class="actor">G</text>
    <text x="-90" y="18" font-size="13" fill="#0b2b44" font-weight="700">Guia</text>
    <text x="-90" y="36" font-size="12" fill="#6b7280">Cria/edita tours, gerencia reservas</text>
  </g>

  <!-- Use case bubbles (center) -->
  <!-- Row 1 -->
  <ellipse cx="300" cy="80" rx="140" ry="34" class="ucase" />
  <text x="300" y="86" text-anchor="middle" class="ucase-text">Explorar Tours</text>

  <ellipse cx="650" cy="80" rx="140" ry="34" class="ucase" />
  <text x="650" y="86" text-anchor="middle" class="ucase-text">Ver Detalhes da Tour</text>

  <!-- Row 2 -->
  <ellipse cx="300" cy="170" rx="160" ry="34" class="ucase" />
  <text x="300" y="176" text-anchor="middle" class="ucase-text">Favoritar / Gerenciar Favoritos</text>

  <ellipse cx="650" cy="170" rx="160" ry="34" class="ucase" />
  <text x="650" y="176" text-anchor="middle" class="ucase-text">Reservar Tour (Calendário + Horários)</text>

  <!-- Row 3 -->
  <ellipse cx="300" cy="260" rx="150" ry="34" class="ucase" />
  <text x="300" y="266" text-anchor="middle" class="ucase-text">Avaliar Tour</text>

  <ellipse cx="650" cy="260" rx="150" ry="34" class="ucase" />
  <text x="650" y="266" text-anchor="middle" class="ucase-text">Ver Minhas Reservas</text>

  <!-- Row 4 - Guia -->
  <ellipse cx="300" cy="360" rx="160" ry="34" class="ucase" />
  <text x="300" y="366" text-anchor="middle" class="ucase-text">Criar / Editar Tour (mapa + horários)</text>

  <ellipse cx="650" cy="360" rx="160" ry="34" class="ucase" />
  <text x="650" y="366" text-anchor="middle" class="ucase-text">Gerenciar Tours</text>

  <!-- Row 5 - reservas recebidas -->
  <ellipse cx="480" cy="460" rx="200" ry="34" class="ucase" />
  <text x="480" y="466" text-anchor="middle" class="ucase-text">Receber / Confirmar / Cancelar Reservas</text>

  <!-- Row 6 - perfis e comunicações -->
  <ellipse cx="480" cy="540" rx="200" ry="34" class="ucase" />
  <text x="480" y="546" text-anchor="middle" class="ucase-text">Ver Perfil do Guia/Turista (contato)</text>

  <!-- Lines from Turista -->
  <line x1="70" y1="100" x2="180" y2="80" class="link-line"/>
  <line x1="70" y1="170" x2="210" y2="170" class="link-line"/>
  <line x1="70" y1="235" x2="200" y2="260" class="link-line"/>
  <line x1="70" y1="315" x2="230" y2="360" class="link-line"/>

  <!-- Lines from Guia -->
  <line x1="980" y1="100" x2="760" y2="80" class="link-line"/>
  <line x1="980" y1="200" x2="780" y2="170" class="link-line"/>
  <line x1="980" y1="320" x2="730" y2="360" class="link-line"/>
  <line x1="980" y1="400" x2="580" y2="460" class="link-line"/>

  <!-- connections to "Ver Perfil" -->
  <line x1="200" y1="560" x2="420" y2="546" stroke="#c7d9ff" stroke-width="2" />
  <line x1="880" y1="560" x2="540" y2="546" stroke="#ffe6d2" stroke-width="2" />

  <!-- small caption -->
  <text x="40" y="700" fill="#94a3b8" font-size="12">Obs: atores/bolhas vinculadas às páginas .php do diretório public/ (ver painel lateral)</text>
</svg>
        </div>

        <!-- SVG Class Diagram -->
        <div id="classWrap" style="display:none">
<svg viewBox="0 0 1100 720" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Diagrama de Classes">
  <!-- Usuario -->
  <g transform="translate(40,40)">
    <rect x="0" y="0" width="260" height="120" rx="8" class="class-box"/>
    <text x="12" y="22" class="class-title">Usuario</text>
    <text x="12" y="44" class="class-attr">- id : int</text>
    <text x="12" y="62" class="class-attr">- nome : string</text>
    <text x="12" y="80" class="class-attr">- email : string</text>
    <text x="12" y="98" class="class-attr">- telefone : string</text>
  </g>

  <!-- Guia (herança visual) -->
  <g transform="translate(340,40)">
    <rect x="0" y="0" width="260" height="120" rx="8" class="class-box"/>
    <text x="12" y="22" class="class-title">Guia (Usuario)</text>
    <text x="12" y="44" class="class-attr">- id_guia : int</text>
    <text x="12" y="62" class="class-attr">- bio_profissional : text</text>
    <text x="12" y="80" class="class-attr">- disponibilidade : string</text>
    <text x="12" y="98" class="class-attr">+ criarTour()</text>
  </g>

  <!-- Turista (herança visual) -->
  <g transform="translate(640,40)">
    <rect x="0" y="0" width="260" height="120" rx="8" class="class-box"/>
    <text x="12" y="22" class="class-title">Turista (Usuario)</text>
    <text x="12" y="44" class="class-attr">- preferencia : string</text>
    <text x="12" y="62" class="class-attr">- historico : text</text>
    <text x="12" y="80" class="class-attr">+ reservar()</text>
  </g>

  <!-- Tour -->
  <g transform="translate(40,220)">
    <rect x="0" y="0" width="360" height="140" rx="8" class="class-box"/>
    <text x="12" y="22" class="class-title">Tour</text>
    <text x="12" y="44" class="class-attr">- id : int</text>
    <text x="12" y="62" class="class-attr">- titulo : string</text>
    <text x="12" y="80" class="class-attr">- descricao : text</text>
    <text x="12" y="98" class="class-attr">- cidade : string</text>
    <text x="12" y="116" class="class-attr">- preco : decimal</text>
  </g>

  <!-- Reserva -->
  <g transform="translate(440,220)">
    <rect x="0" y="0" width="300" height="140" rx="8" class="class-box"/>
    <text x="12" y="22" class="class-title">Reserva</text>
    <text x="12" y="44" class="class-attr">- id : int</text>
    <text x="12" y="62" class="class-attr">- data_reservada : datetime</text>
    <text x="12" y="80" class="class-attr">- status : enum (pendente/confirmada/cancelada)</text>
    <text x="12" y="98" class="class-attr">- quantidade_pessoas : int</text>
    <text x="12" y="116" class="class-attr">- codigo_confirmacao : string</text>
  </g>

  <!-- Avaliacao -->
  <g transform="translate(40,420)">
    <rect x="0" y="0" width="320" height="100" rx="8" class="class-box"/>
    <text x="12" y="22" class="class-title">Avaliacao</text>
    <text x="12" y="44" class="class-attr">- id : int</text>
    <text x="12" y="62" class="class-attr">- nota : int</text>
    <text x="12" y="80" class="class-attr">- comentario : text</text>
  </g>

  <!-- Favorito -->
  <g transform="translate(400,420)">
    <rect x="0" y="0" width="240" height="100" rx="8" class="class-box"/>
    <text x="12" y="22" class="class-title">Favorito</text>
    <text x="12" y="44" class="class-attr">- id : int</text>
    <text x="12" y="62" class="class-attr">- id_turista : int</text>
    <text x="12" y="80" class="class-attr">- id_tour : int</text>
  </g>

  <!-- Relationships (lines) -->
  <!-- Usuario 1 --- N Reserva -->
  <line x1="190" y1="160" x2="500" y2="260" stroke="#9fbff0" stroke-width="2" marker-end="url(#arr)" />
  <text x="420" y="240" font-size="12" fill="#64748b">1..*</text>

  <!-- Tour 1 --- N Reserva -->
  <line x1="220" y1="300" x2="440" y2="260" stroke="#cfe5ff" stroke-width="2" marker-end="url(#arr)"/>
  <text x="320" y="300" font-size="12" fill="#64748b">1..*</text>

  <!-- Reserva -> Usuario (turista) -->
  <line x1="540" y1="300" x2="220" y2="120" stroke="#cfe5ff" stroke-width="1.6" stroke-dasharray="6 4"/>
  <text x="420" y="200" font-size="12" fill="#94a3b8">pertence a</text>

  <!-- Avaliacao -> Usuario & Tour -->
  <line x1="160" y1="520" x2="120" y2="160" stroke="#d7eefd" stroke-width="1.6"/>
  <line x1="270" y1="520" x2="200" y2="260" stroke="#d7eefd" stroke-width="1.6"/>
  <text x="180" y="480" font-size="12" fill="#94a3b8">1..*</text>

  <!-- markers -->
  <defs>
    <marker id="arr" viewBox="0 0 10 10" refX="8" refY="5" markerUnits="strokeWidth" markerWidth="6" markerHeight="6" orient="auto">
      <path d="M 0 0 L 10 5 L 0 10 z" fill="#9fbff0" />
    </marker>
  </defs>

  <!-- footnote -->
  <text x="40" y="680" fill="#94a3b8" font-size="12">Notas: classes simplificadas para o diagrama (atributos essenciais). Ajuste conforme seu BD/tables.</text>
</svg>
        </div>
      </div>
    </div>

    <!-- side panel with file -> role mapping & quick legend -->
    <aside class="side">
      <div class="card">
        <div class="section-title">Mapeamento de páginas / funcionalidades</div>
        <div class="list">
          <p><b>Ambos</b>: css/nookway.css, templates/header.php, templates/footer.php, db.php, index.php, login.php, login_action.php, logout.php, cadastrar.php, cadastro_usuario.php, contato.php, sobre.php, termos.php, privacidade.php, perfil.php, perfil_action.php, atualizar_perfil.php, ver_perfil.php</p>
          <p><b>Guia</b>: criar_tour.php, editar_tour.php, gerenciar_tours.php, dashboard_guia.php, reservas_recebidas.php</p>
          <p><b>Turista</b>: explorar.php, tour.php, reservar.php, avaliar.php, favoritar.php, minhas_reservas.php, meus_favoritos.php, dashboard_turista.php</p>
        </div>
      </div>

      <div class="card">
        <div class="section-title">Casos de Uso (resumo)</div>
        <div class="list">
          <ul style="margin:0 0 0 18px;padding:0;">
            <li>Explorar / Buscar tours</li>
            <li>Ver detalhes da tour (mapa, horários)</li>
            <li>Reservar (calendário + horário) & gerar código de confirmação</li>
            <li>Favoritar / Meus favoritos</li>
            <li>Avaliar tours</li>
            <li>Criar / Editar tours (guia) — incluindo mapa, horários e imagens</li>
            <li>Gerenciar reservas recebidas (confirmar / cancelar)</li>
            <li>Ver perfis (guia / turista) e contato</li>
          </ul>
        </div>
      </div>

      <div class="card">
        <div class="section-title">Diagrama de Classes (resumo)</div>
        <div class="list">
          <p><b>Usuario</b> — base para Guia / Turista</p>
          <p><b>Tour</b> — criado por Guia; contém dias/horários, mapa, preço</p>
          <p><b>Reserva</b> — ligação Turista ↔ Tour (status, código_confirmacao)</p>
          <p><b>Avaliacao</b> — Turista → Tour</p>
          <p><b>Favorito</b> — Turista → Tour</p>
        </div>
      </div>

    </aside>
  </div>

  <footer style="margin-top:18px;text-align:center;color:var(--muted);font-size:13px">
    Gerado automaticamente — cole este arquivo em <code>public/</code> e abra no navegador.
  </footer>
</div>

<script>
  function show(mode){
    document.getElementById('showUse').classList.remove('active');
    document.getElementById('showClass').classList.remove('active');
    document.getElementById('useCaseWrap').style.display='none';
    document.getElementById('classWrap').style.display='none';
    if(mode==='use'){ document.getElementById('showUse').classList.add('active'); document.getElementById('useCaseWrap').style.display='block'; }
    else { document.getElementById('showClass').classList.add('active'); document.getElementById('classWrap').style.display='block'; }
  }
  // default shown: use cases
  show('use');
</script>
</body>
</html>


