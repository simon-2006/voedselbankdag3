@extends('layouts.app')

@section('title', 'Wijzig Product')

@section('content')
    @php
        $leverancier = $product->leveranciers()->first();
        $overzichtUrl = $leverancier ? route('leverancier.producten', $leverancier->Id) : route('leverancier.index');
    @endphp

    <section class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="card-title text-success mb-4">Wijzig Product</h1>

                {{-- Scenario 01: Succesmelding met Countdown Redirect --}}
                @if (session('success_melding'))
                    <div class="alert alert-success border-success auto-dismiss">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success_melding') }}
                        <hr>
                        <p class="mb-0">
                            Je wordt over <strong id="countdown-timer" style="font-size: 1.2em;">3</strong> seconden automatisch teruggestuurd naar het overzicht...
                        </p>
                    </div>

                    {{-- Het JavaScript scriptje voor de visuele 3..2..1 countdown --}}
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            let secondenRebeterend = 3;
                            const timerElement = document.getElementById('countdown-timer');
                            const bestemmingUrl = "{!! $overzichtUrl !!}";

                            // Start een timer die elke seconde afgaat
                            const aftellen = setInterval(function() {
                                secondenRebeterend--; // Haal er 1 seconde vanaf
                                timerElement.innerText = secondenRebeterend; // Update het getal op het scherm

                                // Als we bij 0 zijn, stop de timer en stuurt hij de gebruiker door
                                if (secondenRebeterend <= 0) {
                                    clearInterval(aftellen);
                                    window.location.href = bestemmingUrl;
                                }
                            }, 1000);
                        });
                    </script>
                @endif

                {{-- Scenario 02: Foutmelding --}}
                @if (session('error_melding'))
                    <div class="alert alert-danger auto-dismiss">
                        {{ session('error_melding') }}
                    </div>
                @endif
                

                <form method="POST" action="{{ route('product.update', $product->Id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row align-items-center mb-4 mt-4">
                        <label for="houdbaarheidsdatum" class="col-12 col-md-3 col-form-label fw-bold">
                            Houdbaarheidsdatum:
                        </label>
                        
                        <div class="col-12 col-md-5">
                            {{-- Eis 9: Client-side validatie (required) --}}
                            <input type="date" 
                                   class="form-control" 
                                   id="houdbaarheidsdatum" 
                                   name="houdbaarheidsdatum" 
                                   required 
                                   value="{{ old('houdbaarheidsdatum', \Carbon\Carbon::parse($product->Houdbaarheidsdatum)->format('Y-m-d')) }}">

                            @if (session('error_detail'))
                                <div class="text-danger mt-2 auto-dismiss">
                                    {{ session('error_detail') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-md-row justify-content-between mt-5">
                        <button type="submit" class="btn btn-secondary mb-2 mb-md-0">
                            Wijzig Houdbaarheidsdatum
                        </button>
                        
                        <div class="d-flex gap-2">
                            <a href="{{ $overzichtUrl }}" class="btn btn-primary">Terug</a>
                            <a href="{{ route('home') }}" class="btn btn-primary">Home</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const meldingen = document.querySelectorAll('.auto-dismiss');

            if (meldingen.length > 0) {
                setTimeout(function() {
                    meldingen.forEach(function(melding) {
                        melding.style.display = 'none';
                    });
                }, 3000);
            }
        });
    </script>
@endsection
