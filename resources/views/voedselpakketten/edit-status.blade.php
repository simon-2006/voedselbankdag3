@extends('layouts.app')

@section('title', 'Wijzig voedselpakket status')

@section('content')
    <section class="wireframe-card mb-4">
        <h1 class="wireframe-title mb-4">Wijzig voedselpakket status</h1>

        <form
            method="POST"
            action="{{ route('voedselpakketten.status.update', ['voedselpakket' => $pakket->VoedselpakketId]) }}"
            id="voedselpakketStatusForm"
            novalidate
        >
            @csrf
            @method('PUT')

            <select
                name="status"
                id="status"
                class="form-select wireframe-select mb-3 @error('status') is-invalid @enderror"
                @disabled($isWijzigenGeblokkeerd)
                required
            >
                @foreach ($statusOpties as $statusWaarde => $statusLabelOptie)
                    <option value="{{ $statusWaarde }}" @selected((string) old('status', $geselecteerdeStatus) === (string) $statusWaarde)>
                        {{ $statusLabelOptie }}
                    </option>
                @endforeach
            </select>

            @error('status')
                <div class="invalid-feedback d-block mb-3">{{ $message }}</div>
            @enderror

            @if (session('voedselpakket_status_wijziging_mislukt'))
                <div class="alert alert-danger mb-3">{{ session('voedselpakket_status_wijziging_mislukt') }}</div>
            @elseif (session('voedselpakket_status_wijziging_gelukt'))
                <div class="alert alert-success mb-3">{{ session('voedselpakket_status_wijziging_gelukt') }}</div>
            @elseif (session('voedselpakket_status_ongewijzigd'))
                <div class="alert alert-warning mb-3">{{ session('voedselpakket_status_ongewijzigd') }}</div>
            @elseif ($isWijzigenGeblokkeerd)
                <div class="alert alert-danger mb-3">
                    Dit gezin is niet meer ingeschreven bij de voedselbank en daarom kan er geen voedselpakket worden uitgereikt
                </div>
            @endif

            <div class="d-flex flex-wrap justify-content-between gap-3">
                <button type="submit" class="btn wireframe-btn-secondary" @disabled($isWijzigenGeblokkeerd)>
                    Wijzig status voedselpakket
                </button>

                <div class="d-flex gap-2">
                    <a href="{{ route('voedselpakketten.gezin.show', ['gezin' => $pakket->GezinId]) }}" class="btn btn-primary">terug</a>
                    <a href="{{ route('home') }}" class="btn btn-primary">home</a>
                </div>
            </div>
        </form>
    </section>

    @if (session('voedselpakket_status_wijziging_gelukt'))
        <script>
            setTimeout(() => {
                window.location.href = @json(route('voedselpakketten.gezin.show', ['gezin' => $pakket->GezinId]));
            }, 3000);
        </script>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('voedselpakketStatusForm');
            const select = document.getElementById('status');

            if (!form || !select || select.disabled) {
                return;
            }

            const toegestaneStatussen = new Set(['NietUitgereikt', 'Uitgereikt']);

            form.addEventListener('submit', (event) => {
                if (!toegestaneStatussen.has(select.value)) {
                    event.preventDefault();
                    select.classList.add('is-invalid');
                    return;
                }

                select.classList.remove('is-invalid');
            });
        });
    </script>
@endpush
