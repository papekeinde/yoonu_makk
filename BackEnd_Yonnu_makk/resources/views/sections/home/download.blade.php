@php
    $platformUrl = $frontendUrl ?? url('/');
    $avantages = [
        'Accès rapide à votre dossier et à votre historique',
        'Notifications pour vos rendez-vous et rappels',
        'Consultation de vos données même hors ligne',
        'Expérience optimisée pour téléphone et tablette',
    ];
@endphp

<section id="telechargement" class="py-16 sm:py-24">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6">
        <div class="mb-12 max-w-3xl scroll-reveal">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Accès</p>
            <h2 class="mt-2 font-display text-3xl font-bold tracking-tight text-yoonu-900 sm:text-4xl">Depuis votre téléphone ou votre navigateur.</h2>
            <p class="mt-3 text-base leading-7 text-slate-600">Que vous préfériez le web ou l'application mobile, vous retrouvez le même parcours, fluide et sécurisé.</p>
        </div>

        <div class="grid gap-8 lg:grid-cols-2 lg:items-stretch">
            <div class="scroll-reveal">
                <h3 class="mb-4 font-display text-xl font-bold text-yoonu-900">Télécharger l'application</h3>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <a class="morph-card inline-flex flex-1 items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 transition hover:bg-slate-50" href="#">
                        <svg class="h-6 w-6 text-slate-900" fill="currentColor" viewBox="0 0 24 24"><path d="M17.05 12.04c-.02-2.2 1.8-3.26 1.88-3.31-1.03-1.5-2.62-1.71-3.19-1.73-1.36-.14-2.65.8-3.34.8-.69 0-1.75-.78-2.88-.76-1.48.02-2.85.86-3.61 2.18-1.54 2.67-.39 6.62 1.1 8.79.73 1.06 1.6 2.25 2.74 2.21 1.1-.04 1.52-.71 2.85-.71 1.33 0 1.71.71 2.88.69 1.19-.02 1.94-1.08 2.67-2.15.84-1.23 1.19-2.42 1.21-2.48-.03-.01-2.32-.89-2.34-3.53zM14.88 5.6c.61-.74 1.02-1.77.91-2.8-.88.04-1.95.59-2.58 1.33-.56.65-1.06 1.69-.93 2.69.98.08 1.99-.5 2.6-1.22z"/></svg>
                        <div class="text-left">
                            <p class="text-xs text-slate-500">Télécharger sur</p>
                            <p class="font-semibold text-slate-900">App Store</p>
                        </div>
                    </a>
                    <a class="morph-card inline-flex flex-1 items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 transition hover:bg-slate-50" href="#">
                        <span class="text-2xl">▶</span>
                        <div class="text-left">
                            <p class="text-xs text-slate-500">Disponible sur</p>
                            <p class="font-semibold text-slate-900">Google Play</p>
                        </div>
                    </a>
                </div>

                <div class="mt-6 morph-card rounded-2xl border border-yoonu-100 bg-yoonu-50/60 p-5 transition-all duration-300">
                    <h4 class="font-semibold text-yoonu-900">Vous préférez le web ?</h4>
                    <p class="mt-2 text-sm text-slate-600">Accédez directement depuis votre navigateur, sans rien télécharger.</p>
                    <a class="mt-3 inline-flex rounded-xl bg-yoonu-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-yoonu-900" href="{{ $platformUrl }}">Ouvrir la version web</a>
                </div>
            </div>

            <div class="scroll-reveal morph-card rounded-2xl border border-yoonu-100 bg-gradient-to-br from-yoonu-50 to-rose-50/60 p-6 transition-all duration-300">
                <div class="mb-6 flex h-48 items-center justify-center overflow-hidden rounded-2xl bg-white shadow-soft">
                    <div class="flex flex-col items-center gap-3">
                        <img src="{{ asset('logo-yoonu-makk.svg') }}" alt="YOONU MAKK" class="h-16 w-16">
                        <span class="font-display text-lg font-extrabold tracking-tight text-yoonu-900">YOONU <span class="text-yoonu-500">MAKK</span></span>
                        <span class="rounded-full bg-yoonu-50 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-yoonu-700">Application mobile</span>
                    </div>
                </div>
                <h3 class="font-display text-lg font-bold text-yoonu-900">Pourquoi utiliser l'app YOONU MAKK ?</h3>
                <ul class="mt-4 space-y-3" data-stagger>
                    @foreach ($avantages as $avantage)
                        <li class="stagger-item flex items-start gap-3">
                            <span class="flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-yoonu-700 text-xs font-bold text-white">✓</span>
                            <p class="text-sm text-slate-700">{{ $avantage }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>
