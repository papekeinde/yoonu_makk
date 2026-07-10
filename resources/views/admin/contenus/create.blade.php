@extends('layouts.dashboard')

@section('title', 'Nouveau contenu')

@section('content')
    <div class="mb-5">
        <a href="{{ route('admin.contenus.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-yoonu-600">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Retour aux contenus
        </a>
    </div>

    <form method="POST" action="{{ route('admin.contenus.store') }}" enctype="multipart/form-data"
          class="rounded-2xl border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#25101A] p-6">
        @csrf
        @include('admin.contenus._form', ['submitLabel' => 'Créer le contenu'])
    </form>
@endsection
