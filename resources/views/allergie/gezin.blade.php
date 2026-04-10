@extends('layouts.app')

@section('title', 'Allergieen in het gezin')

@section('content')
    <section class="wireframe-card mb-4">
        <h1 class="wireframe-title mb-4">Allergieën in het gezin</h1>

        <table class="table table-bordered summary-table mb-4">
            <tbody>
                <tr>
                    <th>Gezinsnaam:</th>
                    <td>{{ $gezin->Naam }}</td>
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
                        <th>Naam</th>
                        <th>Type Persoon</th>
                        <th>Gezinsrol</th>
                        <th>Allergie</th>
                        <th class="text-center">Wijzig Allergie</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($personen as $persoon)
                        <tr>
                            <td>{{ $persoon->Naam }}</td>
                            <td>{{ $persoon->TypePersoon }}</td>
                            <td>{{ $persoon->Gezinsrol }}</td>
                            <td>{{ $persoon->Allergie }}</td>
                            <td class="text-center">
                                <a href="{{ route('allergie.edit', ['gezin' => $gezin->Id, 'persoon' => $persoon->PersoonId]) }}" class="icon-link" title="Wijzig allergie">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708L5.207 14.5H2v-3.207L12.146.146zm.708.708L3 10.707V13h2.293l9.854-9.854-2.293-2.292z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Geen gezinsleden gevonden</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('allergie.index') }}" class="btn btn-primary">terug</a>
            <a href="{{ route('home') }}" class="btn btn-primary">home</a>
        </div>
    </section>
@endsection
