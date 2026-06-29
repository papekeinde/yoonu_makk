@extends('layouts.dashboard')

@section('title', 'Notifications')

@php
    $typeLabels = [
        'general'                 => 'Général',
        'nouveau_contenu'         => 'Nouveau contenu',
        'rappel_rendez_vous'      => 'Rappel rendez-vous',
        'statut_rendez_vous'      => 'Statut rendez-vous',
        'rappel_suivi_grossesse'  => 'Rappel suivi grossesse',
        'alerte_grossesse'        => 'Alerte grossesse',
        'felicitations_grossesse' => 'Félicitations grossesse',
    ];
@endphp

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Formulaire d'envoi --}}
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('admin.notifications.envoyer') }}" x-data="{ aTous: {{ old('a_tous') ? 'true' : 'false' }} }"
                  class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6 space-y-5">
                @csrf
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 font-display">Envoyer une notification</h3>

                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-400 mb-1.5">Type</label>
                    <select name="type" required
                            class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#1A0710] px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">
                        @foreach ($typeLabels as $val => $label)
                            <option value="{{ $val }}" @selected(old('type')===$val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-400 mb-1.5">Titre</label>
                    <input type="text" name="titre" value="{{ old('titre') }}" required
                           class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#1A0710] px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-400 mb-1.5">Message</label>
                    <textarea name="corps" rows="4" required
                              class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#1A0710] px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">{{ old('corps') }}</textarea>
                </div>

                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="a_tous" value="1" x-model="aTous"
                           class="rounded border-gray-300 text-yoonu-500 focus:ring-yoonu-500">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Envoyer à toutes les patientes</span>
                </label>

                <div x-show="!aTous" x-cloak>
                    <label class="block text-xs uppercase tracking-wider text-gray-400 mb-1.5">Destinataires</label>
                    <div class="max-h-64 overflow-y-auto rounded-xl border border-gray-200 dark:border-white/[0.08] divide-y divide-gray-100 dark:divide-white/[0.06]">
                        @forelse ($patientes as $p)
                            <label class="flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                                <input type="checkbox" name="user_ids[]" value="{{ $p->id }}"
                                       @checked(collect(old('user_ids', []))->contains($p->id))
                                       class="rounded border-gray-300 text-yoonu-500 focus:ring-yoonu-500">
                                <span class="text-sm">
                                    <span class="text-gray-900 dark:text-white font-medium">{{ $p->prenom }} {{ $p->nom }}</span>
                                    <span class="text-gray-400"> · {{ $p->email }}</span>
                                </span>
                            </label>
                        @empty
                            <p class="px-4 py-6 text-center text-sm text-gray-400">Aucune patiente enregistrée.</p>
                        @endforelse
                    </div>
                </div>

                <button class="rounded-xl bg-yoonu-500 hover:bg-yoonu-600 text-white px-6 py-2.5 text-sm font-medium transition">Envoyer</button>
            </form>
        </div>

        {{-- Historique --}}
        <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6 h-fit">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 font-display">Dernières notifications</h3>
            <div class="space-y-3">
                @forelse ($historique as $n)
                    <div class="rounded-xl bg-gray-50 dark:bg-white/[0.03] p-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-yoonu-600">{{ $typeLabels[$n->type?->value] ?? $n->type?->value }}</span>
                            <span class="text-xs text-gray-400 tabular-nums">{{ $n->created_at?->format('d/m H:i') }}</span>
                        </div>
                        <p class="mt-1.5 text-sm font-medium text-gray-900 dark:text-white">{{ $n->titre }}</p>
                        <p class="text-xs text-gray-500 line-clamp-2">{{ $n->corps }}</p>
                    </div>
                @empty
                    <p class="text-center text-sm text-gray-400 py-6">Aucune notification envoyée.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
