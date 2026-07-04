@php
    $platformUrl = $frontendUrl ?? '#telechargement';
@endphp

<section class="py-8 sm:py-12">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6">
        <div class="relative overflow-hidden rounded-[2rem] border border-yoonu-100 bg-gradient-to-br from-yoonu-700 to-yoonu-900 p-8 sm:p-12">
            <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/10 blur-2xl"></div>
            <div class="relative">
                <h2 class="max-w-3xl font-display text-3xl font-bold tracking-tight text-white sm:text-4xl">Un point d'entrée unique pour informer, orienter et accompagner.</h2>
                <p class="mt-3 max-w-2xl text-[15px] leading-8 text-white/80">Découvrez la plateforme YOONU JIGEEN côté patiente, accompagnant, gynécologue ou administration, dans une expérience simple et lisible.</p>
                <div class="mt-7 flex flex-wrap gap-3">
                    <a class="rounded-xl bg-white px-6 py-3 text-sm font-bold text-yoonu-800 transition hover:bg-yoonu-50" href="{{ $platformUrl }}">{{ $frontendUrl ? "Accéder à l'application" : 'Voir les accès web' }}</a>
                    <a class="rounded-xl border border-white/30 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10" href="{{ route('assistant') }}">Découvrir l'assistant</a>
                </div>
            </div>
        </div>
    </div>
</section>
