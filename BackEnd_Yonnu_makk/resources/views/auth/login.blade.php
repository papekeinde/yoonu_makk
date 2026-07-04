<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion | {{ config('app.name', 'YOONU JIGEEN') }} — Espace professionnel</title>
    <link rel="icon" href="{{ asset('logo-yoonu-makk.svg') }}" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                fontFamily: {
                    sans:    ['Manrope', 'ui-sans-serif', 'system-ui'],
                    display: ['Archivo', 'ui-sans-serif', 'system-ui'],
                    serif:   ['Fraunces', 'Georgia', 'Times New Roman', 'serif'],
                },
                colors: {
                    yoonu: { 50:'#FCE4EC',100:'#F8BBD0',200:'#F48FB1',300:'#F06292',400:'#EC407A',500:'#E91E63',600:'#C2185B',700:'#AD1457',800:'#8B1A47',900:'#6B0D2B' },
                    ink:   { 950:'#140509',900:'#1A0710',800:'#25101A',700:'#311624',600:'#3A1A28' },
                    urgence:  { 500:'#BC4A3C' },
                    rapide:   { 500:'#C08A33' },
                    standard: { 500:'#5F8568' },
                },
            } },
        };
    </script>
    <style>
        body{font-family:'Manrope',sans-serif}
        .font-display{font-family:'Archivo',sans-serif}
        .font-serif{font-family:'Fraunces',Georgia,serif}
        [x-cloak]{display:none!important}
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@600;700;800;900&family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;1,9..144,300;1,9..144,400&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="font-sans min-h-screen bg-white text-ink-900">
    <div class="flex min-h-screen flex-col">

        {{-- ═══════ BARRE HAUTE — identique au header de la vitrine ═══════ --}}
        <header class="border-b border-black/[0.07]">
            <div class="mx-auto flex h-[72px] w-[min(1200px,92vw)] items-center justify-between gap-6">
                <a href="{{ url('/') }}" class="flex items-center" aria-label="YOONU JIGEEN — accueil">
                    <img src="{{ asset('logo-yoonu-makk.svg') }}" alt="YOONU JIGEEN" class="h-9 w-9">
                </a>
                <a href="{{ url('/') }}" class="font-serif text-[0.95rem] text-ink-800/80 transition-colors hover:text-ink-900">
                    ← Retour au site
                </a>
            </div>
        </header>

        <div class="mx-auto grid w-[min(1200px,92vw)] flex-1 lg:grid-cols-[1.05fr_1fr]">

            {{-- ═══════ PANNEAU ÉDITORIAL — même langage que le hero (blanc, grille de points, halo) ═══════ --}}
            <aside class="relative hidden overflow-hidden py-16 pr-16 lg:flex lg:flex-col">
                {{-- Grille de points, façon vitrine --}}
                <div class="pointer-events-none absolute inset-0 z-0"
                     style="background-image: radial-gradient(rgba(26,7,16,0.55) 1.2px, transparent 1.2px);
                            background-size: 30px 30px;
                            -webkit-mask-image: radial-gradient(ellipse 70% 60% at 30% 40%, #000 26%, transparent 78%);
                            mask-image: radial-gradient(ellipse 70% 60% at 30% 40%, #000 26%, transparent 78%);
                            opacity:.10;"></div>
                {{-- Halo rose discret --}}
                <div class="pointer-events-none absolute -left-24 top-24 z-0 h-[380px] w-[380px] rounded-full bg-yoonu-500/[0.07] blur-3xl"></div>

                <div class="relative z-10 mt-auto max-w-md">
                    <span class="inline-flex items-center gap-2 border border-ink-900/15 px-3.5 py-1.5 font-display text-[11px] font-bold uppercase tracking-[0.18em] text-ink-900">
                        <span class="h-1.5 w-1.5 rounded-full bg-yoonu-500"></span>
                        Espace professionnel
                    </span>

                    <h1 class="mt-7 font-display text-[clamp(2.2rem,3vw,3rem)] font-black uppercase leading-[0.95] tracking-[-0.02em] text-ink-900">
                        Un seul accès à votre
                        <span class="font-serif font-light italic normal-case tracking-[0.01em] text-yoonu-600">espace de travail</span>.
                    </h1>

                    <p class="mt-6 font-serif text-lg leading-8 text-ink-800/80">
                        Administration ou suivi gynécologique : connectez-vous, et
                        <span class="font-semibold text-ink-900">YOONU&nbsp;JIGEEN vous dirige</span>
                        vers le bon tableau de bord.
                    </p>

                    {{-- Signature : les trois niveaux d'orientation, en filets éditoriaux --}}
                    <div class="mt-10">
                        <p class="font-display text-[11px] font-bold uppercase tracking-[0.18em] text-ink-800/50">Les trois niveaux d'orientation</p>
                        <div class="mt-4 border-t border-ink-900/10">
                            @foreach ([['urgence','Urgence','Maintenant'],['rapide','Consultation rapide','Sous 48 h'],['standard','Rendez-vous standard','À planifier']] as [$c,$niveau,$delai])
                                <div class="flex items-center gap-3 border-b border-ink-900/10 py-3.5">
                                    <span class="h-2.5 w-2.5 flex-shrink-0 rounded-full bg-{{ $c }}-500"></span>
                                    <span class="font-serif text-base text-ink-900">{{ $niveau }}</span>
                                    <span class="ml-auto font-display text-[11px] font-medium uppercase tracking-[0.16em] text-ink-800/45">{{ $delai }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <p class="relative z-10 mt-12 font-serif text-sm text-ink-800/50">© {{ date('Y') }} YOONU JIGEEN · Plateforme de santé · Sénégal</p>
            </aside>

            {{-- ═══════ COLONNE FORMULAIRE ═══════ --}}
            <main class="flex flex-col justify-center py-12 lg:border-l lg:border-black/[0.07] lg:py-16 lg:pl-16">
                <div class="mx-auto w-full max-w-sm">
                    <span class="font-display text-[11px] font-bold uppercase tracking-[0.18em] text-yoonu-600">Connexion</span>
                    <h2 class="mt-3 font-display text-3xl font-black uppercase tracking-[-0.02em] text-ink-900">Connexion à votre espace</h2>
                    <p class="mt-3 font-serif text-base text-ink-800/70">Accédez à votre tableau de bord.</p>

                    @if ($errors->any())
                        <div class="mt-6 border border-urgence-500/40 bg-urgence-500/5 px-4 py-3 font-serif text-sm text-urgence-500">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5" x-data>
                        @csrf
                        <div>
                            <label for="email" class="mb-1.5 block font-display text-[11px] font-bold uppercase tracking-[0.14em] text-ink-800/70">Adresse email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                                   class="w-full border border-ink-900/15 px-4 py-3 text-sm text-ink-900 placeholder-ink-900/30 outline-none transition focus:border-ink-900"
                                   placeholder="vous@yoonumakk.sn">
                        </div>
                        <div x-data="{ show: false }">
                            <label for="password" class="mb-1.5 block font-display text-[11px] font-bold uppercase tracking-[0.14em] text-ink-800/70">Mot de passe</label>
                            <div class="relative">
                                <input id="password" name="password" :type="show ? 'text' : 'password'" required
                                       class="w-full border border-ink-900/15 px-4 py-3 pr-16 text-sm text-ink-900 placeholder-ink-900/30 outline-none transition focus:border-ink-900"
                                       placeholder="••••••••">
                                <button type="button" @click="show = !show" tabindex="-1"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 font-display text-[11px] font-bold uppercase tracking-wide text-ink-800/50 transition-colors hover:text-ink-900">
                                    <span x-text="show ? 'Cacher' : 'Voir'"></span>
                                </button>
                            </div>
                        </div>
                        <label class="flex select-none items-center gap-2 font-serif text-sm text-ink-800/70">
                            <input type="checkbox" name="remember" class="rounded-none border-ink-900/30 text-ink-900 focus:ring-0 focus:ring-offset-0">
                            Se souvenir de moi
                        </label>
                        <button type="submit"
                                class="w-full border border-ink-900 bg-ink-900 py-3.5 font-display text-sm font-bold tracking-wide text-white transition-colors hover:bg-ink-700">
                            Se connecter
                        </button>
                    </form>

                    <div class="mt-8 flex items-center justify-between border-t border-black/[0.07] pt-6 font-serif text-sm">
                        <span class="text-ink-800/45">Administration &amp; gynécologues</span>
                        <a href="{{ url('/') }}" class="text-ink-800/60 transition-colors hover:text-ink-900 lg:hidden">← Retour au site</a>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
</body>
</html>
