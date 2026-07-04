<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Agenda — YOONU JIGEEN</title>

<!-- FullCalendar CDN -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.11/locales/fr.global.min.js"></script>

<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'Segoe UI', sans-serif;
    background: #FFF5F8;
    color: #2A0A1A;
    min-height: 100vh;
  }

  /* ── HEADER ── */
  .header {
    background: linear-gradient(135deg, #E91E63 0%, #8B1A47 100%);
    color: white;
    padding: 16px 24px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 2px 12px rgba(233,30,99,.25);
  }
  .header h1 { font-size: 18px; font-weight: 800; letter-spacing: .5px; }
  .header p  { font-size: 12px; opacity: .8; }

  /* ── AUTH PANEL ── */
  #auth-panel {
    max-width: 420px;
    margin: 60px auto;
    background: white;
    border-radius: 20px;
    padding: 32px;
    border: 1px solid #F5D6E1;
    box-shadow: 0 4px 24px rgba(233,30,99,.08);
  }
  #auth-panel h2 {
    font-size: 20px; font-weight: 800; color: #8B1A47; margin-bottom: 6px;
  }
  #auth-panel p { font-size: 13px; color: #6B4757; margin-bottom: 22px; }

  .form-group { margin-bottom: 16px; }
  .form-group label {
    display: block; font-size: 11px; font-weight: 700;
    color: #6B4757; letter-spacing: .8px; margin-bottom: 6px;
  }
  .form-group input, .form-group select {
    width: 100%; padding: 12px 14px; border-radius: 10px;
    border: 1.5px solid #F5D6E1; font-size: 14px;
    background: white; outline: none; transition: border-color .2s;
  }
  .form-group input:focus, .form-group select:focus {
    border-color: #E91E63;
  }

  .btn-primary {
    width: 100%; padding: 14px; background: #E91E63; color: white;
    border: none; border-radius: 12px; font-size: 15px; font-weight: 700;
    cursor: pointer; transition: background .2s;
  }
  .btn-primary:hover { background: #C2185B; }

  .error-msg {
    background: #fee2e2; color: #dc2626; padding: 10px 14px;
    border-radius: 10px; font-size: 13px; margin-top: 12px; display: none;
  }

  /* ── MAIN LAYOUT ── */
  #app { display: none; }
  .layout { display: flex; height: calc(100vh - 64px); }

  /* ── SIDEBAR ── */
  .sidebar {
    width: 260px; flex-shrink: 0;
    background: white; border-right: 1px solid #F5D6E1;
    padding: 20px; overflow-y: auto;
  }
  .sidebar h3 {
    font-size: 11px; font-weight: 800; color: #6B4757;
    letter-spacing: .8px; margin-bottom: 12px;
  }

  .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 20px; }
  .stat-card {
    background: #FFF5F8; border-radius: 12px; padding: 12px;
    border: 1px solid #F5D6E1; text-align: center;
  }
  .stat-card .num { font-size: 22px; font-weight: 800; color: #E91E63; }
  .stat-card .lbl { font-size: 10px; color: #6B4757; font-weight: 600; }

  .legend { margin-bottom: 20px; }
  .legend-item {
    display: flex; align-items: center; gap: 8px;
    font-size: 12px; color: #2A0A1A; margin-bottom: 6px; font-weight: 600;
  }
  .legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }

  .filter-group { margin-bottom: 14px; }
  .filter-group label {
    display: block; font-size: 11px; font-weight: 700;
    color: #6B4757; letter-spacing: .8px; margin-bottom: 5px;
  }
  .filter-group select {
    width: 100%; padding: 9px 10px; border-radius: 8px;
    border: 1.5px solid #F5D6E1; font-size: 12px; background: white;
  }

  .btn-refresh {
    width: 100%; padding: 10px; background: #FCE4EC; color: #E91E63;
    border: 1.5px solid #E91E63; border-radius: 10px; font-size: 13px;
    font-weight: 700; cursor: pointer; margin-top: 8px; transition: .2s;
  }
  .btn-refresh:hover { background: #E91E63; color: white; }

  .btn-logout {
    width: 100%; padding: 10px; background: white; color: #6B4757;
    border: 1.5px solid #F5D6E1; border-radius: 10px; font-size: 12px;
    font-weight: 600; cursor: pointer; margin-top: 8px;
  }

  /* ── CALENDAR ── */
  .calendar-wrap {
    flex: 1; padding: 20px; overflow: auto; background: #FFF5F8;
  }

  #calendar {
    background: white; border-radius: 16px;
    border: 1px solid #F5D6E1; padding: 16px;
    height: 100%; min-height: 600px;
  }

  /* FullCalendar overrides */
  .fc .fc-toolbar-title { font-size: 16px !important; font-weight: 800 !important; color: #8B1A47; }
  .fc .fc-button-primary {
    background: #E91E63 !important; border-color: #E91E63 !important;
    font-weight: 700 !important; font-size: 12px !important;
  }
  .fc .fc-button-primary:hover { background: #C2185B !important; }
  .fc .fc-button-primary:disabled { background: #F5D6E1 !important; border-color: #F5D6E1 !important; color: #6B4757 !important; }
  .fc-day-today { background: #FFF5F8 !important; }
  .fc-event { font-size: 11px !important; font-weight: 700 !important; cursor: pointer; }

  /* ── MODAL ── */
  .modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.4); z-index: 1000;
    align-items: center; justify-content: center;
  }
  .modal-overlay.open { display: flex; }
  .modal {
    background: white; border-radius: 20px; padding: 28px;
    width: 380px; max-width: 90vw; position: relative;
    box-shadow: 0 8px 40px rgba(0,0,0,.15);
  }
  .modal h3 { font-size: 17px; font-weight: 800; color: #8B1A47; margin-bottom: 16px; }
  .modal-close {
    position: absolute; top: 16px; right: 16px;
    background: #F5D6E1; border: none; border-radius: 50%;
    width: 28px; height: 28px; cursor: pointer;
    font-size: 14px; color: #8B1A47; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
  }
  .modal-row {
    display: flex; align-items: flex-start; gap: 10px;
    margin-bottom: 10px; font-size: 13px;
  }
  .modal-row .ico { font-size: 16px; margin-top: 1px; }
  .modal-row .lbl { color: #6B4757; font-size: 11px; font-weight: 700; }
  .modal-row .val { color: #2A0A1A; font-weight: 600; }
  .badge {
    display: inline-block; padding: 3px 10px;
    border-radius: 20px; font-size: 11px; font-weight: 700; margin-top: 10px;
  }
</style>
</head>
<body>

<!-- HEADER -->
<div class="header">
  <div style="width:40px;height:40px;background:white;border-radius:50%;display:flex;align-items:center;justify-content:center;">
    <span style="font-size:20px">📅</span>
  </div>
  <div>
    <h1>Agenda YOONU JIGEEN</h1>
    <p id="header-info">Calendrier des rendez-vous</p>
  </div>
</div>

<!-- AUTHENTIFICATION -->
<div id="auth-panel">
  <h2>Connexion requise</h2>
  <p>Entrez vos identifiants pour visualiser l'agenda des rendez-vous.</p>
  <div class="form-group">
    <label>RÔLE</label>
    <select id="role">
      <option value="admin">Administrateur</option>
      <option value="gynecologue">Gynécologue</option>
    </select>
  </div>
  <div class="form-group">
    <label>EMAIL</label>
    <input type="email" id="email" placeholder="admin@yoonumakk.sn">
  </div>
  <div class="form-group">
    <label>MOT DE PASSE</label>
    <input type="password" id="password" placeholder="••••••••">
  </div>
  <button class="btn-primary" onclick="seConnecter()">Accéder à l'agenda</button>
  <div class="error-msg" id="auth-error"></div>
</div>

<!-- APPLICATION PRINCIPALE -->
<div id="app">
  <div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
      <h3>STATISTIQUES</h3>
      <div class="stats-grid">
        <div class="stat-card"><div class="num" id="stat-total">0</div><div class="lbl">Total</div></div>
        <div class="stat-card"><div class="num" id="stat-attente">0</div><div class="lbl">En attente</div></div>
        <div class="stat-card"><div class="num" id="stat-confirme">0</div><div class="lbl">Confirmés</div></div>
        <div class="stat-card"><div class="num" id="stat-termine">0</div><div class="lbl">Terminés</div></div>
      </div>

      <h3>LÉGENDE</h3>
      <div class="legend">
        <div class="legend-item"><div class="legend-dot" style="background:#D97706"></div>En attente</div>
        <div class="legend-item"><div class="legend-dot" style="background:#059669"></div>Confirmé</div>
        <div class="legend-item"><div class="legend-dot" style="background:#6B7280"></div>Terminé</div>
        <div class="legend-item"><div class="legend-dot" style="background:#DC2626"></div>Refusé / Annulé</div>
      </div>

      <h3>FILTRES</h3>
      <div class="filter-group">
        <label>STATUT</label>
        <select id="filter-statut" onchange="appliquerFiltres()">
          <option value="">Tous les statuts</option>
          <option value="en_attente">En attente</option>
          <option value="confirme">Confirmé</option>
          <option value="termine">Terminé</option>
          <option value="refuse">Refusé</option>
          <option value="annule">Annulé</option>
        </select>
      </div>

      <button class="btn-refresh" onclick="chargerEvenements()">⟳ Rafraîchir</button>
      <button class="btn-logout" onclick="seDeconnecter()">Se déconnecter</button>
    </aside>

    <!-- CALENDRIER -->
    <div class="calendar-wrap">
      <div id="calendar"></div>
    </div>
  </div>
</div>

<!-- MODAL DÉTAIL RDV -->
<div class="modal-overlay" id="modal" onclick="fermerModal(event)">
  <div class="modal">
    <button class="modal-close" onclick="document.getElementById('modal').classList.remove('open')">✕</button>
    <h3>📋 Détail du rendez-vous</h3>
    <div id="modal-content"></div>
  </div>
</div>

<script>
const API = '{{ url('/api') }}';
let token = localStorage.getItem('ym_token');
let role  = localStorage.getItem('ym_role') || 'admin';
let calendar;
let tousLesEvts = [];

// ── COULEURS STATUT ────────────────────────────────────────────────────────
const COULEURS = {
  en_attente: '#D97706',
  confirme:   '#059669',
  termine:    '#6B7280',
  refuse:     '#DC2626',
  annule:     '#DC2626',
};
const LABELS = {
  en_attente: 'En attente',
  confirme:   'Confirmé',
  termine:    'Terminé',
  refuse:     'Refusé',
  annule:     'Annulé',
};

// ── CONNEXION ──────────────────────────────────────────────────────────────
async function seConnecter() {
  const r    = document.getElementById('role').value;
  const mail = document.getElementById('email').value.trim();
  const pass = document.getElementById('password').value;
  const err  = document.getElementById('auth-error');
  err.style.display = 'none';

  const endpoint = r === 'admin'
    ? `${API}/auth/connexion`
    : `${API}/gynecologue/auth/connexion`;

  const res = await fetch(endpoint, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
    body: JSON.stringify({ email: mail, password: pass }),
  });
  const data = await res.json();
  if (!res.ok) {
    err.textContent = data.message || 'Identifiants invalides.';
    err.style.display = 'block';
    return;
  }
  token = data.token || data.access_token;
  role  = r;
  localStorage.setItem('ym_token', token);
  localStorage.setItem('ym_role',  role);
  afficherApp();
}

// ── AFFICHER L'APP ─────────────────────────────────────────────────────────
function afficherApp() {
  document.getElementById('auth-panel').style.display = 'none';
  document.getElementById('app').style.display = 'block';
  document.getElementById('header-info').textContent =
    role === 'admin' ? 'Vue administrateur' : 'Mon agenda';
  initCalendrier();
  chargerEvenements();
}

function seDeconnecter() {
  localStorage.removeItem('ym_token');
  localStorage.removeItem('ym_role');
  location.reload();
}

// ── INITIALISER FULLCALENDAR ───────────────────────────────────────────────
function initCalendrier() {
  const el = document.getElementById('calendar');
  calendar = new FullCalendar.Calendar(el, {
    locale: 'fr',
    initialView: 'dayGridMonth',
    headerToolbar: {
      left:   'prev,next today',
      center: 'title',
      right:  'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
    },
    buttonText: {
      today: "Aujourd'hui",
      month: 'Mois',
      week:  'Semaine',
      day:   'Jour',
      list:  'Liste',
    },
    height: '100%',
    events: [],
    eventClick: function(info) { afficherDetail(info.event); },
    eventDidMount: function(info) {
      info.el.title = info.event.title;
    },
  });
  calendar.render();
}

// ── CHARGER ÉVÉNEMENTS ─────────────────────────────────────────────────────
async function chargerEvenements() {
  if (!token) return;

  const endpoint = role === 'admin'
    ? `${API}/admin/rendez-vous?per_page=500`
    : `${API}/gynecologue/rendez-vous?per_page=500`;

  try {
    const res = await fetch(endpoint, {
      headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
    });
    if (res.status === 401) { seDeconnecter(); return; }
    const data = await res.json();
    const items = data.data || data;
    tousLesEvts = items;
    mettreAJourCalendrier(items);
    mettreAJourStats(items);
  } catch (e) {
    console.error('Erreur chargement RDV', e);
  }
}

function mettreAJourCalendrier(items) {
  calendar.removeAllEvents();
  const filtre = document.getElementById('filter-statut').value;
  const filtered = filtre ? items.filter(r => (r.statut?.value || r.statut) === filtre) : items;

  filtered.forEach(rdv => {
    const statut = rdv.statut?.value || rdv.statut || 'en_attente';
    const date   = rdv.date_confirmee || rdv.date_souhaitee;
    const heure  = rdv.heure_confirmee || rdv.heure_souhaitee || '00:00';
    const doc    = rdv.gynecologue
      ? `Dr. ${rdv.gynecologue.nom}`
      : (rdv.patiente ? `${rdv.patiente.prenom} ${rdv.patiente.nom}` : 'N/A');

    calendar.addEvent({
      id:              String(rdv.id),
      title:           doc,
      start:           `${date}T${heure}`,
      backgroundColor: COULEURS[statut] || '#9CA3AF',
      borderColor:     COULEURS[statut] || '#9CA3AF',
      extendedProps:   { rdv },
    });
  });
}

function mettreAJourStats(items) {
  document.getElementById('stat-total').textContent    = items.length;
  document.getElementById('stat-attente').textContent  = items.filter(r => (r.statut?.value||r.statut)==='en_attente').length;
  document.getElementById('stat-confirme').textContent = items.filter(r => (r.statut?.value||r.statut)==='confirme').length;
  document.getElementById('stat-termine').textContent  = items.filter(r => (r.statut?.value||r.statut)==='termine').length;
}

function appliquerFiltres() { mettreAJourCalendrier(tousLesEvts); }

// ── MODAL DÉTAIL ──────────────────────────────────────────────────────────
function afficherDetail(event) {
  const rdv    = event.extendedProps.rdv;
  const statut = rdv.statut?.value || rdv.statut || '';
  const g      = rdv.gynecologue;
  const p      = rdv.femme || rdv.patiente;
  const couleur = COULEURS[statut] || '#9CA3AF';

  const html = `
    <div class="modal-row">
      <span class="ico">👩‍⚕️</span>
      <div><div class="lbl">MÉDECIN</div>
      <div class="val">${g ? `Dr. ${g.prenom} ${g.nom}` : 'Non assigné'}</div>
      ${g ? `<div style="font-size:11px;color:#E91E63;font-weight:600">${g.specialite||''}</div>` : ''}</div>
    </div>
    ${p ? `<div class="modal-row">
      <span class="ico">👤</span>
      <div><div class="lbl">PATIENTE</div>
      <div class="val">${p.prenom||''} ${p.nom||''}</div></div>
    </div>` : ''}
    <div class="modal-row">
      <span class="ico">📅</span>
      <div><div class="lbl">DATE SOUHAITÉE</div>
      <div class="val">${formatDate(rdv.date_souhaitee)} à ${rdv.heure_souhaitee||'--:--'}</div></div>
    </div>
    ${rdv.date_confirmee ? `<div class="modal-row">
      <span class="ico">✅</span>
      <div><div class="lbl">DATE CONFIRMÉE</div>
      <div class="val">${formatDate(rdv.date_confirmee)} à ${rdv.heure_confirmee||'--:--'}</div></div>
    </div>` : ''}
    ${rdv.motif ? `<div class="modal-row">
      <span class="ico">📋</span>
      <div><div class="lbl">MOTIF</div><div class="val">${rdv.motif}</div></div>
    </div>` : ''}
    ${rdv.note_gynecologue ? `<div class="modal-row">
      <span class="ico">📝</span>
      <div><div class="lbl">NOTE MÉDECIN</div><div class="val">${rdv.note_gynecologue}</div></div>
    </div>` : ''}
    <span class="badge" style="background:${couleur}20;color:${couleur}">
      ${LABELS[statut] || statut}
    </span>`;

  document.getElementById('modal-content').innerHTML = html;
  document.getElementById('modal').classList.add('open');
}

function fermerModal(e) {
  if (e.target.id === 'modal') {
    document.getElementById('modal').classList.remove('open');
  }
}

function formatDate(str) {
  if (!str) return '—';
  const [y, m, d] = str.split('-');
  const mois = ['jan','fév','mar','avr','mai','juin','jul','aoû','sep','oct','nov','déc'];
  return `${parseInt(d)} ${mois[parseInt(m)-1]} ${y}`;
}

// ── INIT ──────────────────────────────────────────────────────────────────
if (token) { afficherApp(); }
</script>
</body>
</html>
