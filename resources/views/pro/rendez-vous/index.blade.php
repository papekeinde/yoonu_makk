@extends('layouts.pro')

@section('title', 'Mes rendez-vous')

{{-- ═══════════════════ STYLES FULLCALENDAR (clair / sombre, charte YOONU) ═══════════════════ --}}
@push('styles')
<style>
.fc {
    --fc-border-color: rgba(26, 7, 16, 0.07);
    --fc-today-bg-color: rgba(233, 30, 99, 0.06);
    --fc-neutral-bg-color: rgba(0, 0, 0, 0.02);
    --fc-page-bg-color: transparent;
    --fc-highlight-color: rgba(233, 30, 99, 0.12);
    --fc-button-bg-color: #ffffff;
    --fc-button-border-color: #e5e7eb;
    --fc-button-text-color: #374151;
    --fc-button-hover-bg-color: #f9fafb;
    --fc-button-hover-border-color: #d1d5db;
    --fc-button-active-bg-color: #E91E63;
    --fc-button-active-border-color: #E91E63;
    --fc-button-active-text-color: #ffffff;
    font-family: 'Manrope', sans-serif;
}
html.dark .fc {
    --fc-border-color: rgba(255, 255, 255, 0.06);
    --fc-today-bg-color: rgba(233, 30, 99, 0.10);
    --fc-neutral-bg-color: rgba(255, 255, 255, 0.02);
    --fc-highlight-color: rgba(233, 30, 99, 0.14);
    --fc-button-bg-color: rgba(255, 255, 255, 0.05);
    --fc-button-border-color: rgba(255, 255, 255, 0.08);
    --fc-button-text-color: #d1d5db;
    --fc-button-hover-bg-color: rgba(255, 255, 255, 0.08);
    --fc-button-hover-border-color: rgba(255, 255, 255, 0.14);
    --fc-button-active-bg-color: #E91E63;
    --fc-button-active-border-color: #E91E63;
    --fc-button-active-text-color: #ffffff;
    --fc-list-event-hover-bg-color: rgba(255, 255, 255, 0.04);
}

/* Titres / en-têtes */
.fc .fc-toolbar-title { color: #1A0710; font-family: 'Archivo', sans-serif; font-size: 1.15rem; font-weight: 700; }
html.dark .fc .fc-toolbar-title { color: #f3f4f6; }
.fc .fc-col-header-cell-cushion { color: #6b7280; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; padding: 10px 4px; }
html.dark .fc .fc-col-header-cell-cushion,
html.dark .fc .fc-daygrid-day-number,
html.dark .fc .fc-timegrid-axis-cushion,
html.dark .fc .fc-timegrid-slot-label-cushion { color: #9ca3af; }
.fc .fc-daygrid-day-number { color: #374151; font-size: 0.82rem; font-weight: 500; padding: 6px 8px; }
html.dark .fc .fc-list-event-title a,
html.dark .fc .fc-list-day-text,
html.dark .fc .fc-list-day-side-text { color: #e5e7eb; }
html.dark .fc .fc-list-event-time { color: #9ca3af; }
html.dark .fc .fc-list-empty-cushion { color: #9ca3af; }

/* Pastille « aujourd'hui » */
.fc .fc-day-today .fc-daygrid-day-number {
    background: #E91E63; color: #fff; border-radius: 9999px;
    width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;
    font-weight: 700; padding: 0; margin: 5px;
}

/* Bordures sombres */
html.dark .fc-theme-standard td,
html.dark .fc-theme-standard th { border-color: rgba(255, 255, 255, 0.06); }
html.dark .fc-theme-standard .fc-scrollgrid { border-color: rgba(255, 255, 255, 0.06); }
html.dark .fc .fc-list-day-cushion { background: rgba(255, 255, 255, 0.03); }
html.dark .fc .fc-popover { background: #25101A; border-color: rgba(255, 255, 255, 0.08); }
html.dark .fc .fc-popover-header { background: rgba(255, 255, 255, 0.04); color: #e5e7eb; }
html.dark .fc .fc-popover-body { background: #25101A; }
html.dark .fc .fc-more-link { color: #F06292; }

/* Boutons */
.fc .fc-button {
    border-radius: 10px !important; font-size: 0.8rem !important; font-weight: 500 !important;
    padding: 0.45rem 0.9rem !important; transition: all 0.15s ease !important;
    outline: none !important; box-shadow: none !important; text-transform: capitalize !important;
}
.fc .fc-button-primary:focus { box-shadow: none !important; }
.fc .fc-button-primary:not(:disabled).fc-button-active,
.fc .fc-button-primary:not(:disabled):active {
    background-color: #E91E63 !important; border-color: #E91E63 !important; color: #fff !important; font-weight: 700 !important;
}
.fc .fc-today-button {
    background-color: #E91E63 !important; border-color: #E91E63 !important; color: #fff !important; font-weight: 700 !important;
}
.fc .fc-today-button:disabled {
    background-color: rgba(233, 30, 99, 0.4) !important; border-color: transparent !important; color: #fff !important; cursor: default !important;
}

/* Événements */
.fc .fc-daygrid-event { border-radius: 6px !important; font-size: 0.72rem !important; font-weight: 500 !important; padding: 3px 6px !important; border: none !important; cursor: pointer !important; margin-bottom: 2px !important; overflow: hidden !important; }
.fc .fc-timegrid-event { border-radius: 6px !important; border: none !important; cursor: pointer !important; }
.fc .fc-daygrid-event:hover { filter: brightness(1.08); }
.fc .fc-daygrid-event, .fc .fc-timegrid-event { transition: transform 0.15s ease, filter 0.15s ease; }
.fc-custom-event { padding: 1px 2px; line-height: 1.3; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
.fc-custom-event .fc-ev-nom { font-weight: 700; font-size: 0.7rem; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.fc-custom-event .fc-ev-heure { font-weight: 400; font-size: 0.62rem; color: rgba(255, 255, 255, 0.85); }
.fc .fc-daygrid-event-dot { display: none !important; }

/* Survol cellule jour */
.fc .fc-daygrid-day:hover { background: rgba(233, 30, 99, 0.04) !important; }

/* Toolbar */
.fc .fc-toolbar.fc-header-toolbar { margin-bottom: 1rem !important; flex-wrap: wrap; gap: 0.5rem; }
@media (max-width: 640px) {
    .fc .fc-toolbar.fc-header-toolbar { flex-direction: column; align-items: flex-start; }
}

/* Vue liste */
.fc .fc-list-event:hover td { background: rgba(233, 30, 99, 0.05) !important; cursor: pointer !important; }

#cal-loading { transition: opacity 0.3s ease; }
</style>
@endpush

@section('content')
    {{-- En-tête : filtre statut + légende --}}
    <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
        <div class="relative">
            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M6 10h12M9 16h6"/>
            </svg>
            <select id="statut-filter"
                class="pl-9 pr-9 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#25101A] dark:text-white text-sm focus:border-yoonu-500 focus:ring-1 focus:ring-yoonu-500 outline-none appearance-none cursor-pointer">
                <option value="">Tous les statuts</option>
                <option value="en_attente">En attente</option>
                <option value="accepte">Accepté</option>
                <option value="refuse">Refusé</option>
                <option value="termine">Terminé</option>
                <option value="annule">Annulé</option>
            </select>
        </div>

        <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full" style="background:#F59E0B"></span>En attente</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full" style="background:#22C55E"></span>Accepté</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full" style="background:#EF4444"></span>Refusé</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full" style="background:#E91E63"></span>Terminé</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full" style="background:#9CA3AF"></span>Annulé</span>
        </div>
    </div>

    {{-- Carte calendrier --}}
    <div class="relative rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-5 overflow-hidden">
        <div id="cal-loading" class="absolute inset-0 z-10 flex items-center justify-center bg-white/70 dark:bg-[#25101A]/70 backdrop-blur-sm rounded-2xl pointer-events-none opacity-0">
            <div class="flex flex-col items-center gap-3">
                <svg class="w-8 h-8 animate-spin text-yoonu-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span class="text-xs text-gray-500 dark:text-gray-400">Chargement…</span>
            </div>
        </div>

        <div id="calendar" data-events-url="{{ route('pro.rendez-vous.events') }}"></div>
    </div>

    {{-- Modale détail d'un rendez-vous --}}
    <div id="event-modal" class="fixed inset-0 z-[99999] hidden items-center justify-center p-4">
        <div id="modal-backdrop" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
        <div id="modal-panel"
             class="relative w-full max-w-md rounded-2xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#25101A] shadow-2xl overflow-hidden scale-95 opacity-0 transition-all duration-200">
            <div id="modal-bar" class="h-1.5 w-full"></div>
            <div class="p-6">
                <div class="flex items-start justify-between mb-5">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-1">Rendez-vous</p>
                        <h3 id="modal-title" class="text-lg font-bold dark:text-white font-display"></h3>
                    </div>
                    <button id="modal-close" class="flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 dark:border-white/[0.08] text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/[0.06] transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-white/[0.03]">
                        <div class="flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-white/[0.06]">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div><p class="text-xs text-gray-400">Date &amp; heure</p><p id="modal-date" class="text-sm font-semibold dark:text-white"></p></div>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-white/[0.03]">
                        <div class="flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-white/[0.06]">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div><p class="text-xs text-gray-400">Patiente</p><p id="modal-patiente" class="text-sm font-semibold dark:text-white"></p></div>
                    </div>
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 dark:bg-white/[0.03]">
                        <div class="flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-white/[0.06]">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div><p class="text-xs text-gray-400">Motif</p><p id="modal-motif" class="text-sm font-semibold dark:text-white"></p></div>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-white/[0.03]">
                        <div class="flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-white/[0.06]">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div><p class="text-xs text-gray-400">Statut</p><span id="modal-statut" class="inline-flex items-center text-xs font-semibold px-2.5 py-0.5 rounded-full"></span></div>
                    </div>
                </div>

                <div class="mt-5 flex items-center gap-3">
                    <a id="modal-link" href="#" class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-yoonu-500 text-white text-sm font-semibold hover:bg-yoonu-600 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Voir le dossier
                    </a>
                    <button id="modal-close-btn" class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.08] text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/[0.06] transition">Fermer</button>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- ═══════════════════ SCRIPTS FULLCALENDAR (CDN global) ═══════════════════ --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales/fr.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl || typeof FullCalendar === 'undefined') return;

    const loadingEl = document.getElementById('cal-loading');
    const statutSel = document.getElementById('statut-filter');
    const modal     = document.getElementById('event-modal');
    const panel     = document.getElementById('modal-panel');
    const eventsUrl = calendarEl.dataset.eventsUrl;

    const badge = {
        'En attente': 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400',
        'Accepté'   : 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400',
        'Refusé'    : 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
        'Terminé'   : 'bg-pink-100 text-pink-700 dark:bg-pink-500/20 dark:text-pink-400',
        'Annulé'    : 'bg-gray-100 text-gray-600 dark:bg-gray-500/20 dark:text-gray-400',
    };

    const showLoading = () => { loadingEl.style.opacity = '1'; };
    const hideLoading = () => { loadingEl.style.opacity = '0'; };

    function closeModal() {
        panel.classList.add('scale-95', 'opacity-0');
        setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); }, 180);
    }

    function openModal(event) {
        const p = event.extendedProps;
        document.getElementById('modal-title').textContent    = p.patiente;
        document.getElementById('modal-patiente').textContent = p.email || p.patiente;
        document.getElementById('modal-motif').textContent    = p.motif || '—';
        document.getElementById('modal-link').href            = p.showUrl;

        const d = event.start
            ? event.start.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
            : '—';
        document.getElementById('modal-date').textContent = p.heure ? (d + ' · ' + p.heure) : d;

        const st = document.getElementById('modal-statut');
        st.className = 'inline-flex items-center text-xs font-semibold px-2.5 py-0.5 rounded-full ' + (badge[p.statut] || badge['Annulé']);
        st.textContent = p.statut;

        document.getElementById('modal-bar').style.backgroundColor = event.backgroundColor || '#E91E63';

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        requestAnimationFrame(() => panel.classList.remove('scale-95', 'opacity-0'));
    }

    document.getElementById('modal-close').addEventListener('click', closeModal);
    document.getElementById('modal-close-btn').addEventListener('click', closeModal);
    document.getElementById('modal-backdrop').addEventListener('click', closeModal);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'fr',
        initialView: 'dayGridMonth',
        height: 'auto',
        firstDay: 1,
        nowIndicator: true,
        navLinks: true,
        dayMaxEvents: 3,
        displayEventTime: false,
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,listWeek' },
        buttonText: { today: "Aujourd'hui", month: 'Mois', week: 'Semaine', list: 'Liste' },

        events: function (info, successCb, failureCb) {
            showLoading();
            let url = eventsUrl;
            const s = statutSel ? statutSel.value : '';
            if (s) url += '?statut=' + encodeURIComponent(s);
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => { data.forEach(e => e.display = 'block'); hideLoading(); successCb(data); })
                .catch(() => { hideLoading(); failureCb(); });
        },

        eventContent: function (arg) {
            if (arg.view.type === 'dayGridMonth') {
                const p = arg.event.extendedProps;
                const w = document.createElement('div');
                w.className = 'fc-custom-event';
                w.innerHTML = '<div class="fc-ev-nom">' + (p.patiente || arg.event.title) + '</div>' +
                              (p.heure ? '<div class="fc-ev-heure">' + p.heure + '</div>' : '');
                return { domNodes: [w] };
            }
            return true;
        },

        eventClick: function (info) { info.jsEvent.preventDefault(); openModal(info.event); },
        navLinkDayClick: function (date) { calendar.changeView('timeGridWeek', date); },
        loading: function (isLoading) { isLoading ? showLoading() : hideLoading(); },
    });

    calendar.render();

    if (statutSel) statutSel.addEventListener('change', () => calendar.refetchEvents());

    new MutationObserver(() => calendar.render())
        .observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
});
</script>
@endpush
