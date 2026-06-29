@php
    // [tracé d'icône filaire, titre, descriptif]
    $features = [
        ['M3.79 2.94A49 49 0 0112 2.25c2.8 0 5.54.24 8.21.69a1.86 1.86 0 011.54 1.83v1.05a3 3 0 01-.88 2.12l-6.18 6.18a1.5 1.5 0 00-.44 1.06v2.93a3 3 0 01-1.66 2.68l-1.99 1a.75.75 0 01-1.08-.67v-5.94a1.5 1.5 0 00-.44-1.06L2.53 7.94a3 3 0 01-.88-2.12V4.77c0-.9.65-1.66 1.54-1.83z',
            'Pré-triage & orientation', 'Décrivez votre situation en langage simple ; l\'assistant qualifie la demande et oriente vers le bon niveau : urgence, consultation rapide ou rendez-vous standard.'],
        ['M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.11c0-1.14-.85-2.1-1.98-2.2-.37-.03-.74-.05-1.12-.08m-5.8 0A2.25 2.25 0 0113.5 2.25H15c1.01 0 1.87.67 2.15 1.59m-5.8 0c-.38.02-.75.05-1.12.08C9.1 4.01 8.25 4.97 8.25 6.11v2.14m0 0H4.88c-.63 0-1.13.5-1.13 1.13v11.25c0 .62.5 1.12 1.13 1.12h9.75c.62 0 1.12-.5 1.12-1.12V9.375c0-.62-.5-1.13-1.12-1.13H8.25z',
            'Suivi & historique', 'Symptômes, dossiers et rappels conservés au même endroit pour une continuité de soin claire au fil du temps.'],
        ['M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.12 2.99 2.7 3.23.85.13 1.7.22 2.57.28v3.23l3.39-3.39c.18-.18.43-.28.69-.27 1.62-.05 3.23-.18 4.83-.4 1.58-.24 2.7-1.62 2.7-3.22V6.74c0-1.6-1.12-2.98-2.7-3.22A48.4 48.4 0 0012 3.25c-2.39 0-4.74.18-7.05.52-1.58.24-2.7 1.62-2.7 3.22v5.78z',
            'Assistant santé', 'Un compagnon disponible à tout moment pour informer, structurer la demande et rappeler les signaux d\'alerte.'],
        ['M15 19.13a9.1 9.1 0 002.63.37 9.34 9.34 0 004.12-.95 4.13 4.13 0 00-7.53-2.5M15 19.13v-.01c0-1.11-.29-2.16-.79-3.07M15 19.13v.11A12.32 12.32 0 018.62 21c-2.33 0-4.51-.65-6.37-1.77v-.1a6.38 6.38 0 0111.96-3.07M12 6.38a3.38 3.38 0 11-6.75 0 3.38 3.38 0 016.75 0zm8.25 2.25a2.63 2.63 0 11-5.25 0 2.63 2.63 0 015.25 0z',
            'Intermédiation praticien', 'Mise en relation avec des professionnels de santé selon le profil, avec des demandes mieux préparées en amont.'],
        ['M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15z',
            'Rendez-vous', 'Prise de rendez-vous et coordination avec le bon professionnel, sans démarches inutiles.'],
        ['M12 6.04A8.97 8.97 0 006 3.75c-1.05 0-2.06.18-3 .51v14.25A8.99 8.99 0 016 18c2.3 0 4.41.87 6 2.29m0-14.25a8.97 8.97 0 016-2.29c1.05 0 2.06.18 3 .51v14.25A8.99 8.99 0 0018 18a8.97 8.97 0 00-6 2.29m0-14.25v14.25',
            'Éducation & prévention', 'Contenus fiables sur la grossesse, la ménopause et la santé des femmes, adaptés à chaque parcours.'],
    ];
@endphp

<section id="fonctionnalites" class="bg-ink-950 py-16 sm:py-24">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6">
        <div class="mb-12 max-w-3xl scroll-reveal">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-mauve-400">Fonctionnalités</p>
            <h2 class="mt-2 font-display text-3xl font-bold tracking-tight text-white sm:text-4xl">Des outils pensés pour vous.</h2>
            <p class="mt-3 text-base leading-7 text-mauve-300">Tout ce qu'il faut pour comprendre sa situation, agir au bon moment et garder le lien avec son praticien.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($features as [$path, $titre, $desc])
                <div class="scroll-reveal morph-card rounded-2xl border border-white/10 bg-ink-800 p-6 transition-all duration-300">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-yoonu-500/15 text-yoonu-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $path }}"/></svg>
                    </div>
                    <h3 class="mt-4 font-display text-lg font-bold text-white">{{ $titre }}</h3>
                    <p class="mt-2 text-sm leading-6 text-mauve-300">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
