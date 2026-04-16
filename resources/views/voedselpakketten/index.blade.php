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
                        <th scope="col">Naam</th>
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
                                    <a
                                        href="{{ route('voedselpakketten.gezin.show', ['gezin' => $gezin->GezinId]) }}"
                                        class="icon-link"
                                        title="{{ $gezin->AantalPakketten }} pakket(ten), {{ $gezin->TotaalProductEenheden }} producteenheden"
                                        aria-label="Bekijk voedselpakket details voor {{ $gezin->Gezinsnaam }}"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                            <path d="M8.5 1.25a.75.75 0 0 0-1 0l-5.5 4.4A.75.75 0 0 0 1.75 6.9v6.35c0 .83.67 1.5 1.5 1.5h9.5c.83 0 1.5-.67 1.5-1.5V6.9a.75.75 0 0 0-.25-.57l-5.5-5.08Zm-5.25 5.9L8 2.84l4.75 4.3v6.1h-9.5v-6.1Z"/>
                                            <path d="M5 8.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5Zm0 2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5Z"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
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
