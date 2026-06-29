<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion | {{ config('app.name', 'YOONU MAKK') }} — Espace gynécologue</title>
    <link rel="icon" href="{{ asset('logo-yoonu-makk.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                fontFamily: { sans: ['Manrope','ui-sans-serif'], display: ['Archivo','ui-sans-serif'] },
                colors: {
                    yoonu: { 50:'#FCE4EC',100:'#F8BBD0',200:'#F48FB1',300:'#F06292',400:'#EC407A',500:'#E91E63',600:'#C2185B',700:'#AD1457',800:'#8B1A47',900:'#6B0D2B' },
                    urgence:  { 50:'#FAEEEC', 500:'#BC4A3C', 700:'#8A3328' },
                    rapide:   { 50:'#FAF3E4', 500:'#C08A33', 700:'#855C1C' },
                    standard: { 50:'#EDF2ED', 500:'#5F8568', 700:'#3E5A45' },
                },
            } },
        };
    </script>
    <style>
        body{font-family:'Manrope',sans-serif}
        .font-display{font-family:'Archivo',sans-serif}
        [x-cloak]{display:none!important}
    </style>
</head>
<body class="font-sans min-h-screen bg-white text-gray-900">
    <div class="lg:grid lg:min-h-screen lg:grid-cols-[1fr_1.05fr]">

        {{-- ═══════ COLONNE FORMULAIRE ═══════ --}}
        <main class="flex min-h-screen flex-col justify-center px-5 py-10 sm:px-10 lg:order-1 lg:min-h-0">
            <div class="mx-auto w-full max-w-sm">
                {{-- en-tête de marque (mobile) --}}
                <a href="{{ url('/') }}" class="mb-10 flex items-center gap-2.5 lg:hidden">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-yoonu-500">
                        <img src="{{ asset('logo-yoonu-makk.svg') }}" alt="YOONU MAKK" class="h-6 w-6">
                    </span>
                    <span class="font-display text-lg font-extrabold tracking-tight text-yoonu-900">YOONU <span class="text-yoonu-500">MAKK</span></span>
                </a>

                <span class="text-xs font-bold uppercase tracking-[0.2em] text-yoonu-600">Espace gynécologue</span>
                <h2 class="mt-2 font-display text-2xl font-extrabold tracking-tight text-yoonu-900">Connexion à votre espace</h2>
                <p class="mt-2 text-sm text-gray-500">Vos rendez-vous, vos notifications et vos demandes qualifiées.</p>

                @if ($errors->any())
                    <div class="mt-6 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('pro.login.store') }}" class="mt-7 space-y-5" x-data>
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Adresse email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                               class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-yoonu-500 focus:ring-2 focus:ring-yoonu-500/20 outline-none transition"
                               placeholder="vous@exemple.sn">
                    </div>
                    <div x-data="{ show: false }">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Mot de passe</label>
                        <div class="relative">
                            <input id="password" name="password" :type="show ? 'text' : 'password'" required
                                   class="w-full rounded-xl border border-gray-200 px-4 py-3 pr-12 text-sm focus:border-yoonu-500 focus:ring-2 focus:ring-yoonu-500/20 outline-none transition"
                                   placeholder="••••••••">
                            <button type="button" @click="show = !show" tabindex="-1"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs font-medium">
                                <span x-text="show ? 'Cacher' : 'Voir'"></span>
                            </button>
                        </div>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-600 select-none">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-yoonu-500 focus:ring-yoonu-500/30">
                        Se souvenir de moi
                    </label>
                    <button type="submit"
                            class="w-full rounded-xl bg-yoonu-500 hover:bg-yoonu-600 text-white font-semibold py-3 transition shadow-lg shadow-yoonu-500/25">
                        Se connecter
                    </button>
                </form>

                <div class="mt-8 flex items-center justify-between text-xs">
                    <a href="{{ route('login') }}" class="font-medium text-gray-400 hover:text-yoonu-600 transition">Accès administration</a>
                    <a href="{{ url('/') }}" class="font-medium text-gray-400 hover:text-yoonu-600 transition">← Retour au site</a>
                </div>
            </div>
        </main>

        {{-- ═══════ PANNEAU MARQUE — l'espace de soin (clair et chaud) ═══════ --}}
        <aside class="relative hidden overflow-hidden bg-gradient-to-br from-yoonu-50 via-rose-50 to-white px-12 py-14 lg:order-2 lg:flex lg:flex-col xl:px-16">
            <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full bg-yoonu-100/70 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-28 -left-20 h-80 w-80 rounded-full bg-rose-100/60 blur-3xl"></div>

            <a href="{{ url('/') }}" class="relative ml-auto flex items-center gap-2.5">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-yoonu-500">
                    <img src="{{ asset('logo-yoonu-makk.svg') }}" alt="YOONU MAKK" class="h-6 w-6">
                </span>
                <span class="font-display text-lg font-extrabold tracking-tight text-yoonu-900">YOONU <span class="text-yoonu-500">MAKK</span></span>
            </a>

            <div class="relative mt-auto">
                <span class="text-xs font-bold uppercase tracking-[0.22em] text-yoonu-600">Espace gynécologue</span>
                <h1 class="mt-4 font-display text-[2.1rem] font-extrabold leading-[1.1] tracking-tight text-yoonu-900 xl:text-[2.5rem]">
                    Des demandes mieux préparées, avant la consultation.
                </h1>
                <p class="mt-4 max-w-md text-sm leading-7 text-slate-600">
                    Les patientes arrivent avec une situation déjà qualifiée. Vous priorisez
                    plus vite et concentrez votre temps sur le soin.
                </p>

                {{-- SIGNATURE : l'échelle de triage, ce que vous recevez déjà trié --}}
                <div class="mt-9 max-w-md rounded-2xl border border-yoonu-100 bg-white/80 p-5 shadow-[0_12px_36px_rgba(233,30,99,0.10)] backdrop-blur">
                    <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-slate-400">Demandes reçues, déjà classées</p>
                    <ul class="mt-4 space-y-3">
                        @foreach ([['urgence','Urgence','Maintenant'],['rapide','Consultation rapide','Sous 48 h'],['standard','Rendez-vous standard','À planifier']] as [$c,$niveau,$delai])
                            <li class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 flex-shrink-0 rounded-full bg-{{ $c }}-500"></span>
                                <span class="text-sm font-semibold text-yoonu-900">{{ $niveau }}</span>
                                <span class="ml-auto rounded-full bg-{{ $c }}-50 px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide text-{{ $c }}-700">{{ $delai }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <p class="relative mt-12 text-xs text-slate-400">© {{ date('Y') }} YOONU MAKK · Espace réservé aux gynécologues</p>
        </aside>
    </div>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
</body>
</html>
