<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tableau de bord') | {{ config('app.name', 'YOONU MAKK') }}</title>
    <link rel="icon" href="{{ asset('logo-yoonu-makk.svg') }}" type="image/svg+xml">

    <!-- Polices -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind + Alpine (CDN, comme le site vitrine) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Manrope', 'ui-sans-serif', 'system-ui'],
                        display: ['Archivo', 'ui-sans-serif', 'system-ui'],
                    },
                    colors: {
                        yoonu: {
                            50: '#FCE4EC', 100: '#F8BBD0', 200: '#F48FB1', 300: '#F06292',
                            400: '#EC407A', 500: '#E91E63', 600: '#C2185B', 700: '#AD1457',
                            800: '#8B1A47', 900: '#6B0D2B',
                        },
                        // Échelle de triage — alignée sur la vitrine.
                        urgence:  { 50:'#FAEEEC', 100:'#F1D5D0', 500:'#BC4A3C', 700:'#8A3328' },
                        rapide:   { 50:'#FAF3E4', 100:'#F0E1BC', 500:'#C08A33', 700:'#855C1C' },
                        standard: { 50:'#EDF2ED', 100:'#D6E2D6', 500:'#5F8568', 700:'#3E5A45' },
                    },
                    boxShadow: { soft: '0 8px 24px rgba(15, 23, 42, 0.06)' },
                },
            },
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    @stack('styles')

    <style>
        :root {
            --brand: #E91E63;
            --brand-hover: #C2185B;
            --dark: #1A0710;
            --dark-card: #25101A;
            --dark-border: #3A1A28;
        }
        body { font-family: 'Manrope', sans-serif; }
        .font-display { font-family: 'Archivo', sans-serif; }
        [x-cloak] { display: none !important; }

        /* Sidebar */
        #sidebar { background-color: var(--dark); }
        .menu-item {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.625rem 0.875rem; border-radius: 0.75rem;
            font-size: 0.875rem; font-weight: 500; transition: all 0.2s;
        }
        .menu-item-active { background-color: var(--brand); color: #fff !important; font-weight: 600; }
        .menu-item-inactive { color: #C9B8C0; }
        .menu-item-inactive:hover { background-color: rgba(255,255,255,0.06); color: #fff; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d9aebf; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--brand); }

        html { scroll-behavior: smooth; }
    </style>

    <script>
        (function () {
            if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark');
        })();
    </script>
</head>
<body class="bg-gray-50 dark:bg-[#1A0710] text-gray-900 dark:text-gray-100"
      x-data
      x-init="
        Alpine.store('sidebar', {
            isExpanded: window.innerWidth >= 1280,
            isHovered: false,
            isMobileOpen: false,
            get isOpen() { return this.isExpanded || this.isHovered || this.isMobileOpen; },
            toggleExpanded() { this.isExpanded = !this.isExpanded; },
            toggleMobileOpen() { this.isMobileOpen = !this.isMobileOpen; },
            setHovered(v) { if (!this.isExpanded && window.innerWidth >= 1280) this.isHovered = v; }
        });
        Alpine.store('theme', {
            mode: localStorage.getItem('theme') || 'light',
            toggle() {
                this.mode = this.mode === 'dark' ? 'light' : 'dark';
                localStorage.setItem('theme', this.mode);
                document.documentElement.classList.toggle('dark', this.mode === 'dark');
            }
        });
      ">

    <div class="flex min-h-screen">

        {{-- ═══════ SIDEBAR ═══════ --}}
        <aside id="sidebar"
            class="fixed left-0 top-0 flex flex-col h-screen px-5 transition-all duration-300 ease-in-out z-[99999] border-r border-white/[0.06]"
            :class="{
                'w-[280px]': $store.sidebar.isOpen,
                'w-[88px]': !$store.sidebar.isOpen,
                'translate-x-0': $store.sidebar.isMobileOpen,
                '-translate-x-full xl:translate-x-0': !$store.sidebar.isMobileOpen
            }"
            @mouseenter="$store.sidebar.setHovered(true)"
            @mouseleave="$store.sidebar.setHovered(false)">

            {{-- Logo --}}
            <div class="pt-7 pb-6 flex" :class="!$store.sidebar.isOpen ? 'xl:justify-center' : 'justify-start'">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-yoonu-500 shrink-0">
                        <img src="{{ asset('logo-yoonu-makk.svg') }}" alt="YOONU MAKK" class="h-6 w-6">
                    </span>
                    <span x-show="$store.sidebar.isOpen" class="text-lg font-bold text-white font-display tracking-tight">YOONU&nbsp;MAKK</span>
                </a>
            </div>

            {{-- Nav --}}
            <nav class="flex flex-col overflow-y-auto flex-1 pb-4">
                <div class="flex flex-col gap-5">

                    {{-- Pilotage --}}
                    <div>
                        <h2 class="mb-3 text-[0.65rem] uppercase tracking-[0.2em] flex text-gray-400 dark:text-gray-500"
                            :class="!$store.sidebar.isOpen ? 'xl:justify-center' : 'justify-start'">
                            <span x-show="$store.sidebar.isOpen">Pilotage</span>
                            <span x-show="!$store.sidebar.isOpen">•••</span>
                        </h2>
                        <ul class="flex flex-col gap-1">
                            @php
                                $nav = [
                                    ['admin.dashboard',  'Tableau de bord', 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                                    ['admin.statistiques.*', 'Statistiques', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                                    ['admin.utilisateurs.*', 'Utilisateurs', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                                    ['admin.gynecologues.*', 'Gynécologues', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                                    ['admin.demandes.*', 'Demandes d\'adhésion', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                                    ['admin.rendez-vous.*', 'Rendez-vous', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                                ];
                            @endphp
                            @foreach ($nav as [$route, $label, $icon])
                                <li>
                                    <a href="{{ route(\Illuminate\Support\Str::replaceLast('.*', '.index', $route)) }}"
                                       class="menu-item {{ request()->routeIs($route) ? 'menu-item-active' : 'menu-item-inactive' }}"
                                       :class="!$store.sidebar.isOpen ? 'xl:justify-center' : ''">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                                        </svg>
                                        <span x-show="$store.sidebar.isOpen">{{ $label }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Contenus --}}
                    <div>
                        <h2 class="mb-3 text-[0.65rem] uppercase tracking-[0.2em] flex text-gray-400 dark:text-gray-500"
                            :class="!$store.sidebar.isOpen ? 'xl:justify-center' : 'justify-start'">
                            <span x-show="$store.sidebar.isOpen">Contenus</span>
                            <span x-show="!$store.sidebar.isOpen">•••</span>
                        </h2>
                        <ul class="flex flex-col gap-1">
                            @php
                                $navContenus = [
                                    ['admin.contenus.*',   'Articles', 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'],
                                    ['admin.categories.*', 'Catégories', 'M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z'],
                                    ['admin.videos.*',     'Vidéos', 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
                                    ['admin.notifications.*', 'Notifications', 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
                                ];
                            @endphp
                            @foreach ($navContenus as [$route, $label, $icon])
                                <li>
                                    <a href="{{ route(\Illuminate\Support\Str::replaceLast('.*', '.index', $route)) }}"
                                       class="menu-item {{ request()->routeIs($route) ? 'menu-item-active' : 'menu-item-inactive' }}"
                                       :class="!$store.sidebar.isOpen ? 'xl:justify-center' : ''">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                                        </svg>
                                        <span x-show="$store.sidebar.isOpen">{{ $label }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </nav>
        </aside>

        {{-- Backdrop mobile --}}
        <div x-show="$store.sidebar.isMobileOpen" @click="$store.sidebar.isMobileOpen = false"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[99998] xl:hidden" x-transition.opacity x-cloak></div>

        {{-- ═══════ CONTENU ═══════ --}}
        <div class="relative flex flex-1 flex-col overflow-x-hidden transition-all duration-300"
             :class="{ 'xl:ml-[280px]': $store.sidebar.isOpen, 'xl:ml-[88px]': !$store.sidebar.isOpen }">

            {{-- Header --}}
            <header class="sticky top-0 w-full z-[9999] border-b border-gray-200 dark:border-white/[0.06] bg-white/80 dark:bg-[#1A0710]/80 backdrop-blur-xl">
                <div class="flex items-center justify-between px-4 py-3 xl:px-6 lg:py-4">
                    <div class="flex items-center gap-3">
                        {{-- Toggle desktop --}}
                        <button @click="$store.sidebar.toggleExpanded()"
                                class="hidden xl:flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200 dark:border-white/[0.08] text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/[0.04] transition">
                            <svg class="w-4 h-4" viewBox="0 0 16 12" fill="currentColor"><path fill-rule="evenodd" clip-rule="evenodd" d="M0.583 1C0.583 0.586 0.919 0.25 1.333 0.25H14.667C15.081 0.25 15.417 0.586 15.417 1C15.417 1.414 15.081 1.75 14.667 1.75H1.333C0.919 1.75 0.583 1.414 0.583 1ZM0.583 11C0.583 10.586 0.919 10.25 1.333 10.25H14.667C15.081 10.25 15.417 10.586 15.417 11C15.417 11.414 15.081 11.75 14.667 11.75H1.333C0.919 11.75 0.583 11.414 0.583 11ZM1.333 5.25C0.919 5.25 0.583 5.586 0.583 6C0.583 6.414 0.919 6.75 1.333 6.75H8C8.414 6.75 8.75 6.414 8.75 6C8.75 5.586 8.414 5.25 8 5.25H1.333Z"/></svg>
                        </button>
                        {{-- Toggle mobile --}}
                        <button @click="$store.sidebar.toggleMobileOpen()"
                                class="flex xl:hidden items-center justify-center w-10 h-10 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/[0.04]">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <h1 class="text-base xl:text-lg font-semibold dark:text-white font-display">@yield('title', 'Tableau de bord')</h1>
                    </div>

                    <div class="flex items-center gap-3">
                        {{-- Thème --}}
                        <button @click="$store.theme.toggle()"
                                class="flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200 dark:border-white/[0.08] text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/[0.04] transition">
                            <svg class="hidden dark:block w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <svg class="dark:hidden w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        </button>

                        {{-- Utilisateur --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center gap-2.5 text-sm">
                                <span class="hidden lg:block text-right leading-tight">
                                    <span class="block font-medium dark:text-white">{{ auth()->user()->prenom }} {{ auth()->user()->nom }}</span>
                                    <span class="block text-xs text-gray-500">{{ auth()->user()->email }}</span>
                                </span>
                                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-yoonu-500 text-white font-bold text-sm">
                                    {{ strtoupper(mb_substr(auth()->user()->prenom ?? auth()->user()->nom ?? 'A', 0, 1)) }}{{ strtoupper(mb_substr(auth()->user()->nom ?? '', 0, 1)) }}
                                </span>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition x-cloak
                                 class="absolute right-0 mt-3 w-56 rounded-2xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#25101A] shadow-xl z-[99999]">
                                <div class="p-3 border-b border-gray-100 dark:border-white/[0.06]">
                                    <p class="px-2 text-sm font-semibold dark:text-white">{{ auth()->user()->prenom }} {{ auth()->user()->nom }}</p>
                                    <p class="px-2 text-xs text-yoonu-600">Administrateur</p>
                                </div>
                                <div class="p-3">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            Déconnexion
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Main --}}
            <main class="mx-auto w-full max-w-screen-2xl p-4 md:p-6 2xl:p-8">
                @if (session('success'))
                    <div class="mb-4 rounded-xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 px-4 py-3 text-sm text-green-700 dark:text-green-400">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 px-4 py-3 text-sm text-red-700 dark:text-red-400">
                        {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 px-4 py-3 text-sm text-red-700 dark:text-red-400">
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
