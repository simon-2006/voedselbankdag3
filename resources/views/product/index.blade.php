@extends('layouts.app')

@section('title', 'Overzicht Producten')

@section('content')
    <section class="container mt-4">
        <div class="card shadow-sm wireframe-card">
            <div class="card-body">
                <h1 class="wireframe-title mb-4">Overzicht producten</h1>

                {{-- Bovenste gedeelte: Informatie over de Leverancier (Zie Wireframe-03) --}}
                <div class="row mb-5">
                    <div class="col-md-5">
                        <table class="table table-bordered wireframe-table mb-0">
                            <tbody>
                                <tr>
                                    <th class="w-50 bg-light">Naam:</th>
                                    <td>{{ $leverancier->Naam }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Leveranciernummer:</th>
                                    <td>{{ $leverancier->LeverancierNummer }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Leveranciertype:</th>
                                    <td>{{ $leverancier->LeverancierType }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Onderste gedeelte: De Producten Tabel --}}
                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle wireframe-table">
                        <thead>
                            <tr>
                                <th>Naam</th>
                                <th>Soort Allergie</th>
                                <th>Barcode</th>
                                <th>Houdbaarheidsdatum</th>
                                <th class="text-center">Wijzig Product</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($producten as $product)
                                <tr>
                                    <td>{{ $product->Naam }}</td>
                                    <td>{{ $product->SoortAllergie ?? 'Geen' }}</td>
                                    <td>{{ $product->Barcode }}</td>
                                    <td>{{ $product->Houdbaarheidsdatum }}</td>
                                    <td class="text-center">
                                        {{-- Link naar het formulier om de datum aan te passen --}}
                                        <a href="{{ route('product.edit', $product->Id) }}" class="icon-link" title="Wijzig product">
                                            {{-- Een passend 'edit' icoontje in plaats van het 'oogje' --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Geen producten gevonden voor deze leverancier.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Knoppen rechtsonder --}}
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('leverancier.index') }}" class="btn btn-primary">Terug</a>
                    <a href="{{ route('home') }}" class="btn btn-primary">Home</a>
                </div>
            </div>
        </div>
    </section>
@endsection