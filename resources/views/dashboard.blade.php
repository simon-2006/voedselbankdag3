@extends('layouts.app')

@section('title', 'Dashboard | Voedselbank Samen')

@section('content')
    <section class="mb-4 reveal">
        <h1 class="h2 mb-2">Homepage voedselbank maaskantje</h1>
        <a href="{{ route('voorraad.index') }}" class="h3 text-decoration-underline">Overzicht Productvoorraden</a>
    </section>

    <section class="card border-0 shadow-lg p-4 p-lg-5 reveal mb-4">
        <p class="section-eyebrow mb-2">Mijn omgeving</p>
        <h1 class="display-6 fw-semibold mb-3">Welkom, {{ auth()->user()->name }}</h1>
        <p class="text-secondary mb-0">Je bent ingelogd. Vanaf hier kun je straks je aanvraagstatus, documenten en afspraken beheren.</p>
    </section>

    <section class="row g-3">
        <article class="col-md-6 reveal reveal-delay-1">
            <div class="card border-0 shadow-sm h-100 card-lift">
                <div class="card-body p-4">
                    <h3 class="h4 mb-2">Status aanvraag</h3>
                    <p class="text-secondary mb-0">Geen actieve aanvraag gevonden. Je kunt een nieuwe aanvraag starten via het team.</p>
                </div>
            </div>
        </article>

        <article class="col-md-6 reveal reveal-delay-2">
            <div class="card border-0 shadow-sm h-100 card-lift">
                <div class="card-body p-4">
                    <h3 class="h4 mb-2">Contactmoment</h3>
                    <p class="text-secondary mb-0">Heb je vragen? Gebruik het contactpunt in je buurtlocatie of bel tijdens openingstijden.</p>
                </div>
            </div>
        </article>

        <article class="col-12 reveal reveal-delay-3">
            <div class="card border-0 shadow-sm h-100 card-lift">
                <div class="card-body p-4 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                    <div>
                        <h3 class="h4 mb-2">Overzicht voedselpakketten</h3>
                        <p class="text-secondary mb-0">Bekijk alle gezinnen met voedselpakketten en filter op eetwens.</p>
                    </div>
                    <a href="{{ route('voedselpakketten.index') }}" class="btn btn-success px-4">Open overzicht</a>
                </div>
            </div>
        </article>
    </section>
@endsection
