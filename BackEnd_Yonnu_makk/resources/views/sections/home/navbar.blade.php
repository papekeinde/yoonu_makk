@php
    $navLinks = [
        ['Comment ça marche', '#process'],
        ['Pour qui', '#acteurs'],
        ['Fonctionnalités', '#fonctionnalites'],
        ['Assistant', route('assistant')],
    ];
@endphp

<div x-data="{ scrolled: false, menuOpen: false }"
     x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 24; })"
     @keydown.escape.window="menuOpen = false">

    {{-- Barre fixe blanche, façon vitrine éditoriale --}}
    <nav class="fixed inset-x-0 top-0 z-50 border-b border-black/[0.07] bg-white/95 backdrop-blur transition-shadow duration-300"
         :class="scrolled ? 'shadow-[0_2px_24px_rgba(26,7,16,0.08)]' : ''">
        <div class="mx-auto flex h-[72px] w-[min(1200px,92vw)] items-center justify-between gap-6">
            {{-- Logo --}}
            <a href="/" class="flex items-center" aria-label="YOONU JIGEEN — accueil">
                <img src="{{ asset('logo-yoonu-makk.svg') }}" alt="YOONU JIGEEN" class="h-9 w-9">
            </a>

            {{-- Liens — centrés --}}
            <div class="hidden md:flex md:flex-1 md:items-center md:justify-center md:gap-1">
                @foreach ($navLinks as [$label, $anchor])
                    <a href="{{ $anchor }}"
                       class="px-4 py-2 font-serif text-[0.95rem] text-ink-800/80 transition-colors hover:text-ink-900">{{ $label }}</a>
                @endforeach
            </div>

            {{-- Connexion : un seul bouton, façonné « plein » --}}
            <div class="hidden md:block">
                <a href="{{ route('login') }}"
                   class="inline-flex items-center border border-ink-900 bg-ink-900 px-6 py-2.5 font-display text-sm font-bold tracking-wide text-white transition-colors hover:bg-ink-700">
                    Se connecter
                </a>
            </div>

            {{-- Bouton menu mobile --}}
            <button @click="menuOpen = !menuOpen" class="border border-ink-900/15 p-2 text-ink-900 md:hidden" aria-label="Menu">
                <svg x-show="!menuOpen" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="menuOpen" x-cloak class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </nav>

    {{-- Menu mobile plein écran --}}
    <div x-show="menuOpen" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 flex flex-col bg-white md:hidden">
        <div class="flex h-[72px] items-center justify-between border-b border-black/[0.07] px-[4vw]">
            <span class="font-display text-lg font-extrabold tracking-tight text-ink-900">YOONU <span class="text-yoonu-600">JIGEEN</span></span>
            <button @click="menuOpen = false" class="border border-ink-900/15 p-2 text-ink-900" aria-label="Fermer">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex flex-1 flex-col justify-center gap-2 px-[6vw]">
            @foreach ($navLinks as [$label, $anchor])
                <a href="{{ $anchor }}" @click="menuOpen = false"
                   class="border-b border-black/5 py-4 font-serif text-2xl text-ink-900">{{ $label }}</a>
            @endforeach
            <a href="{{ route('login') }}" @click="menuOpen = false"
               class="mt-5 border border-ink-900 bg-ink-900 py-4 text-center font-display text-base font-bold tracking-wide text-white">
                Se connecter
            </a>
        </div>
    </div>
</div>
