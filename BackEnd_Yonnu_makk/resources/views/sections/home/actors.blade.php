@php
    $acteurs = [
        ['01', 'Femmes', 'Déclarer vos symptômes, comprendre votre situation, recevoir une orientation claire et conserver votre historique de santé.'],
        ['02', 'Accompagnants', 'Mieux comprendre les situations, repérer les signaux d\'alerte importants et soutenir sans se substituer au professionnel.'],
        ['03', 'Gynécologues', 'Recevoir des demandes mieux qualifiées, trier et prioriser les cas, et gérer un dossier patiente plus structuré.'],
        ['04', 'Administration', 'Piloter la plateforme, suivre l\'activité, gérer les profils et garantir la cohérence du service.'],
    ];
@endphp

<section id="acteurs" class="py-16 sm:py-24">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6">
        <div class="mb-12 max-w-3xl scroll-reveal">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Pour qui</p>
            <h2 class="mt-2 font-display text-3xl font-bold tracking-tight text-yoonu-900 sm:text-4xl">Une plateforme, quatre expériences adaptées.</h2>
            <p class="mt-3 text-base leading-7 text-slate-600">Chaque acteur du parcours de soin trouve sa place et son utilité dans YOONU MAKK.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($acteurs as [$num, $titre, $desc])
                <div class="scroll-reveal morph-card rounded-2xl border border-yoonu-100 bg-white p-6 shadow-soft transition-all duration-300">
                    <div class="mb-5 h-1.5 w-16 rounded-full bg-gradient-to-r from-yoonu-300 via-yoonu-500 to-yoonu-700"></div>
                    <span class="inline-flex rounded-full bg-yoonu-50 px-3 py-1 text-xs font-bold text-yoonu-700">{{ $num }}</span>
                    <h3 class="mt-3 font-display text-lg font-bold text-yoonu-900">{{ $titre }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
