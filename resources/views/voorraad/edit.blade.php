@extends('layouts.app')

@section('title', 'Wijzig Product Details – ' . $product->Productnaam)

@section('content')
    <section class="card border-0 shadow-sm align-items-center">
        <div class="card-body">

            <h1 class="h2 mb-4 text-success text-decoration-underline fw-bold">
                Wijzig Product Details – {{ $product->Productnaam }}
            </h1>

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            @if ($foutmelding ?? false)
                <div class="alert alert-danger">{{ $foutmelding }}</div>
            @endif

            <form method="POST" action="{{ route('voorraad.update', $product->ProductPerMagazijnId) }}">
                @csrf
                @method('PUT')

                <table class="table w-auto mb-4">
                    <tbody>
                        <tr>
                            <th>
                                <label for="Productnaam" class="form-label mb-0">Productnaam</label>
                            </th>
                            <td>
                                <input type="text" id="Productnaam" name="Productnaam"
                                    class="form-control @error('Productnaam') is-invalid @enderror"
                                    value="{{ old('Productnaam', $product->Productnaam) }}" required>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="Houdbaarheidsdatum" class="form-label mb-0">Houdbaarheidsdatum</label>
                            </th>
                            <td>
                                <input type="date" id="Houdbaarheidsdatum" name="Houdbaarheidsdatum"
                                    class="form-control @error('Houdbaarheidsdatum') is-invalid @enderror"
                                    value="{{ old('Houdbaarheidsdatum', \Carbon\Carbon::parse($product->Houdbaarheidsdatum)->format('Y-m-d')) }}"
                                    required>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="Barcode" class="form-label mb-0">Barcode</label>
                            </th>
                            <td>
                                <input type="text" id="Barcode" name="Barcode"
                                    class="form-control @error('Barcode') is-invalid @enderror"
                                    value="{{ old('Barcode', $product->Barcode) }}" required>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="MagazijnLocatie" class="form-label mb-0">Magazijnlocatie</label>
                            </th>
                            <td>
                                <select id="MagazijnLocatie" name="MagazijnLocatie"
                                    class="form-select @error('MagazijnLocatie') is-invalid @enderror">
                                    @foreach ($locaties as $locatie)
                                        <option value="{{ $locatie }}"
                                            {{ old('MagazijnLocatie', $product->MagazijnLocatie) === $locatie ? 'selected' : '' }}>
                                            {{ $locatie }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="Ontvangstdatum" class="form-label mb-0">Ontvangstdatum</label>
                            </th>
                            <td>
                                <input type="date" id="Ontvangstdatum" name="Ontvangstdatum"
                                    class="form-control @error('Ontvangstdatum') is-invalid @enderror"
                                    value="{{ old('Ontvangstdatum', \Carbon\Carbon::parse($product->Ontvangstdatum)->format('Y-m-d')) }}"
                                    required>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="AantalUitgeleverd" class="form-label mb-0">Aantal uitgeleverde producten</label>
                            </th>
                            <td>
                                <input type="number" id="AantalUitgeleverd" name="AantalUitgeleverd"
                                    class="form-control @error('AantalUitgeleverd') is-invalid @enderror"
                                    min="0"
                                    value="{{ old('AantalUitgeleverd', 0) }}">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="Uitleveringsdatum" class="form-label mb-0">Uitleveringsdatum</label>
                            </th>
                            <td>
                                <input type="date" id="Uitleveringsdatum" name="Uitleveringsdatum"
                                    class="form-control @error('Uitleveringsdatum') is-invalid @enderror"
                                    value="{{ old('Uitleveringsdatum', $product->Uitleveringsdatum ? \Carbon\Carbon::parse($product->Uitleveringsdatum)->format('Y-m-d') : '') }}">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="AantalOpVoorraad" class="form-label mb-0">Aantal op voorraad</label>
                            </th>
                            <td>
                                <input type="number" id="AantalOpVoorraad" name="AantalOpVoorraad"
                                    class="form-control @error('AantalOpVoorraad') is-invalid @enderror"
                                    min="0"
                                    value="{{ old('AantalOpVoorraad', $product->AantalOpVoorraad) }}" required>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-info text-white">Wijzig Product Details</button>
                    <a href="{{ route('voorraad.show', $product->ProductPerMagazijnId) }}" class="btn btn-info text-white ms-auto">Annuleren</a>
                    <a href="{{ route('dashboard') }}" class="btn btn-info text-white">Home</a>
                </div>
            </form>

        </div>
    </section>
@endsection