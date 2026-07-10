@php
    // L'échelle de triage, déployée. Chaque niveau dit : ce que ça signifie,
    // et dans quels cas il s'applique. Le niveau « urgence » porte les signaux d'alerte.
    $tiers = [
        [
            'cle'    => 'standard',
            'niveau' => 'Rendez-vous standard',
            'delai'  => 'À planifier',
            'sens'   => 'Aucun signe de gravité. On organise un suivi serein avec le bon professionnel, au bon rythme.',
            'cas'    => ['Suivi de grossesse planifié', 'Question de prévention', 'Renouvellement, contrôle de routine'],
        ],
        [
            'cle'    => 'rapide',
            'niveau' => 'Consultation rapide',
            'delai'  => 'Sous 48 h',
            'sens'   => 'Une situation à ne pas laisser traîner. On conseille de prendre rendez-vous à brève échéance.',
            'cas'    => ['Douleurs modérées qui persistent', 'Symptôme nouveau et gênant', 'Doute qui demande un avis'],
        ],
        [
            'cle'     => 'urgence',
            'niveau'  => 'Urgence',
            'delai'   => 'Maintenant',
            'sens'    => 'Des signaux d\'alerte sont détectés. On oriente vers les urgences, sans attendre.',
            'alertes' => [
                'Douleurs très intenses et soudaines',
                'Saignements abondants inhabituels',
                'Fièvre persistante au-delà de 38,5 °C',
                'Vertiges ou malaise importants',
                'Signes d\'infection évidents',
            ],
        ],
    ];

    $pretriage = [
        'Description simple des symptômes et de leur ancienneté',
        'Questions adaptées à la situation décrite',
        'Détection automatique des signaux d\'alerte',
        'Orientation ou conseil immédiat en cas d\'urgence',
    ];
@endphp

<section id="securite" class="py-16 sm:py-24">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6">
        <div class="mb-12 max-w-3xl scroll-reveal">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-mauve-400">Sécurité et qualification</p>
            <h2 class="mt-2 font-display text-3xl font-bold tracking-tight text-white sm:text-4xl">Identifier les situations urgentes d'abord.</h2>
            <p class="mt-3 text-base leading-7 text-mauve-300">Chaque situation est qualifiée, puis dirigée vers l'un des trois niveaux de prise en charge. Le plus pressant prime toujours.</p>
        </div>

        {{-- L'ÉCHELLE, matérialisée — le niveau « urgence » domine --}}
        <div class="grid gap-5 lg:grid-cols-3 lg:items-start scroll-reveal">
            @foreach ($tiers as $t)
                @php $c = $t['cle']; $estUrgence = $c === 'urgence'; @endphp
                <div class="overflow-hidden rounded-[1.75rem] border bg-ink-800
                            {{ $estUrgence ? 'border-urgence-500/40 ring-1 ring-urgence-500/30 lg:-translate-y-2' : 'border-white/10' }}">
                    {{-- Bandeau de niveau --}}
                    <div class="flex items-center justify-between px-5 py-4 {{ $estUrgence ? 'bg-urgence-500' : 'bg-'.$c.'-500/15' }}">
                        <span class="font-display text-base font-extrabold {{ $estUrgence ? 'text-white' : 'text-'.$c.'-200' }}">{{ $t['niveau'] }}</span>
                        <span class="rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-wider
                                     {{ $estUrgence ? 'bg-white/20 text-white' : 'bg-white/10 text-'.$c.'-200' }}">{{ $t['delai'] }}</span>
                    </div>

                    <div class="px-5 py-5">
                        <p class="text-sm leading-7 text-mauve-300">{{ $t['sens'] }}</p>

                        @if ($estUrgence)
                            <p class="mt-4 text-[11px] font-bold uppercase tracking-wide text-urgence-200">Signaux d'alerte</p>
                            <ul class="mt-2 space-y-1.5">
                                @foreach ($t['alertes'] as $a)
                                    <li class="flex items-start gap-2 text-sm text-mauve-200">
                                        <span class="mt-1.5 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-urgence-500"></span>{{ $a }}
                                    </li>
                                @endforeach
                            </ul>
                            <div class="mt-4 rounded-xl bg-urgence-500/15 px-4 py-3">
                                <p class="text-[11px] font-bold uppercase leading-4 tracking-wide text-urgence-200">En cas d'urgence : appelez le 1515 (SAMU) ou rendez-vous aux urgences.</p>
                            </div>
                        @else
                            <p class="mt-4 text-[11px] font-bold uppercase tracking-wide text-{{ $c }}-200">Par exemple</p>
                            <ul class="mt-2 space-y-1.5">
                                @foreach ($t['cas'] as $ex)
                                    <li class="flex items-start gap-2 text-sm text-mauve-300">
                                        <span class="mt-1.5 h-1.5 w-1.5 flex-shrink-0 rounded-full bg-{{ $c }}-500"></span>{{ $ex }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Comment la qualification se construit + rappel du cadre --}}
        <div class="mt-12 grid gap-6 lg:grid-cols-[1.15fr_.85fr] lg:items-stretch scroll-reveal">
            <div class="rounded-2xl border border-white/10 bg-ink-800 p-6">
                <h3 class="font-display text-lg font-bold text-white">Comment se construit le pré-triage</h3>
                <ol class="mt-4 grid gap-3 sm:grid-cols-2">
                    @foreach ($pretriage as $i => $point)
                        <li class="flex items-start gap-3">
                            <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-yoonu-500/15 font-display text-xs font-bold text-yoonu-300">{{ $i + 1 }}</span>
                            <p class="text-sm leading-6 text-mauve-300">{{ $point }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>

            <div class="flex flex-col justify-center rounded-2xl border border-yoonu-500/30 bg-yoonu-500/[0.06] p-6">
                <span class="text-xs font-bold uppercase tracking-[0.16em] text-yoonu-300">À retenir</span>
                <p class="mt-2 text-sm leading-7 text-mauve-200">
                    YOONU JIGEEN <strong class="text-white">informe et oriente</strong>. Il ne pose pas de diagnostic
                    et ne remplace jamais l'avis d'un professionnel de santé.
                </p>
            </div>
        </div>
    </div>
</section>
