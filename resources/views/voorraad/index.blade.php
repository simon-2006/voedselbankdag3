@extends('layouts.app')

@section('title', 'Overzicht Productvoorraden')

@section('content')
    <section class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <h1 class="h2 mb-0 text-success text-decoration-underline fw-bold">
                    Overzicht Productvoorraden
                </h1>

                <form method="GET" action="{{ route('voorraad.index') }}" class="d-flex flex-wrap gap-2">
                    <select name="categorie" class="form-select" style="min-width: 230px;">
                        <option value="">Selecteer Categorie</option>
                        @foreach ($categorieen as $categorie)
                            <option value="{{ $categorie }}" {{ $gekozenCategorie === $categorie ? 'selected' : '' }}>
                                {{ $categorie }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-secondary">Toon Voorraad</button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Productnaam</th>
                            <th>Categorie</th>
                            <th>Eenheid</th>
                            <th>Aantal</th>
                            <th>Houdbaarheidsdatum</th>
                            <th>Magazijn</th>
                            <th class="text-center">Voorraad Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($foutmelding)
                            <tr>
                                <td colspan="7">
                                    <div class="alert alert-danger mb-0">{{ $foutmelding }}</div>
                                </td>
                            </tr>
                        @elseif ($voorraadProducten->isEmpty())
                            <tr>
                                <td colspan="7">
                                    <div class="alert alert-warning mb-0 text-center">
                                        Er zijn geen producten bekend die behoren bij de geselecteerde productcategorie.
                                    </div>
                                </td>
                            </tr>
                        @else
                            @foreach ($voorraadProducten as $product)
                                <tr>
                                    <td>{{ $product->Productnaam }}</td>
                                    <td>{{ $product->Categorie }}</td>
                                    <td>{{ $product->Eenheid }}</td>
                                    <td>{{ $product->Aantal }}</td>
                                    <td>{{ \Carbon\Carbon::parse($product->Houdbaarheidsdatum)->format('d-m-Y') }}</td>
                                    <td>{{ $product->Magazijn }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('voorraad.show', $product->ProductPerMagazijnId) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            📘 Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('home') }}" class="btn btn-primary text-white">Home</a>
            </div>
        </div>
    </section>
@endsection
