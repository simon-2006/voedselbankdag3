@extends('layouts.app')

@section('title', 'Overzicht voedselpakketten | Voedselbank Maaskantje')

@section('content')
    <section class="card border-0 shadow-lg p-4 p-lg-5">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            <h1 class="h2 mb-0 text-success">
                Overzicht gezinnen met voedselpakketten
            </h1>

            <form
                method="GET"
                action="{{ route('voedselpakketten.index') }}"
                class="d-flex flex-column flex-sm-row gap-2"
                id="overzichtFilterForm"
                novalidate
            >
                <label for="eetwens_id" class="visually-hidden">Selecteer Eetwens</label>

                <select
                    name="eetwens_id"
                    id="eetwens_id"
                    class="form-select @error('eetwens_id') is-invalid @enderror"
                    aria-label="Selecteer Eetwens"
                >
                    <option value="">Selecteer Eetwens</option>

                    @foreach ($eetwensen as $eetwens)
                        <option
                            value="{{ $eetwens->Id }}"
                            @selected((string) $selectedEetwensId === (string) $eetwens->Id)
                        >
                            {{ $eetwens->Naam }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-secondary fw-semibold px-3">
                    Toon Gezinnen
                </button>
            </form>
        </div>

        @error('eetwens_id')
            <div class="alert alert-danger mb-4" role="alert">
                {{ $message }}
            </div>
        @enderror

        @if (! empty($feedbackMessage) && ! empty($feedbackType))
            <div class="alert alert-{{ $feedbackType }} shadow-sm mb-4" role="alert">
                {{ $feedbackMessage }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">Gezinsnaam</th>
                        <th scope="col">Omschrijving</th>
                        <th scope="col">Volwassenen</th>
                        <th scope="col">Kinderen</th>
                        <th scope="col">Babys</th>
                        <th scope="col">Vertegenwoordiger</th>
                        <th scope="col" class="text-center">Voedselpakket Details</th>
                    </tr>
                </thead>

                <tbody>
                    @if ($gezinnen->isNotEmpty())
                        @foreach ($gezinnen as $gezin)
                            <tr>
                                <td class="fw-semibold">{{ $gezin->Gezinsnaam }}</td>
                                <td>{{ $gezin->Omschrijving }}</td>
                                <td>{{ $gezin->AantalVolwassenen }}</td>
                                <td>{{ $gezin->AantalKinderen }}</td>
                                <td>{{ $gezin->AantalBabys }}</td>
                                <td>
                                    {{ trim($gezin->Vertegenwoordiger) !== '' ? $gezin->Vertegenwoordiger : 'Onbekend' }}
                                </td>
                                <td class="text-center">
                                    <span
                                        title="{{ $gezin->AantalPakketten }} pakket(ten), {{ $gezin->TotaalProductEenheden }} producteenheden"
                                    >
                                        Details
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @elseif ($selectedEetwensId !== null)
                        <tr>
                            <td colspan="7" class="p-0 border-0">
                                <div class="alert alert-warning m-2 mb-0" role="alert">
                                    Er zijn geen gezinnen bekent die de geselecteerde eetwens hebben
                                </div>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="7" class="text-secondary py-4 text-center">
                                Er zijn nog geen gezinnen met voedselpakketten gevonden.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-3">
            <a href="{{ route('home') }}" class="btn btn-primary btn-sm px-3">
                home
            </a>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('overzichtFilterForm');
            const select = document.getElementById('eetwens_id');

            if (!form || !select) {
                return;
            }

            const allowedValues = new Set(
                Array.from(select.options).map(function (option) {
                    return option.value;
                })
            );

            form.addEventListener('submit', function (event) {
                if (!allowedValues.has(select.value)) {
                    event.preventDefault();
                    select.classList.add('is-invalid');
                    return;
                }

                select.classList.remove('is-invalid');
            });
        });
    </script>
@endpush
