@php
    $etapes = [
        ['01', 'Décrire', 'Vous exprimez votre situation en langage simple, avec les informations clés : symptômes, ancienneté, intensité.'],
        ['02', 'Qualifier', 'L\'assistant informe et structure votre demande, rappelle les signaux d\'alerte et prépare les questions utiles.'],
        ['03', 'Orienter', 'Vous recevez une orientation claire : urgence, consultation rapide ou rendez-vous standard avec le bon professionnel.'],
        ['04', 'Suivre', 'Vous conservez votre historique, recevez des recommandations et restez informée de votre parcours de santé.'],
    ];
@endphp

<section id="process" class="py-16 sm:py-24">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6">
        <div class="mb-12 max-w-3xl scroll-reveal">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-mauve-400">Comment ça marche</p>
            <h2 class="mt-2 font-display text-3xl font-bold tracking-tight text-white sm:text-4xl">Quatre étapes, de la question à l'action juste.</h2>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            @foreach ($etapes as [$num, $titre, $desc])
                <div class="scroll-reveal morph-card rounded-2xl border border-white/10 bg-ink-800 p-6 transition-all duration-300">
                    <span class="inline-flex rounded-full bg-yoonu-500/15 px-3 py-1 text-sm font-bold text-yoonu-300">{{ $num }}</span>
                    <h3 class="mt-3 font-display text-lg font-bold text-white">{{ $titre }}</h3>
                    <p class="mt-2 text-sm leading-6 text-mauve-300">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-16 sm:py-24">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6">
        <div class="layout-morph-section overflow-hidden rounded-[2rem] border border-white/10 bg-gradient-to-br from-ink-800 via-ink-800 to-ink-900 p-6 sm:p-8">
            <div class="grid gap-4 lg:grid-cols-[1.3fr_.9fr] lg:items-end">
                <div class="layout-morph-intro">
                    <span class="text-xs font-bold uppercase tracking-[0.18em] text-mauve-400">Pourquoi YOONU JIGEEN</span>
                    <h2 class="mt-2 font-display text-3xl font-bold tracking-tight text-white sm:text-4xl">Une plateforme pensée pour inspirer confiance.</h2>
                    <p class="mt-3 max-w-3xl text-[15px] leading-8 text-mauve-300">YOONU JIGEEN clarifie la demande de la patiente, structure l'information utile pour le gynécologue et rend la prise en charge plus lisible, sans brouiller le cadre médical.</p>
                </div>
                <div class="layout-morph-accent rounded-2xl border border-white/10 bg-white/5 p-5 text-sm font-semibold leading-7 text-white backdrop-blur-sm">
                    Langage clair, parcours plus lisibles et priorisation assumée des situations sensibles.
                </div>
            </div>

            <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['Orientation lisible', 'La patiente sait si elle doit consulter vite, prendre un rendez-vous standard ou se rendre aux urgences.'],
                    ['Coordination praticien', 'Le gynécologue reçoit des demandes mieux préparées et peut prioriser les cas plus rapidement.'],
                    ['Suivi structuré', 'Historique, symptômes, dossiers et rappels donnent une continuité utile à la relation de soin.'],
                    ['Valeur mesurable', 'Le praticien gagne du temps, améliore le filtrage en amont et suit mieux ses patientes.'],
                ] as [$titre, $desc])
                    <div class="layout-morph-card rounded-2xl border border-white/10 bg-ink-800 p-5">
                        <h3 class="font-display text-lg font-bold text-white">{{ $titre }}</h3>
                        <p class="mt-2 text-sm leading-7 text-mauve-300">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 grid gap-3 md:grid-cols-3">
                @foreach ([
                    ['Plus d\'orientation, moins d\'ambiguïté', 'Les écrans et messages visent d\'abord la compréhension et l\'action juste.'],
                    ['Relation praticien mieux préparée', 'Les informations utiles remontent plus tôt, avant la consultation.'],
                    ['Expérience responsable', 'Le service rappelle ses limites et renvoie vers l\'urgence si nécessaire.'],
                ] as [$titre, $desc])
                    <div class="layout-morph-card rounded-2xl border border-white/10 bg-white/5 p-4 text-sm leading-7 text-mauve-300">
                        <strong class="block text-white">{{ $titre }}</strong>{{ $desc }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
