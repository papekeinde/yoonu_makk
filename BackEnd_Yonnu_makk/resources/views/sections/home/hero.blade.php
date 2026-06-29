@php
    $platformUrl = $frontendUrl ?? '#telechargement';
@endphp

<section id="hero" class="relative flex min-h-screen flex-col overflow-hidden bg-white">
    {{-- Grille de points, façon vitrine — discrète, centrée --}}
    <div class="pointer-events-none absolute inset-0 z-0"
         style="background-image: radial-gradient(rgba(26,7,16,0.55) 1.2px, transparent 1.2px);
                background-size: 30px 30px;
                -webkit-mask-image: radial-gradient(ellipse 62% 60% at 50% 46%, #000 28%, transparent 76%);
                mask-image: radial-gradient(ellipse 62% 60% at 50% 46%, #000 28%, transparent 76%);
                opacity: .10;"></div>
    {{-- Halo rose discret, signature de marque sur le blanc --}}
    <div class="pointer-events-none absolute -right-24 top-10 z-0 h-[420px] w-[420px] rounded-full bg-yoonu-500/[0.07] blur-3xl"></div>

    <div class="relative z-10 mx-auto flex min-h-screen w-[min(1200px,92vw)] flex-col">

        {{-- Haut : accroche + phrase de valeur (le produit doit se comprendre vite) --}}
        <div class="max-w-xl pt-32 sm:pt-36 animate-fade-in animate-delay-100">
            <span class="inline-flex items-center gap-2 border border-ink-900/15 px-3.5 py-1.5 font-display text-[11px] font-bold uppercase tracking-[0.18em] text-ink-900">
                <span class="h-1.5 w-1.5 rounded-full bg-yoonu-500"></span>
                Santé des femmes · Sénégal
            </span>
            <p class="mt-6 max-w-[556px] font-serif text-lg leading-8 text-ink-800/80">
                YOONU MAKK qualifie une situation en langage simple et l'oriente vers
                <span class="font-semibold text-ink-900">le bon niveau de prise en charge</span>.
                Sans jamais se substituer au diagnostic médical.
            </p>
        </div>

        {{-- Bas : actions à gauche, grande typographie à droite --}}
        <div class="mt-auto flex flex-col items-start gap-12 pb-16 sm:pb-20 lg:flex-row lg:items-end lg:justify-between">

            {{-- Actions, empilées en bas à gauche --}}
            <div class="w-full max-w-[16rem] animate-fade-in animate-delay-300">
                <div class="flex flex-col gap-3">
                    <a href="{{ $platformUrl }}"
                       class="border border-ink-900 bg-ink-900 px-8 py-3.5 text-center font-display text-sm font-bold tracking-wide text-white transition-colors hover:bg-ink-700">
                        Ouvrir la plateforme
                    </a>
                    <a href="#process"
                       class="border border-ink-900/30 px-8 py-3.5 text-center font-display text-sm font-semibold tracking-wide text-ink-900 transition-colors hover:border-ink-900 hover:bg-ink-900 hover:text-white">
                        Comment ça marche
                    </a>
                </div>
            </div>

            {{-- Signature : grande typographie alignée à droite --}}
            <h1 class="flex flex-col items-start gap-1 text-left leading-[0.95] lg:items-end lg:text-right animate-fade-in animate-delay-200">
                <span class="whitespace-nowrap font-display font-black uppercase tracking-[-0.02em] text-[clamp(1.95rem,7.06vw,5.34rem)]">
                    <span class="text-ink-900">Yoonu</span> <span class="font-serif font-light italic normal-case tracking-[0.01em] text-yoonu-600 text-[clamp(3.4rem,12.3vw,9.3rem)]">Jigeen</span>
                </span>
                <span class="whitespace-nowrap font-serif font-light tracking-[0.01em] text-ink-900 text-[clamp(1.08rem,3.91vw,2.93rem)]">
                    savoir <span class="italic text-yoonu-600">quoi faire,</span> au bon moment
                </span>
            </h1>
        </div>
    </div>
</section>
