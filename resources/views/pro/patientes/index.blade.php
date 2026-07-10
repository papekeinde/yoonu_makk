@extends('layouts.pro')

@section('title', 'Mes patientes')

@section('content')
    {{-- Barre de recherche / filtre --}}
    <form method="GET" class="mb-5 flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-[220px]">
            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
            </svg>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher une patiente (nom, e-mail)…"
                   class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#25101A] dark:text-white text-sm outline-none focus:border-yoonu-500 focus:ring-1 focus:ring-yoonu-500">
        </div>
        <select name="profil"
                class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#25101A] dark:text-white text-sm outline-none focus:border-yoonu-500 focus:ring-1 focus:ring-yoonu-500 appearance-none cursor-pointer">
            <option value="">Tous les profils</option>
            <option value="grossesse" @selected(request('profil')==='grossesse')>Grossesse</option>
            <option value="menopause" @selected(request('profil')==='menopause')>Ménopause</option>
        </select>
        <button class="rounded-xl bg-yoonu-500 hover:bg-yoonu-600 text-white px-5 py-2.5 text-sm font-medium transition">Filtrer</button>
    </form>

    {{-- Compteur --}}
    <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
        <span class="font-semibold text-gray-900 dark:text-white">{{ $patientes->count() }}</span>
        patiente(s) suivie(s)
    </p>

    {{-- Grille de cartes patientes --}}
    @if ($patientes->isEmpty())
        <div class="rounded-2xl border border-dashed border-gray-300 dark:border-white/[0.08] bg-white dark:bg-[#25101A] py-16 text-center">
            <svg class="mx-auto w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4z"/>
            </svg>
            <p class="mt-3 text-sm text-gray-400">Aucune patiente pour le moment.</p>
            <p class="text-xs text-gray-400">Vos patientes apparaîtront ici dès leur premier rendez-vous.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($patientes as $femme)
                @php
                    $u = $femme->user;
                    $enceinte = (bool) $femme->grossesseActive;
                    $age = $u?->date_naissance ? \Carbon\Carbon::parse($u->date_naissance)->age : null;
                @endphp
                <a href="{{ route('pro.patientes.show', $femme->id) }}"
                   class="group rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-5 transition hover:border-yoonu-300 dark:hover:border-yoonu-500/40 hover:shadow-soft">
                    <div class="flex items-start gap-3">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-yoonu-500 text-white font-bold font-display">
                            {{ strtoupper(mb_substr($u?->prenom ?? 'P', 0, 1)) }}{{ strtoupper(mb_substr($u?->nom ?? '', 0, 1)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <h3 class="truncate font-semibold text-gray-900 dark:text-white font-display group-hover:text-yoonu-600">{{ $u?->prenom }} {{ $u?->nom }}</h3>
                            <p class="truncate text-xs text-gray-500">{{ $u?->email }}</p>
                            <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                @if ($enceinte)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-yoonu-50 dark:bg-yoonu-500/15 px-2 py-0.5 text-[11px] font-medium text-yoonu-700 dark:text-yoonu-300">
                                        Enceinte · {{ $femme->grossesseActive->semainesAmenorrhee() }} SA
                                    </span>
                                @elseif ($femme->stade_menopause)
                                    <span class="inline-flex items-center rounded-full bg-rapide-50 dark:bg-rapide-500/15 px-2 py-0.5 text-[11px] font-medium text-rapide-700 dark:text-rapide-500">
                                        Ménopause
                                    </span>
                                @endif
                                @if ($age)
                                    <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-white/[0.06] px-2 py-0.5 text-[11px] font-medium text-gray-500 dark:text-gray-400">{{ $age }} ans</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between border-t border-gray-100 dark:border-white/[0.06] pt-3 text-xs text-gray-500 dark:text-gray-400">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $femme->rdv_count }} rendez-vous
                        </span>
                        @if ($u?->ville)
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $u->ville }}
                            </span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection
