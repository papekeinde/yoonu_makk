@php
    $navLinks = [
        ['Comment ça marche', '#process'],
        ['Pour qui', '#acteurs'],
        ['Fonctionnalités', '#fonctionnalites'],
        ['Sécurité', '#securite'],
    ];
    $platformUrl = $frontendUrl ?? '#telechargement';
@endphp

<div x-data="{ scrolled: false, menuOpen: false }"
     x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 40; })"
     @keydown.escape.window="menuOpen = false">

    <nav class="fixed inset-x-0 top-0 z-50 transition-all duration-300"
         :class="scrolled ? 'bg-white/95 backdrop-blur-xl border-b border-yoonu-100 shadow-[0_6px_28px_rgba(233,30,99,0.07)]' : 'bg-transparent'">
        <div class="mx-auto flex h-[72px] max-w-7xl items-center justify-between px-4 sm:px-6">
            {{-- Logo --}}
            <a href="/" class="flex items-center gap-2.5">
                <img src="{{ asset('logo-yoonu-makk.svg') }}" alt="YOONU MAKK" class="h-9 w-9">
                <span class="font-display text-lg font-extrabold tracking-tight text-yoonu-900">
                    YOONU <span class="text-yoonu-500">MAKK</span>
                </span>
            </a>

            {{-- Liens desktop --}}
            <div class="hidden items-center gap-1 md:flex">
                @foreach ($navLinks as [$label, $anchor])
                    <a href="{{ $anchor }}"
                       class="rounded-lg px-3.5 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-yoonu-50 hover:text-yoonu-700">{{ $label }}</a>
                @endforeach
            </div>

            {{-- CTA desktop --}}
            <div class="hidden items-center gap-2 md:flex">
                <a href="{{ route('assistant') }}"
                   class="rounded-lg px-3.5 py-2 text-sm font-semibold text-yoonu-700 transition-colors hover:bg-yoonu-50">Assistant</a>
                <a href="{{ $platformUrl }}"
                   class="rounded-xl bg-yoonu-700 px-5 py-2.5 text-sm font-bold text-white shadow-[0_4px_18px_rgba(173,20,87,0.28)] transition-all hover:bg-yoonu-900 hover:scale-[1.02]">
                    Ouvrir la plateforme
                </a>
            </div>

            {{-- Bouton menu mobile --}}
            <button @click="menuOpen = !menuOpen" class="rounded-lg p-2 text-yoonu-900 md:hidden" aria-label="Menu">
                <svg x-show="!menuOpen" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="menuOpen" x-cloak class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
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
        <div class="flex h-[72px] items-center justify-between border-b border-yoonu-100 px-4">
            <span class="font-display text-lg font-extrabold tracking-tight text-yoonu-900">YOONU <span class="text-yoonu-500">MAKK</span></span>
            <button @click="menuOpen = false" class="p-2 text-yoonu-900" aria-label="Fermer">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex flex-1 flex-col justify-center gap-5 px-8">
            @foreach ($navLinks as [$label, $anchor])
                <a href="{{ $anchor }}" @click="menuOpen = false"
                   class="font-display text-2xl font-bold text-yoonu-900">{{ $label }}</a>
            @endforeach
            <a href="{{ route('assistant') }}" @click="menuOpen = false"
               class="font-display text-2xl font-bold text-yoonu-700">Assistant</a>
            <a href="{{ $platformUrl }}" @click="menuOpen = false"
               class="mt-3 rounded-2xl bg-yoonu-700 py-4 text-center text-base font-bold text-white shadow-[0_4px_20px_rgba(173,20,87,0.3)]">
                Ouvrir la plateforme
            </a>
        </div>
    </div>
</div>
