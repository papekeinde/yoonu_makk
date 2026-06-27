@php
    $platformUrl = $frontendUrl ?? '#telechargement';
    $colonnes = [
        'Plateforme' => [
            ['Comment ça marche', '#process'],
            ['Pour qui', '#acteurs'],
            ['Fonctionnalités', '#fonctionnalites'],
            ['Assistant', route('assistant')],
        ],
        'Parcours' => [
            ['Grossesse', '#process'],
            ['Ménopause', '#process'],
            ['Découverte', '#process'],
            ['Sécurité & alerte', '#securite'],
        ],
        'Légal' => [
            ['Mentions légales', '#'],
            ['Confidentialité', '#'],
            ['Conditions d\'utilisation', '#'],
            ['Contact', '#'],
        ],
    ];
@endphp

<footer class="border-t border-yoonu-100 bg-white py-12 sm:py-16">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6">
        <div class="grid gap-10 md:grid-cols-[1.4fr_1fr_1fr_1fr]">
            {{-- Marque + newsletter --}}
            <div>
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('logo-yoonu-makk.svg') }}" alt="YOONU MAKK" class="h-9 w-9">
                    <span class="font-display text-lg font-extrabold tracking-tight text-yoonu-900">YOONU <span class="text-yoonu-500">MAKK</span></span>
                </div>
                <p class="mt-3 max-w-xs text-sm text-slate-600">Orientation, coordination et suivi en santé des femmes — sans se substituer au diagnostic médical.</p>

                <form class="mt-5 flex max-w-sm overflow-hidden rounded-xl border border-yoonu-200 bg-white" onsubmit="return false;">
                    <input type="email" placeholder="Votre e-mail" class="w-full border-0 bg-transparent px-4 py-2.5 text-sm text-slate-700 outline-none placeholder:text-slate-400">
                    <button type="submit" class="bg-yoonu-700 px-4 text-sm font-semibold text-white transition hover:bg-yoonu-900">S'abonner</button>
                </form>
            </div>

            {{-- Colonnes de liens --}}
            @foreach ($colonnes as $titre => $liens)
                <div>
                    <h3 class="font-semibold text-yoonu-900">{{ $titre }}</h3>
                    <ul class="mt-3 space-y-2">
                        @foreach ($liens as [$label, $href])
                            <li><a class="text-sm text-slate-600 transition hover:text-yoonu-700" href="{{ $href }}">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        <div class="mt-10 border-t border-yoonu-100 pt-8">
            <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                <p class="text-xs text-slate-500">© {{ now()->year }} YOONU MAKK · Plateforme de santé basée au Sénégal · Tous droits réservés</p>
                <div class="flex gap-3">
                    <a class="flex h-9 w-9 items-center justify-center rounded-full bg-yoonu-50 text-yoonu-700 transition hover:bg-yoonu-100" href="#" aria-label="Twitter">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-7.007 3.748 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84"/></svg>
                    </a>
                    <a class="flex h-9 w-9 items-center justify-center rounded-full bg-yoonu-50 text-yoonu-700 transition hover:bg-yoonu-100" href="#" aria-label="Facebook">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M20 10c0-5.523-4.477-10-10-10S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987H5.898V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10"/></svg>
                    </a>
                    <a class="flex h-9 w-9 items-center justify-center rounded-full bg-yoonu-50 text-yoonu-700 transition hover:bg-yoonu-100" href="#" aria-label="Instagram">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 0C7.284 0 6.944.012 5.877.06 4.813.11 4.086.278 3.45.525a4.9 4.9 0 00-1.772 1.153A4.9 4.9 0 00.525 3.45C.278 4.086.11 4.813.06 5.877.012 6.944 0 7.284 0 10s.012 3.056.06 4.123c.05 1.064.218 1.791.465 2.427a4.9 4.9 0 001.153 1.772 4.9 4.9 0 001.772 1.153c.636.247 1.363.415 2.427.465C6.944 19.988 7.284 20 10 20s3.056-.012 4.123-.06c1.064-.05 1.791-.218 2.427-.465a4.9 4.9 0 001.772-1.153 4.9 4.9 0 001.153-1.772c.247-.636.415-1.363.465-2.427.048-1.067.06-1.407.06-4.123s-.012-3.056-.06-4.123c-.05-1.064-.218-1.791-.465-2.427a4.9 4.9 0 00-1.153-1.772A4.9 4.9 0 0016.55.525C15.914.278 15.187.11 14.123.06 13.056.012 12.716 0 10 0zm0 1.8c2.67 0 2.986.01 4.04.058.976.045 1.505.207 1.858.344.467.182.8.4 1.15.748.35.35.566.683.748 1.15.137.353.3.882.344 1.857.048 1.055.058 1.37.058 4.041 0 2.67-.01 2.986-.058 4.04-.045.976-.207 1.505-.344 1.858a3.1 3.1 0 01-.748 1.15 3.1 3.1 0 01-1.15.748c-.353.137-.882.3-1.857.344-1.054.048-1.37.058-4.041.058-2.67 0-2.987-.01-4.04-.058-.976-.045-1.505-.207-1.858-.344a3.1 3.1 0 01-1.15-.748 3.1 3.1 0 01-.748-1.15c-.137-.353-.3-.882-.344-1.857C1.81 12.987 1.8 12.67 1.8 10c0-2.67.01-2.986.058-4.04.045-.976.207-1.505.344-1.858.182-.467.4-.8.748-1.15.35-.35.683-.566 1.15-.748.353-.137.882-.3 1.857-.344C7.014 1.81 7.33 1.8 10 1.8zm0 3.06A5.139 5.139 0 0010 15.14 5.139 5.139 0 0010 4.86zm0 8.473A3.333 3.333 0 1110 6.667a3.333 3.333 0 010 6.666zm6.538-8.671a1.2 1.2 0 11-2.4 0 1.2 1.2 0 012.4 0z" clip-rule="evenodd"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>
