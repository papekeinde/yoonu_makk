@extends('layouts.dashboard')

@section('title', 'Statistiques')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Inscriptions sur 12 mois --}}
        <div class="lg:col-span-2 rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 font-display">Inscriptions (12 derniers mois)</h3>
            <div class="h-72"><canvas id="chartInscriptions"></canvas></div>
        </div>

        {{-- Profils des femmes --}}
        <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 font-display">Profils des femmes</h3>
            <div class="h-72 flex items-center"><canvas id="chartProfils"></canvas></div>
        </div>

        {{-- Rendez-vous par statut --}}
        <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 font-display">Rendez-vous par statut</h3>
            <div class="h-64"><canvas id="chartRdv"></canvas></div>
        </div>

        {{-- Demandes par statut --}}
        <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 font-display">Demandes d'adhésion</h3>
            <div class="h-64 flex items-center"><canvas id="chartDemandes"></canvas></div>
        </div>

        {{-- Publications --}}
        <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 font-display">Contenus publiés / brouillons</h3>
            <div class="h-64"><canvas id="chartPublications"></canvas></div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    (function () {
        // Palette accordée à la charte : rose de marque + triage sourd (terracotta/ocre/sauge).
        const pink = '#E91E63', purple = '#F48FB1', amber = '#C08A33',
              green = '#5F8568', red = '#BC4A3C', blue = '#AD1457', gray = '#9CA3AF';
        const isDark = document.documentElement.classList.contains('dark');
        const grid = isDark ? 'rgba(255,255,255,.08)' : 'rgba(0,0,0,.06)';
        const tick = isDark ? '#C9B8C0' : '#6B7280';
        Chart.defaults.color = tick;
        Chart.defaults.font.family = "'Manrope', sans-serif";

        const axes = { x: { grid: { color: grid } }, y: { beginAtZero: true, grid: { color: grid }, ticks: { precision: 0 } } };

        new Chart(document.getElementById('chartInscriptions'), {
            type: 'line',
            data: {
                labels: @json($moisLabels),
                datasets: [{
                    label: 'Inscriptions', data: @json($moisValeurs),
                    borderColor: pink, backgroundColor: 'rgba(233,30,99,.12)',
                    fill: true, tension: .35, pointRadius: 3, pointBackgroundColor: pink,
                }],
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: axes },
        });

        new Chart(document.getElementById('chartProfils'), {
            type: 'doughnut',
            data: { labels: @json($profils['labels']), datasets: [{ data: @json($profils['data']), backgroundColor: [pink, purple, gray], borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, cutout: '62%' },
        });

        new Chart(document.getElementById('chartRdv'), {
            type: 'bar',
            data: { labels: @json($rdvParStatut['labels']), datasets: [{ label: 'Rendez-vous', data: @json($rdvParStatut['data']), backgroundColor: [amber, green, red, blue, gray], borderRadius: 6 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: axes },
        });

        new Chart(document.getElementById('chartDemandes'), {
            type: 'doughnut',
            data: { labels: @json($demandesParStatut['labels']), datasets: [{ data: @json($demandesParStatut['data']), backgroundColor: [amber, green, red], borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, cutout: '62%' },
        });

        new Chart(document.getElementById('chartPublications'), {
            type: 'bar',
            data: {
                labels: @json($publications['labels']),
                datasets: [
                    { label: 'Publiés', data: @json($publications['publies']), backgroundColor: green, borderRadius: 6 },
                    { label: 'Brouillons', data: @json($publications['brouillons']), backgroundColor: gray, borderRadius: 6 },
                ],
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, scales: { x: { stacked: true, grid: { color: grid } }, y: { stacked: true, beginAtZero: true, grid: { color: grid }, ticks: { precision: 0 } } } },
        });
    })();
</script>
@endpush
