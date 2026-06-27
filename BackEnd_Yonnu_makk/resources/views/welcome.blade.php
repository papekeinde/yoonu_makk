@extends('layouts.website')

@section('title', 'YOONU MAKK — Site web')

@section('content')
    @include('sections.home.navbar')
    @include('sections.home.hero')
    @include('sections.home.marquee')
    @include('sections.home.value')
    @include('sections.home.actors')
    @include('sections.home.features-images')
    @include('sections.home.triage')
    @include('sections.home.social_proof')
    @include('sections.home.download')
    @include('sections.home.cta')
    @include('sections.home.footer')
@endsection
