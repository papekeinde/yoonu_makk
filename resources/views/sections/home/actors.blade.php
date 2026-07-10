@php
    // Quatre acteurs = quatre rôles distincts (pas une séquence : pas de numéros).
    // [étiquette de rôle, titre, descriptif, tracé d'icône filaire]
    $acteurs = [
        ['Patiente', 'Femmes', 'Déclarer vos symptômes, comprendre votre situation, recevoir une orientation claire et conserver votre historique de santé.',
            'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.118a7.5 7.5 0 0115 0A17.9 17.9 0 0112 21.75c-2.676 0-5.216-.584-7.5-1.632z'],
        ['Proche', 'Accompagnants', 'Mieux comprendre les situations, repérer les signaux d\'alerte importants et soutenir sans se substituer au professionnel.',
            'M18 18.72a9.1 9.1 0 003.74-.48 3 3 0 00-4.68-2.72m.94 3.2c0 .22-.01.44-.04.67A11.94 11.94 0 0112 21c-2.17 0-4.2-.58-5.96-1.58M18 18.72a5.97 5.97 0 00-.94-3.2m0 0A6 6 0 0012 12.75a6 6 0 00-5.06 2.77m0 0a3 3 0 00-4.68 2.72c1.18.51 2.46.69 3.74.48m.94-3.2a5.97 5.97 0 00-.94 3.2M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z'],
        ['Praticien', 'Gynécologues', 'Recevoir des demandes mieux qualifiées, trier et prioriser les cas, et gérer un dossier patiente plus structuré.',
            'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.11c0-1.14-.85-2.1-1.98-2.2-.37-.03-.74-.05-1.12-.08m-5.8 0A2.25 2.25 0 0113.5 2.25H15c1.01 0 1.87.67 2.15 1.59m-5.8 0c-.38.02-.75.05-1.12.08C9.1 4.01 8.25 4.97 8.25 6.11v2.14m0 0H4.88c-.63 0-1.13.5-1.13 1.13v11.25c0 .62.5 1.12 1.13 1.12h9.75c.62 0 1.12-.5 1.12-1.12V9.375c0-.62-.5-1.13-1.12-1.13H8.25z'],
        ['Pilotage', 'Administration', 'Piloter la plateforme, suivre l\'activité, gérer les profils et garantir la cohérence du service.',
            'M3 13.13C3 12.5 3.5 12 4.13 12h2.25c.62 0 1.12.5 1.12 1.13v6.75c0 .62-.5 1.12-1.12 1.12H4.13C3.5 21 3 20.5 3 19.88v-6.75zM9.75 8.63c0-.63.5-1.13 1.13-1.13h2.25c.62 0 1.12.5 1.12 1.13v11.25c0 .62-.5 1.12-1.12 1.12h-2.25c-.63 0-1.13-.5-1.13-1.12V8.63zM16.5 4.13c0-.63.5-1.13 1.13-1.13h2.25C20.5 3 21 3.5 21 4.13v15.75c0 .62-.5 1.12-1.12 1.12h-2.25c-.63 0-1.13-.5-1.13-1.12V4.13z'],
    ];
@endphp

<section id="acteurs" class="py-16 sm:py-24">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6">
        <div class="mb-12 max-w-3xl scroll-reveal">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-mauve-400">Pour qui</p>
            <h2 class="mt-2 font-display text-3xl font-bold tracking-tight text-white sm:text-4xl">Une plateforme, quatre expériences adaptées.</h2>
            <p class="mt-3 text-base leading-7 text-mauve-300">Chaque acteur du parcours de soin trouve sa place et son utilité dans YOONU JIGEEN.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($acteurs as [$role, $titre, $desc, $path])
                <div class="scroll-reveal morph-card rounded-2xl border border-white/10 bg-ink-800 p-6 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-yoonu-500/15 text-yoonu-300">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $path }}"/></svg>
                        </span>
                        <span class="rounded-full border border-white/15 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-yoonu-300">{{ $role }}</span>
                    </div>
                    <h3 class="mt-4 font-display text-lg font-bold text-white">{{ $titre }}</h3>
                    <p class="mt-2 text-sm leading-6 text-mauve-300">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
