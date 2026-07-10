@extends('layouts.dashboard')

@section('title', 'Catégories')

@section('content')
    <div class="mb-5 flex items-center justify-end">
        <a href="{{ route('admin.categories.create') }}" class="rounded-xl bg-yoonu-500 hover:bg-yoonu-600 text-white px-5 py-2.5 text-sm font-medium transition inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Nouvelle catégorie
        </a>
    </div>

    <div class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wider text-gray-400 border-b border-gray-100 dark:border-white/[0.06]">
                        <th class="px-5 py-3 font-medium">Nom</th>
                        <th class="px-5 py-3 font-medium">Description</th>
                        <th class="px-5 py-3 font-medium">Contenus</th>
                        <th class="px-5 py-3 font-medium">Vidéos</th>
                        <th class="px-5 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/[0.06]">
                    @forelse ($categories as $cat)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                            <td class="px-5 py-3 font-medium text-gray-900 dark:text-white">{{ $cat->nom }}</td>
                            <td class="px-5 py-3 text-gray-500 max-w-[320px] truncate">{{ $cat->description ?? '—' }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $cat->contenus_count }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $cat->videos_count }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.categories.edit', $cat->id) }}"
                                       class="rounded-lg px-3 py-1.5 text-xs font-medium text-yoonu-600 hover:bg-yoonu-50 dark:hover:bg-white/[0.04] transition">Modifier</a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $cat->id) }}"
                                          onsubmit="return confirm('Supprimer cette catégorie ?')">
                                        @csrf @method('DELETE')
                                        <button class="rounded-lg px-3 py-1.5 text-xs font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition">Supprimer</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">Aucune catégorie trouvée.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $categories->links() }}</div>
@endsection
