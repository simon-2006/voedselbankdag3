@extends('layouts.app')

@section('title', 'Overzicht Leveranciers')

@section('content')
    <section class="wireframe-card mb-4">
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-3">
            <h1 class="wireframe-title m-0">Overzicht Leveranciers</h1>

            {{-- Filter formulier voor LeverancierType [cite: 51, 58] --}}
            <form method="GET" action="{{ route('leverancier.index') }}" class="leverancier-filter-form ms-auto">
                <select name="leverancier_type" class="form-select wireframe-select">
                    <option value="">Selecteer Leveranciertype</option>
                    @foreach ($types as $type)
                        <option value="{{ $type }}" @selected($geselecteerdeType === $type)>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn wireframe-btn-secondary text-nowrap">Toon Leveranciers</button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle wireframe-table">
                <thead>
                    <tr>
                        <th>Naam</th> {{-- [cite: 45, 76] --}}
                        <th>Contactpersoon</th> {{-- [cite: 45, 76] --}}
                        <th>Email</th> {{-- [cite: 45, 71] --}}
                        <th>Mobiel</th> {{-- [cite: 45, 71] --}}
                        <th>Leveranciernummer</th> {{-- [cite: 45, 76] --}}
                        <th>LeverancierType</th> {{-- [cite: 45, 76] --}}
                        <th class="text-center">Product Details</th> {{-- [cite: 32, 57] --}}
                    </tr>
                </thead>
                <tbody>
                    @if ($toonLegeMelding)
                        {{-- Specifieke melding uit Scenario 2 van US_07 [cite: 53, 59] --}}
                        <tr>
                            <td colspan="7">
                                <div class="alert alert-warning m-2 text-center">
                                    Er zijn geen leveranciers bekent van het geselecteerde leverancierstype
                                </div>
                            </td>
                        </tr>
                    @else
                        @forelse ($leveranciers as $leverancier)
                            <tr>
                                <td>{{ $leverancier->Naam }}</td>
                                <td>{{ $leverancier->ContactPersoon }}</td>
                                <td>{{ $leverancier->Email }}</td>
                                <td>{{ $leverancier->Mobiel }}</td>
                                <td>{{ $leverancier->LeverancierNummer }}</td>
                                <td>{{ $leverancier->LeverancierType }}</td>
                                <td class="text-center">
                                    {{-- Link naar het productoverzicht van deze leverancier (US_08) [cite: 10, 11] --}}
                                            <a href="{{ route('leverancier.producten', $leverancier->Id) }}" class="icon-link" title="Bekijk product details">                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                            <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5Zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5H11Z"/>
                                            <path d="M4.5 9.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5Zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5Z"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Geen leveranciers gevonden</td>
                            </tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end gap-2">
            @if ($toonLegeMelding)
                <a href="{{ route('leverancier.index') }}" class="btn btn-primary">Terug</a> {{--  --}}
            @endif
            <a href="{{ route('home') }}" class="btn btn-primary">Home</a> {{-- [cite: 56, 59] --}}
        </div>
    </section>
@endsection