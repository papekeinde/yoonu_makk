@extends('layouts.pro')

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
    <div class="space-y-3 max-w-3xl">
        @forelse ($notifications as $n)
            <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-4 flex items-start justify-between gap-4 {{ $n->est_lu ? 'opacity-70' : '' }}">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-yoonu-600">{{ $typeLabels[$n->type?->value] ?? $n->type?->value }}</span>
                        @unless ($n->est_lu)
                            <span class="h-1.5 w-1.5 rounded-full bg-yoonu-500"></span>
                        @endunless
                        <span class="text-xs text-gray-400 tabular-nums">{{ $n->created_at?->format('d/m/Y H:i') }}</span>
                    </div>
                    <p class="mt-1.5 text-sm font-medium text-gray-900 dark:text-white">{{ $n->titre }}</p>
                    <p class="text-sm text-gray-500">{{ $n->corps }}</p>
                </div>
                @unless ($n->est_lu)
                    <form method="POST" action="{{ route('pro.notifications.lu', $n->id) }}">
                        @csrf @method('PATCH')
                        <button class="rounded-lg px-3 py-1.5 text-xs font-medium text-yoonu-600 hover:bg-yoonu-50 dark:hover:bg-white/[0.04] transition whitespace-nowrap">Marquer lu</button>
                    </form>
                @endunless
            </div>
        @empty
            <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-10 text-center text-gray-400">
                Aucune notification.
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $notifications->links() }}</div>
@endsection
