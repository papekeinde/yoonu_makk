@extends('layouts.pro')

@section('title', 'Dossier patiente')

@php
    $u = $femme->user;
    $age = $u?->date_naissance ? \Carbon\Carbon::parse($u->date_naissance)->age : null;
    $statutColors = ['en_attente'=>'amber','accepte'=>'green','refuse'=>'red','termine'=>'brand','annule'=>'gray'];
    $statutLabels = ['en_attente'=>'En attente','accepte'=>'Accepté','refuse'=>'Refusé','termine'=>'Terminé','annule'=>'Annulé'];
@endphp

@section('content')
    <div class="mb-5">
        <a href="{{ route('pro.patientes.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-yoonu-600">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Retour à mes patientes
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Colonne principale --}}
        <div class="lg:col-span-2 space-y-5">
            {{-- Identité --}}
            <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6">
                <div class="flex items-center gap-4">
                    <span class="flex h-14 w-14 items-center justify-center rounded-full bg-yoonu-500 text-white text-lg font-bold font-display">
                        {{ strtoupper(mb_substr($u?->prenom ?? 'P', 0, 1)) }}{{ strtoupper(mb_substr($u?->nom ?? '', 0, 1)) }}
                    </span>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white font-display">{{ $u?->prenom }} {{ $u?->nom }}</h2>
                        <p class="text-sm text-gray-500">{{ $u?->email }}</p>
                    </div>
                </div>

                <dl class="mt-5 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    @php
                        $infos = [
                            'Téléphone' => $u?->telephone ?? '—',
                            'Âge'       => $age ? $age.' ans' : '—',
                            'Ville'     => $u?->ville ?? '—',
                            'Profil'    => $femme->grossesseActive ? 'Grossesse' : ($femme->stade_menopause ? 'Ménopause' : '—'),
                        ];
                    @endphp
                    @foreach ($infos as $label => $val)
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-400">{{ $label }}</dt>
                            <dd class="mt-0.5 text-gray-900 dark:text-white">{{ $val }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            {{-- Grossesse active --}}
            @if ($grossesse)
                <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4 font-display">Grossesse en cours</h3>
                    <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-400">Semaines</dt>
                            <dd class="mt-0.5 text-gray-900 dark:text-white font-semibold">{{ $grossesse->semainesAmenorrhee() }} SA</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-400">Terme prévu</dt>
                            <dd class="mt-0.5 text-gray-900 dark:text-white">{{ $grossesse->date_accouchement_prevue?->format('d/m/Y') ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-400">Groupe sanguin</dt>
                            <dd class="mt-0.5 text-gray-900 dark:text-white">{{ $grossesse->groupe_sanguin ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-400">J-accouchement</dt>
                            <dd class="mt-0.5 text-gray-900 dark:text-white">{{ max(0, $grossesse->joursAvantAccouchement()) }} j</dd>
                        </div>
                    </dl>
                </div>
            @endif

            {{-- Journal des symptômes --}}
            <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 font-display">Journal des symptômes <span class="text-gray-400 font-normal">(10 derniers)</span></h3>
                <div class="space-y-3">
                    @forelse ($symptomes as $s)
                        <div class="rounded-xl bg-gray-50 dark:bg-white/[0.03] p-3">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $s->date_journal?->format('d/m/Y') }}</p>
                                <span class="text-xs text-gray-400">{{ $s->entrees->count() }} entrée(s)</span>
                            </div>
                            @if ($s->note_generale)
                                <p class="mt-1 text-xs text-gray-500">{{ $s->note_generale }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-center text-sm text-gray-400 py-6">Aucun symptôme enregistré.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Colonne latérale --}}
        <div class="space-y-5">
            {{-- Historique des rendez-vous --}}
            <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 font-display">Rendez-vous</h3>
                <div class="space-y-3">
                    @forelse ($rendezVous as $rdv)
                        @php $st = $rdv->statut?->value; @endphp
                        <a href="{{ route('pro.rendez-vous.show', $rdv->id) }}"
                           class="block rounded-xl bg-gray-50 dark:bg-white/[0.03] p-3 transition hover:bg-gray-100 dark:hover:bg-white/[0.05]">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $rdv->date_souhaitee?->format('d/m/Y') }}
                                    @if ($rdv->heure_souhaitee)<span class="text-gray-400 font-normal">· {{ substr($rdv->heure_souhaitee, 0, 5) }}</span>@endif
                                </p>
                                <x-status-badge :color="$statutColors[$st] ?? 'gray'" :label="$statutLabels[$st] ?? $st" />
                            </div>
                            @if ($rdv->motif)
                                <p class="mt-1 text-xs text-gray-500 truncate">{{ $rdv->motif }}</p>
                            @endif
                        </a>
                    @empty
                        <p class="text-center text-sm text-gray-400 py-4">Aucun rendez-vous.</p>
                    @endforelse
                </div>
            </div>

            {{-- Recommandations envoyées --}}
            <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 font-display">Mes recommandations</h3>
                <div class="space-y-3">
                    @forelse ($recommandations as $reco)
                        <div class="rounded-xl bg-gray-50 dark:bg-white/[0.03] p-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $reco->titre }}</p>
                            <p class="mt-1 text-xs text-gray-500 line-clamp-2">{{ $reco->corps }}</p>
                            <p class="mt-1.5 text-[11px] text-gray-400">{{ $reco->created_at?->format('d/m/Y') }}</p>
                        </div>
                    @empty
                        <p class="text-center text-sm text-gray-400 py-4">Aucune recommandation envoyée.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
