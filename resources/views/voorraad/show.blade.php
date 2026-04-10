@extends('layouts.app')

@section('title', 'Product Details – ' . $product->Productnaam)

@section('content')
    <section class="card border-0 shadow-sm">
        <div class="card-body">

            <h1 class="h2 mb-4 text-success text-decoration-underline fw-bold">
                Product Details – {{ $product->Productnaam }}
            </h1>

            @if ($foutmelding)
                <div class="alert alert-danger">{{ $foutmelding }}</div>
            @else
                <table class="table w-auto">
                    <tbody>
                        <tr>
                            <th>Productnaam</th>
                            <td>{{ $product->Productnaam }}</td>
                        </tr>
                        <tr>
                            <th>Barcode</th>
                            <td>{{ $product->Barcode }}</td>
                        </tr>
                        <tr>
                            <th>Houdbaarheidsdatum</th>
                            <td>{{ \Carbon\Carbon::parse($product->Houdbaarheidsdatum)->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <th>Magazijnlocatie</th>
                            <td>{{ $product->MagazijnLocatie }}</td>
                        </tr>
                        <tr>
                            <th>Ontvangstdatum</th>
                            <td>{{ \Carbon\Carbon::parse($product->Ontvangstdatum)->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <th>Uitleveringsdatum</th>
                            <td>
                                {{ $product->Uitleveringsdatum
                                    ? \Carbon\Carbon::parse($product->Uitleveringsdatum)->format('d-m-Y')
                                    : '–' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Aantal op voorraad</th>
                            <td>{{ $product->AantalOpVoorraad }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif

            <div class="d-flex gap-2 mt-3">
                @if (!$foutmelding)
                    <a href="{{ route('voorraad.edit', $product->ProductPerMagazijnId) }}"
                       class="btn btn-info text-white">
                        Wijzig
                    </a>
                @endif
                <a href="{{ route('voorraad.index') }}" class="btn btn-info text-white">Terug naar overzicht</a>
                <a href="{{ route('dashboard') }}" class="btn btn-info text-white">Home</a>
            </div>

        </div>
    </section>
@endsection