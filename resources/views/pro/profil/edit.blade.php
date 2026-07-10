@extends('layouts.pro')

@section('title', 'Mon profil')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Informations professionnelles --}}
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('pro.profil.update') }}"
                  class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6">
                @csrf @method('PUT')
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-5 font-display">Informations professionnelles</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @php
                        $fields = [
                            ['prenom', 'Prénom', 'text'],
                            ['nom', 'Nom', 'text'],
                            ['email', 'Email', 'email'],
                            ['telephone', 'Téléphone', 'text'],
                            ['specialite', 'Spécialité', 'text'],
                            ['annees_experience', 'Années d\'expérience', 'number'],
                            ['structure_sante', 'Structure de santé', 'text'],
                            ['ville', 'Ville', 'text'],
                            ['tarif_consultation', 'Tarif consultation (FCFA)', 'number'],
                        ];
                    @endphp
                    @foreach ($fields as [$name, $label, $type])
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-gray-400 mb-1.5">{{ $label }}</label>
                            <input type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $gynecologue->$name) }}"
                                   class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#1A0710] px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <label class="block text-xs uppercase tracking-wider text-gray-400 mb-1.5">Bio</label>
                    <textarea name="bio" rows="4"
                              class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#1A0710] px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">{{ old('bio', $gynecologue->bio) }}</textarea>
                </div>

                <div class="mt-6">
                    <button class="rounded-xl bg-yoonu-500 hover:bg-yoonu-600 text-white px-6 py-2.5 text-sm font-medium transition">Enregistrer</button>
                </div>
            </form>
        </div>

        {{-- Mot de passe --}}
        <div>
            <form method="POST" action="{{ route('pro.profil.mot-de-passe') }}"
                  class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6 space-y-4">
                @csrf @method('PUT')
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 font-display">Changer le mot de passe</h3>
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-400 mb-1.5">Mot de passe actuel</label>
                    <input type="password" name="mot_de_passe_actuel" required
                           class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#1A0710] px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-400 mb-1.5">Nouveau mot de passe</label>
                    <input type="password" name="nouveau_mot_de_passe" required
                           class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#1A0710] px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-wider text-gray-400 mb-1.5">Confirmer</label>
                    <input type="password" name="nouveau_mot_de_passe_confirmation" required
                           class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#1A0710] px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">
                </div>
                <button class="w-full rounded-xl bg-yoonu-500 hover:bg-yoonu-600 text-white px-4 py-2.5 text-sm font-medium transition">Modifier le mot de passe</button>
            </form>
        </div>
    </div>
@endsection
