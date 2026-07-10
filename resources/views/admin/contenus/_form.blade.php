@php
    $c = $contenu ?? null;
    $typeLabels = ['article' => 'Article', 'conseil' => 'Conseil', 'faq' => 'FAQ'];
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 space-y-5">
        <div>
            <label class="block text-xs uppercase tracking-wider text-gray-400 mb-1.5">Titre</label>
            <input type="text" name="titre" value="{{ old('titre', $c->titre ?? '') }}" required
                   class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#25101A] px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">
        </div>
        <div>
            <label class="block text-xs uppercase tracking-wider text-gray-400 mb-1.5">Corps</label>
            <textarea name="corps" rows="12" required
                      class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#25101A] px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">{{ old('corps', $c->corps ?? '') }}</textarea>
        </div>
    </div>

    <div class="space-y-5">
        <div>
            <label class="block text-xs uppercase tracking-wider text-gray-400 mb-1.5">Type</label>
            <select name="type" required
                    class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#25101A] px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">
                @foreach ($typeLabels as $val => $label)
                    <option value="{{ $val }}" @selected(old('type', $c->type?->value ?? '')===$val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs uppercase tracking-wider text-gray-400 mb-1.5">Catégorie</label>
            <select name="categorie_id" required
                    class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#25101A] px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected((int)old('categorie_id', $c->categorie_id ?? 0)===$cat->id)>{{ $cat->nom }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs uppercase tracking-wider text-gray-400 mb-1.5">Langue</label>
            <select name="langue" required
                    class="w-full rounded-xl border border-gray-200 dark:border-white/[0.08] bg-white dark:bg-[#25101A] px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-yoonu-500">
                <option value="fr" @selected(old('langue', $c->langue ?? 'fr')==='fr')>Français</option>
                <option value="wo" @selected(old('langue', $c->langue ?? '')==='wo')>Wolof</option>
            </select>
        </div>
        <div>
            <label class="block text-xs uppercase tracking-wider text-gray-400 mb-1.5">Image de couverture</label>
            @if ($c && $c->image_couverture)
                <img src="{{ asset('storage/' . $c->image_couverture) }}" alt="" class="mb-2 h-28 w-full rounded-xl object-cover">
            @endif
            <input type="file" name="image_couverture" accept="image/*"
                   class="w-full text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-yoonu-50 file:px-3 file:py-2 file:text-yoonu-700 file:text-xs file:font-medium">
        </div>
        <label class="flex items-center gap-2.5 cursor-pointer">
            <input type="checkbox" name="est_publie" value="1" @checked(old('est_publie', $c->est_publie ?? false))
                   class="rounded border-gray-300 text-yoonu-500 focus:ring-yoonu-500">
            <span class="text-sm text-gray-700 dark:text-gray-300">Publier ce contenu</span>
        </label>
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <button class="rounded-xl bg-yoonu-500 hover:bg-yoonu-600 text-white px-6 py-2.5 text-sm font-medium transition">{{ $submitLabel }}</button>
    <a href="{{ route('admin.contenus.index') }}" class="rounded-xl border border-gray-200 dark:border-white/[0.08] px-6 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/[0.03] transition">Annuler</a>
</div>
