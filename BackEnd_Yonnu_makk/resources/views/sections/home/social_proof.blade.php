@php
    $temoignages = [
        ['AM', 'Aïssatou M.', 'Suivi de grossesse', 'Grâce au journal des symptômes et à l\'orientation claire, j\'ai pu préparer ma consultation de manière plus organisée. Le gynécologue a apprécié d\'avoir des informations structurées.'],
        ['RD', 'Dr Rahim D.', 'Gynécologue', 'Les demandes sont mieux qualifiées et les patientes arrivent avec des informations préparées. Cela me fait gagner du temps et la qualité du triage s\'en ressent.'],
        ['SM', 'Sokhna M.', 'Accompagnement ménopause', 'L\'application m\'a aidée à comprendre ce qui se passe et à repérer les moments où j\'avais vraiment besoin d\'une consultation. C\'est rassurant.'],
    ];
    $partenaires = ['Cabinet Dr Sow', 'Clinique Dakar Santé', 'Association Femmes', 'Hôpital Régional', 'Cabinet Kassé', 'Cabinet Diallo', 'Cabinet Sarr', 'Réseau Santé +'];
@endphp

<section class="py-16 sm:py-24">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6">
        <div class="mb-12 max-w-3xl scroll-reveal">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Témoignages</p>
            <h2 class="mt-2 font-display text-3xl font-bold tracking-tight text-yoonu-900 sm:text-4xl">Paroles de patientes et de praticiens.</h2>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            @foreach ($temoignages as [$initiales, $nom, $role, $texte])
                <div class="scroll-reveal morph-card flex flex-col rounded-2xl border border-yoonu-100 bg-white p-6 shadow-soft transition-all duration-300">
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-yoonu-100 font-display text-sm font-bold text-yoonu-700">{{ $initiales }}</span>
                        <div>
                            <p class="font-semibold text-yoonu-900">{{ $nom }}</p>
                            <p class="text-xs text-slate-500">{{ $role }}</p>
                        </div>
                    </div>
                    <p class="mt-4 text-sm leading-7 text-slate-600">« {{ $texte }} »</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="bg-yoonu-50 py-16 sm:py-24">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6">
        <div class="scroll-reveal text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Ils nous font confiance</p>
            <h2 class="mt-2 font-display text-3xl font-bold tracking-tight text-yoonu-900">Des patientes et praticiens partout au Sénégal.</h2>
        </div>

        <div class="mt-10 grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
            @foreach ($partenaires as $partenaire)
                <div class="scroll-reveal stagger-item morph-card rounded-xl border border-yoonu-100 bg-white px-6 py-8 transition-all duration-300">
                    <p class="text-center text-sm font-semibold text-slate-600">{{ $partenaire }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
