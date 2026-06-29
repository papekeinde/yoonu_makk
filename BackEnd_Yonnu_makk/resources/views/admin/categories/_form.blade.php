@php $cat = $categorie ?? null; @endphp

<div class="space-y-5 max-w-xl">
    <div>
        <label class="block text-xs uppercase tracking-wider text-gray-400 mb-1.5">Nom</label>
        <input type="text" name="nom" value="{{ old('nom', $cat->nom ?? '') }}" required maxlength="100"
               class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#25101A] px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">
    </div>
    <div>
        <label class="block text-xs uppercase tracking-wider text-gray-400 mb-1.5">Description</label>
        <textarea name="description" rows="4"
                  class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#25101A] px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">{{ old('description', $cat->description ?? '') }}</textarea>
    </div>
    <div>
        <label class="block text-xs uppercase tracking-wider text-gray-400 mb-1.5">Icône <span class="normal-case text-gray-400">(nom optionnel)</span></label>
        <input type="text" name="icone" value="{{ old('icone', $cat->icone ?? '') }}" maxlength="50" placeholder="ex: heart"
               class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#25101A] px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button class="rounded-xl bg-yoonu-500 hover:bg-yoonu-600 text-white px-6 py-2.5 text-sm font-medium transition">{{ $submitLabel }}</button>
    <a href="{{ route('admin.categories.index') }}" class="rounded-xl border border-gray-200 dark:border-white/[0.08] px-6 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/[0.03] transition">Annuler</a>
</div>
