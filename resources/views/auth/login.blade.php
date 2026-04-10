@extends('layouts.app')

@section('title', 'Inloggen | Voedselbank Samen')

@section('content')
    <section class="row justify-content-center reveal">
        <div class="col-xl-10">
            <div class="card border-0 shadow-lg auth-panel overflow-hidden">
                <div class="row g-0">
                    <div class="col-lg-5 p-4 p-lg-5 bg-light border-end">
                        <p class="section-eyebrow mb-2">Welkom terug</p>
                        <h1 class="display-6 fw-semibold mb-3">Log in op je account</h1>
                        <p class="text-secondary mb-0">Log in met het e-mailadres uit de tabel Gebruiker.</p>
                    </div>

                    <div class="col-lg-7 p-4 p-lg-5">
                        <form method="POST" action="{{ route('login') }}" class="vstack gap-3">
                            @csrf

                            <div>
                                <label for="email" class="form-label fw-semibold">E-mail</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" class="form-control form-control-lg @error('email') is-invalid @enderror" required autocomplete="email">
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="form-label fw-semibold">Wachtwoord</label>
                                <input id="password" name="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" required autocomplete="current-password">
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg">Inloggen</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
