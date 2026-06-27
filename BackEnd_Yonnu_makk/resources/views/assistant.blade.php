@extends('layouts.website')

@section('title', 'Assistant IA - YOONU MAKK')

@section('content')
    <header class="sticky top-0 z-20 border-b border-yoonu-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex w-full max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-3 sm:px-6">
            <div class="flex items-center gap-3 font-display text-lg font-bold tracking-tight text-yoonu-900 sm:text-xl">
                <img src="{{ asset('logo-yoonu-makk.svg') }}" alt="YOONU MAKK" class="h-9 w-9 rounded-full border border-yoonu-200">
                <span>YOONU MAKK</span>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-sm">
                <a class="font-medium text-slate-600 transition hover:text-yoonu-700" href="{{ url('/') }}">Accueil</a>
                <a class="font-medium text-slate-600 transition hover:text-yoonu-700" href="{{ url('/#acteurs') }}">Acteurs</a>
                <a class="rounded-lg bg-yoonu-700 px-4 py-2 font-semibold text-white transition hover:bg-yoonu-900" href="{{ url('/#telechargement') }}">Acces web</a>
            </div>
        </div>
    </header>

    <section class="py-12 sm:py-16" id="assistant-ia">
        <div class="mx-auto grid w-full max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-[1.2fr_1fr]">
            <div class="rounded-2xl border border-yoonu-200 bg-white p-6 sm:p-7 animate-fade-in animate-delay-100">
                <span class="inline-flex rounded-full bg-yoonu-100 px-3 py-1 text-xs font-bold uppercase tracking-[0.16em] text-yoonu-700">Assistant IA · Orientation conversationnelle</span>
                <h1 class="mt-4 font-display text-3xl font-bold tracking-tight text-yoonu-900 sm:text-4xl">Un assistant qui informe, reformule et oriente sans diagnostiquer.</h1>
                <p class="mt-4 text-base leading-7 text-slate-600">Cette page presente le role de l'assistant IA dans YOONU MAKK : aider l'utilisatrice a mieux exprimer sa situation, rappeler les signaux d'alerte et guider vers le bon parcours sans se substituer au professionnel de sante.</p>

                <div class="mt-6 space-y-3">
                    <div class="ml-auto max-w-[88%] rounded-2xl border border-yoonu-200 bg-yoonu-50 p-4 text-sm leading-7 text-slate-700">Bonjour, j'ai une douleur pelvienne depuis hier et je ne sais pas si je dois prendre rendez-vous ou consulter vite.</div>
                    <div class="max-w-[88%] rounded-2xl border border-yoonu-200 bg-white p-4 text-sm leading-7 text-slate-700">Je peux vous aider a structurer votre demande. Depuis combien de temps la douleur dure-t-elle, quelle est son intensite, et y a-t-il d'autres signes comme un saignement important, de la fievre ou un malaise ?</div>
                    <div class="max-w-[88%] rounded-2xl border border-yoonu-200 bg-white p-4 text-sm leading-7 text-slate-700">Si un signe d'alerte est present, l'application recommande une orientation vers les urgences. Sinon, elle aide a preparer une demande de rendez-vous plus claire pour le gynecologue.</div>
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a class="rounded-lg bg-yoonu-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-yoonu-900" href="{{ url('/#telechargement') }}">Voir les acces disponibles</a>
                    <a class="rounded-lg border border-yoonu-200 bg-white px-5 py-2.5 text-sm font-semibold text-yoonu-700 transition hover:bg-yoonu-50" href="{{ url('/') }}">Retour au site</a>
                </div>
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border border-yoonu-200 bg-white p-5 animate-fade-in animate-delay-200">
                    <span class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Ce qu'il fait</span>
                    <h3 class="mt-2 font-display text-base font-bold tracking-tight text-yoonu-900">Un cadre de conversation utile</h3>
                    <ul class="mt-3 space-y-2 text-xs text-slate-600">
                        <li class="flex items-start gap-2"><span class="mt-2 h-2 w-2 rounded-full bg-yoonu-700 flex-shrink-0"></span><span>Reformuler un motif de consultation</span></li>
                        <li class="flex items-start gap-2"><span class="mt-2 h-2 w-2 rounded-full bg-yoonu-700 flex-shrink-0"></span><span>Preparer les informations utiles avant contact medical</span></li>
                        <li class="flex items-start gap-2"><span class="mt-2 h-2 w-2 rounded-full bg-yoonu-700 flex-shrink-0"></span><span>Rappeler les situations qui demandent une vigilance accrue</span></li>
                    </ul>
                </div>

                <div class="rounded-2xl border border-yoonu-200 bg-white p-5 animate-fade-in animate-delay-300">
                    <span class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Pour qui</span>
                    <h3 class="mt-2 font-display text-base font-bold tracking-tight text-yoonu-900">Patiente et accompagnant</h3>
                    <p class="mt-2 text-xs leading-6 text-slate-600">L'assistant aide d'abord a comprendre la situation et a preparer la suite. Il est pense pour reduire l'hesitation, pas pour produire une reponse medicale definitive.</p>
                </div>

                <div class="rounded-2xl border border-red-200 bg-red-50 p-5 text-xs leading-6 text-red-700 animate-fade-in animate-delay-400">
                    <strong class="mb-1 block text-red-900">Limite importante</strong>
                    L'assistant IA ne pose aucun diagnostic. En cas de douleur intense, saignement abondant, fievre elevee, malaise, ou diminution marquee des mouvements du bebe, il faut privilegier l'evaluation medicale urgente.
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 sm:py-16">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6">
            <div class="rounded-2xl border border-yoonu-200 bg-white p-6 sm:p-7 animate-fade-in animate-delay-100">
                <div class="grid gap-4 lg:grid-cols-[1.3fr_.9fr] lg:items-end">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Parcours</span>
                        <h2 class="mt-2 font-display text-3xl font-bold tracking-tight text-yoonu-900">Comment l'assistant s'integre dans l'experience YOONU MAKK.</h2>
                    </div>
                    <div class="rounded-2xl border border-yoonu-200 bg-yoonu-50 p-4 text-xs font-semibold leading-6 text-yoonu-900">L'objectif n'est pas de remplacer l'echange clinique, mais de le preparer avec plus de clarte et moins de friction.</div>
                </div>

                <div class="mt-6 grid gap-3 md:grid-cols-3">
                    <div class="rounded-2xl border border-yoonu-200 bg-white p-4 text-xs leading-6 text-slate-700 animate-fade-in animate-delay-200"><strong class="block text-yoonu-900">1. Ecouter</strong>L'utilisatrice exprime son besoin en langage simple, sans jargon medical.</div>
                    <div class="rounded-2xl border border-yoonu-200 bg-white p-4 text-xs leading-6 text-slate-700 animate-fade-in animate-delay-300"><strong class="block text-yoonu-900">2. Structurer</strong>L'assistant aide a preciser anciennete, intensite, urgence percue et signes associes.</div>
                    <div class="rounded-2xl border border-yoonu-200 bg-white p-4 text-xs leading-6 text-slate-700 animate-fade-in animate-delay-400"><strong class="block text-yoonu-900">3. Orienter</strong>La plateforme suggere le parcours le plus adapte : information, rendez-vous ou urgence.</div>
                </div>
            </div>
        </div>
    </section>

    @include('sections.home.footer')
@endsection
