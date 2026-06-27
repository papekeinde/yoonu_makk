@php
    $platformUrl = $frontendUrl ?? '#telechargement';
    $profils = [
        ['Grossesse', 'Suivi semaine par semaine', '🤰'],
        ['Ménopause', 'Comprendre et anticiper', '🌿'],
        ['Découverte', 'S\'informer en confiance', '💗'],
    ];
@endphp

<section id="hero" class="relative overflow-hidden">
    {{-- Fonds dégradés roses --}}
    <div class="absolute inset-0 -z-10 bg-gradient-to-b from-yoonu-50 via-rose-50/50 to-white"></div>
    <div class="absolute -top-24 right-0 -z-10 h-[480px] w-[480px] rounded-full bg-yoonu-100/60 blur-3xl"></div>
    <div class="absolute -left-24 top-40 -z-10 h-[360px] w-[360px] rounded-full bg-rose-100/50 blur-3xl"></div>

    <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 pb-20 pt-32 sm:px-6 sm:pt-36 lg:grid-cols-2 lg:gap-16 lg:pb-28">
        {{-- Colonne gauche --}}
        <div>
            <div class="inline-flex items-center gap-2 rounded-full border border-yoonu-200 bg-white/70 px-4 py-2 backdrop-blur animate-fade-in animate-delay-100">
                <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-yoonu-500"></span>
                <span class="text-xs font-semibold uppercase tracking-[0.16em] text-yoonu-700">Santé des femmes · Sénégal</span>
            </div>

            <h1 class="mt-6 font-display text-4xl font-extrabold leading-[1.1] tracking-tight text-yoonu-900 sm:text-5xl animate-fade-in animate-delay-200">
                Un parcours de soin
                <span class="relative inline-block text-yoonu-600">
                    clair
                    <svg class="absolute -bottom-1.5 left-0 w-full" viewBox="0 0 200 12" fill="none" preserveAspectRatio="none">
                        <path d="M 2 8 Q 100 2 198 8" stroke="#E91E63" stroke-width="3" stroke-linecap="round" fill="none"/>
                    </svg>
                </span>,
                pour chaque femme.
            </h1>

            <p class="mt-6 max-w-lg text-base leading-8 text-slate-600 animate-fade-in animate-delay-300">
                YOONU MAKK aide les patientes, les accompagnants et les praticiens à mieux qualifier
                une situation, à organiser la suite et à orienter rapidement vers le bon niveau de
                prise en charge — sans se substituer au diagnostic médical.
            </p>

            <div class="mt-8 flex flex-wrap gap-3 animate-fade-in animate-delay-400">
                <a href="{{ $platformUrl }}"
                   class="rounded-xl bg-yoonu-700 px-7 py-3.5 text-sm font-bold text-white shadow-[0_6px_24px_rgba(173,20,87,0.32)] transition-all hover:bg-yoonu-900 hover:scale-[1.02]">
                    Accéder à la plateforme
                </a>
                <a href="#process"
                   class="rounded-xl border border-yoonu-200 bg-white px-7 py-3.5 text-sm font-semibold text-yoonu-700 transition-all hover:bg-yoonu-50">
                    Voir comment ça marche
                </a>
            </div>

            {{-- Indicateurs de confiance --}}
            <div class="mt-10 flex flex-wrap gap-3 animate-fade-in animate-delay-500">
                <div class="flex items-center gap-2 rounded-xl border border-yoonu-100 bg-white/80 px-4 py-2.5 shadow-soft backdrop-blur">
                    <span class="text-base">⚡</span>
                    <div>
                        <span class="block text-xs font-bold text-yoonu-900">Orientation immédiate</span>
                        <span class="block text-[11px] text-slate-500">Urgence · rapide · standard</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 rounded-xl border border-yoonu-100 bg-white/80 px-4 py-2.5 shadow-soft backdrop-blur">
                    <span class="text-base">🔒</span>
                    <div>
                        <span class="block text-xs font-bold text-yoonu-900">Données protégées</span>
                        <span class="block text-[11px] text-slate-500">Historique confidentiel</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 rounded-xl border border-yoonu-100 bg-white/80 px-4 py-2.5 shadow-soft backdrop-blur">
                    <span class="text-base">🤝</span>
                    <div>
                        <span class="block text-xs font-bold text-yoonu-900">Lien praticien</span>
                        <span class="block text-[11px] text-slate-500">Demandes mieux préparées</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Colonne droite — carte des parcours --}}
        <div class="animate-fade-in-right animate-delay-300">
            <div class="rounded-[2rem] border border-yoonu-100 bg-white/80 p-6 shadow-[0_24px_60px_rgba(233,30,99,0.12)] backdrop-blur-xl sm:p-7">
                <div class="flex items-center justify-between">
                    <p class="font-display text-lg font-bold text-yoonu-900">Trois parcours, une plateforme</p>
                    <span class="rounded-full bg-yoonu-50 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-yoonu-700">Profils</span>
                </div>

                <div class="mt-5 space-y-3">
                    @foreach ($profils as [$titre, $desc, $emoji])
                        <div class="flex items-center gap-4 rounded-2xl border border-yoonu-100 bg-gradient-to-r from-yoonu-50/70 to-white p-4 transition-all hover:border-yoonu-200 hover:shadow-soft">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-xl shadow-soft">{{ $emoji }}</span>
                            <div>
                                <p class="font-semibold text-yoonu-900">{{ $titre }}</p>
                                <p class="text-xs text-slate-500">{{ $desc }}</p>
                            </div>
                            <svg class="ml-auto h-5 w-5 text-yoonu-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 grid grid-cols-3 gap-3">
                    @foreach ([['3', 'Parcours'], ['4', 'Acteurs'], ['24/7', 'Assistant']] as [$val, $label])
                        <div class="rounded-2xl border border-yoonu-100 bg-yoonu-50/60 p-4 text-center">
                            <div class="font-display text-2xl font-extrabold text-yoonu-700">{{ $val }}</div>
                            <div class="mt-0.5 text-[11px] text-slate-500">{{ $label }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
