@extends('layouts.app')

@section('title', 'Wijzig allergie')

@section('content')
    <section class="wireframe-card mb-4">
        <h1 class="wireframe-title mb-4">Wijzig allergie</h1>

        <form method="POST" action="{{ route('allergie.update', ['gezin' => $gezinId, 'persoon' => $persoon->PersoonId]) }}">
            @csrf

            <select name="allergie_id" class="form-select wireframe-select mb-3 @error('allergie_id') is-invalid @enderror" required>
                <option value="">Selecteer Allergie</option>
                @foreach ($allergieen as $allergie)
                    <option value="{{ $allergie->Id }}" @selected((int) old('allergie_id', (int) ($persoon->AllergieId ?? 0)) === (int) $allergie->Id)>
                        {{ $allergie->Naam }}
                    </option>
                @endforeach
            </select>

            @error('allergie_id')
                <div class="invalid-feedback d-block mb-3">{{ $message }}</div>
            @enderror

            @if (session('wijziging_niet_doorgvoerd'))
                <div class="alert alert-danger mb-3">{{ session('wijziging_niet_doorgvoerd') }}</div>
            @elseif (session('wijziging_doorgvoerd'))
                <div class="alert alert-success mb-3">{{ session('wijziging_doorgvoerd') }}</div>
            @elseif ($heeftHoogRisico)
                <div class="alert alert-danger mb-3">
                    Voor het wijzigen van deze allergie wordt geadviseerd eerst een arts te raadplegen vanwege een hoog risico op een anafylactisch shock
                </div>
            @endif

            <div class="d-flex flex-wrap justify-content-between gap-3">
                <button type="submit" class="btn wireframe-btn-secondary">Wijzig Allergie</button>

                <div class="d-flex gap-2">
                    <a href="{{ route('allergie.gezin', ['gezin' => $gezinId]) }}" class="btn btn-primary">Terug</a>
                    <a href="{{ route('home') }}" class="btn btn-primary">Home</a>
                </div>
            </div>
        </form>
    </section>

    @if (session('wijziging_doorgvoerd'))
        <script>
            // Na succesvolle wijziging automatisch terug naar de gezinsdetails.
            setTimeout(() => {
                window.location.href = @json(route('allergie.gezin', ['gezin' => $gezinId]));
            }, 3000);
        </script>
    @endif
@endsection
