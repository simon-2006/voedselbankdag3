@extends('layouts.app')

@section('title', 'Overzicht voedselpakketten')

@section('content')
    <section class="wireframe-card mb-4">
        <h1 class="wireframe-title mb-4">Overzicht Voedselpakketten</h1>

        <table class="table table-bordered summary-table mb-4">
            <tbody>
                <tr>
                    <th>Naam:</th>
                    <td>{{ $gezin->Gezinsnaam }}</td>
                </tr>
                <tr>
                    <th>Omschrijving:</th>
                    <td>{{ $gezin->Omschrijving }}</td>
                </tr>
                <tr>
                    <th>Totaal aantal Personen:</th>
                    <td>{{ $gezin->TotaalAantalPersonen }}</td>
                </tr>
            </tbody>
        </table>

        <div class="table-responsive">
            <table class="table table-bordered align-middle wireframe-table">
                <thead>
                    <tr>
                        <th>Pakketnummer</th>
                        <th>Datum samenstelling</th>
                        <th>Datum uitgifte</th>
                        <th>Status</th>
                        <th>Aantal producten</th>
                        <th class="text-center">Wijzig Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($voedselpakketten as $voedselpakket)
                        <tr>
                            <td>{{ $voedselpakket->PakketNummer }}</td>
                            <td>{{ $voedselpakket->DatumSamenstelling }}</td>
                            <td>{{ $voedselpakket->DatumUitgifteLabel }}</td>
                            <td>{{ $voedselpakket->StatusLabel }}</td>
                            <td>{{ $voedselpakket->AantalProducten }}</td>
                            <td class="text-center">
                                <a
                                    href="{{ route('voedselpakketten.status.edit', ['voedselpakket' => $voedselpakket->VoedselpakketId]) }}"
                                    class="icon-link"
                                    title="Wijzig status"
                                    aria-label="Wijzig status van voedselpakket {{ $voedselpakket->PakketNummer }}"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708L5.207 14.5H2v-3.207L12.146.146zm.708.708L3 10.707V13h2.293l9.854-9.854-2.293-2.292z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Geen voedselpakketten gevonden voor dit gezin.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('voedselpakketten.index') }}" class="btn btn-primary">terug</a>
            <a href="{{ route('home') }}" class="btn btn-primary">home</a>
        </div>
    </section>
@endsection
