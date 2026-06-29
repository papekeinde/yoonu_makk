@php
    $mots = ['Orientation', 'Suivi de grossesse', 'Ménopause', 'Écoute', 'Prévention', 'Coordination des soins', 'Éducation', 'Confidentialité', 'Accompagnement'];
@endphp

<section class="marquee-wrap overflow-hidden border-y border-white/10 bg-ink-950 py-4">
    <div class="marquee-track items-center gap-0 text-mauve-200">
        @for ($i = 0; $i < 2; $i++)
            @foreach ($mots as $mot)
                <span class="px-7 font-display text-sm font-bold uppercase tracking-[0.18em]">{{ $mot }}</span>
                <span class="text-yoonu-500" aria-hidden="true">✦</span>
            @endforeach
        @endfor
    </div>
</section>
