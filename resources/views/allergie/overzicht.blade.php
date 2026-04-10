@extends('layouts.app')

@section('title', 'Overzicht gezinnen met allergieen')

@section('content')
    <section class="wireframe-card mb-4">
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-3">
            <h1 class="wireframe-title m-0">Overzicht gezinnen met allergieën</h1>

            <form method="GET" action="{{ route('allergie.index') }}" class="allergie-filter-form ms-auto">
                <select name="allergie_id" class="form-select wireframe-select">
                    <option value="0">Selecteer Allergie</option>
                    @foreach ($allergieen as $allergie)
                        <option value="{{ $allergie->Id }}" @selected((int) $geselecteerdeAllergieId === (int) $allergie->Id)>
                            {{ $allergie->Naam }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn wireframe-btn-secondary">Toon Gezinnen</button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle wireframe-table">
                <thead>
                    <tr>
                        <th>Naam</th>
                        <th>Omschrijving</th>
                        <th>Volwassenen</th>
                        <th>Kinderen</th>
                        <th>Babys</th>
                        <th>Vertegenwoordiger</th>
                        <th class="text-center">Allergie Details</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($toonLegeMelding)
                        <tr>
                            <td colspan="7">
                                <div class="alert alert-warning m-2 text-center">
                                    Er zijn geen gezinnen bekent die de geselecteerde allergie hebben
                                </div>
                            </td>
                        </tr>
                    @else
                        @forelse ($gezinnen as $gezin)
                            <tr>
                                <td>{{ $gezin->Naam }}</td>
                                <td>{{ $gezin->Omschrijving }}</td>
                                <td>{{ $gezin->AantalVolwassenen }}</td>
                                <td>{{ $gezin->AantalKinderen }}</td>
                                <td>{{ $gezin->AantalBabys }}</td>
                                <td>{{ $gezin->Vertegenwoordiger }}</td>
                                <td class="text-center">
                                    <a href="{{ route('allergie.gezin', ['gezin' => $gezin->Id]) }}" class="icon-link" title="Bekijk allergiedetails">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                            <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5Zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5H11Z"/>
                                            <path d="M4.5 9.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5Zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5Z"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Geen gegevens beschikbaar</td>
                            </tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end">
            <a href="{{ route('home') }}" class="btn btn-primary">Home</a>
        </div>
    </section>
@endsection
