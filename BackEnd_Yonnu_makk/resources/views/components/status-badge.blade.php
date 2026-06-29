@props(['color' => 'gray', 'label' => ''])
@php
    // Pastille minimaliste : un point coloré + un libellé neutre.
    // Les couleurs reprennent la palette de triage de la vitrine.
    $dot = [
        'gray'  => 'bg-gray-300 dark:bg-gray-600',
        'brand' => 'bg-yoonu-500',
        'amber' => 'bg-rapide-500',
        'green' => 'bg-standard-500',
        'red'   => 'bg-urgence-500',
    ];
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 whitespace-nowrap text-xs font-medium text-gray-600 dark:text-gray-300']) }}>
    <span class="h-1.5 w-1.5 flex-shrink-0 rounded-full {{ $dot[$color] ?? $dot['gray'] }}"></span>
    {{ $label ?: $slot }}
</span>
