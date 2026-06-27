@php
    $features = [
        ['🩺', 'Pré-triage & orientation', 'Décrivez votre situation en langage simple ; l\'assistant qualifie la demande et oriente vers le bon niveau : urgence, consultation rapide ou rendez-vous standard.'],
        ['📔', 'Suivi & historique', 'Symptômes, dossiers et rappels conservés au même endroit pour une continuité de soin claire au fil du temps.'],
        ['💬', 'Assistant santé', 'Un compagnon disponible à tout moment pour informer, structurer la demande et rappeler les signaux d\'alerte.'],
        ['🤝', 'Intermédiation praticien', 'Mise en relation avec des professionnels de santé selon le profil, avec des demandes mieux préparées en amont.'],
        ['📅', 'Rendez-vous', 'Prise de rendez-vous et coordination avec le bon professionnel, sans démarches inutiles.'],
        ['📚', 'Éducation & prévention', 'Contenus fiables sur la grossesse, la ménopause et la santé des femmes, adaptés à chaque parcours.'],
    ];
@endphp

<section id="fonctionnalites" class="bg-slate-50 py-16 sm:py-24">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6">
        <div class="mb-12 max-w-3xl scroll-reveal">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Fonctionnalités</p>
            <h2 class="mt-2 font-display text-3xl font-bold tracking-tight text-yoonu-900 sm:text-4xl">Des outils pensés pour vous.</h2>
            <p class="mt-3 text-base leading-7 text-slate-600">Tout ce qu'il faut pour comprendre sa situation, agir au bon moment et garder le lien avec son praticien.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($features as $i => [$icon, $titre, $desc])
                <div class="scroll-reveal morph-card rounded-2xl border border-yoonu-100 bg-white p-6 shadow-soft transition-all duration-300">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-yoonu-50 text-2xl">{{ $icon }}</div>
                    <span class="mt-4 inline-flex rounded-full bg-yoonu-50 px-3 py-1 text-xs font-bold text-yoonu-700">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <h3 class="mt-3 font-display text-lg font-bold text-yoonu-900">{{ $titre }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
