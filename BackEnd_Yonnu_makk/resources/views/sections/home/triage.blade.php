<section id="securite" class="py-16 sm:py-24">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6">
        <div class="mb-12 max-w-3xl scroll-reveal">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Sécurité et qualification</p>
            <h2 class="mt-2 font-display text-3xl font-bold tracking-tight text-yoonu-900 sm:text-4xl">Identifier les situations urgentes d'abord.</h2>
            <p class="mt-3 text-base leading-7 text-slate-600">Un processus clair et structuré pour qualifier chaque situation et la diriger vers le bon niveau d'urgence.</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-2 lg:items-start scroll-reveal">
            <div class="space-y-4">
                <h3 class="font-display text-xl font-bold text-yoonu-900">Pré-triage structuré</h3>
                <ul class="space-y-3">
                    @foreach ([
                        'Description simple des symptômes et de leur ancienneté',
                        'Questions intelligentes adaptées à la situation',
                        'Détection automatique des signaux d\'alerte',
                        'Orientation ou conseil immédiat en cas d\'urgence',
                    ] as $point)
                        <li class="flex items-start gap-3">
                            <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-yoonu-100 text-xs font-bold text-yoonu-700">✓</span>
                            <p class="text-sm leading-6 text-slate-600">{{ $point }}</p>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-6 rounded-2xl border border-yoonu-200 bg-yoonu-50/60 p-5">
                    <p class="text-sm leading-7 text-slate-700">
                        <strong class="text-yoonu-900">À retenir :</strong> YOONU MAKK informe et oriente. Il ne pose pas
                        de diagnostic et ne remplace jamais l'avis d'un professionnel de santé.
                    </p>
                </div>
            </div>

            <div class="rounded-2xl border border-red-200 bg-red-50 p-6">
                <div class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-red-100 text-lg">⚠️</span>
                    <h3 class="font-display text-lg font-bold text-red-900">Signaux d'alerte</h3>
                </div>
                <p class="mt-3 text-sm text-red-700">Consultez rapidement en cas de :</p>
                <ul class="mt-4 space-y-2">
                    @foreach ([
                        'Douleurs très intenses et soudaines',
                        'Saignements abondants inhabituels',
                        'Fièvre persistante supérieure à 38,5 °C',
                        'Vertiges ou malaise importants',
                        'Signes d\'infection évidents',
                    ] as $alerte)
                        <li class="text-sm text-red-700">• {{ $alerte }}</li>
                    @endforeach
                </ul>
                <div class="mt-4 border-t border-red-200 pt-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-red-900">En cas d'urgence : appelez le 1515 (SAMU) ou rendez-vous aux urgences.</p>
                </div>
            </div>
        </div>
    </div>
</section>
