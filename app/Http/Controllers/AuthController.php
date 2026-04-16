<?php

namespace App\Http\Controllers;

use App\Models\Gebruiker;
use App\Models\Persoon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:Gebruiker,Gebruikersnaam'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $fullName = trim($validated['name']);
        $nameParts = preg_split('/\s+/', $fullName) ?: [];
        $voornaam = $nameParts[0] ?? $fullName;
        $achternaam = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : $voornaam;

        $gebruiker = DB::transaction(function () use ($validated, $fullName, $voornaam, $achternaam) {
            $persoon = Persoon::create([
                'Voornaam' => $voornaam,
                'Achternaam' => $achternaam,
                'Geboortedatum' => '2000-01-01',
                'TypePersoon' => 'Vrijwilliger',
                'IsVertegenwoordiger' => 0,
                'IsActief' => 1,
            ]);

            return Gebruiker::create([
                'PersoonId' => $persoon->Id,
                'InlogNaam' => $fullName,
                'Gebruikersnaam' => $validated['email'],
                'Wachtwoord' => Hash::make($validated['password']),
                'IsIngelogd' => 1,
                'Ingelogd' => now(),
                'IsActief' => 1,
            ]);
        });

        Auth::login($gebruiker);
        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('status', 'Welkom! Je account is aangemaakt.');
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $attempt = Auth::attempt([
            'Gebruikersnaam' => $credentials['email'],
            'password' => $credentials['password'],
            'IsActief' => 1,
        ], false);

        if (! $attempt) {
            throw ValidationException::withMessages([
                'email' => 'Deze combinatie van e-mail en wachtwoord klopt niet.',
            ]);
        }

        /** @var Gebruiker $gebruiker */
        $gebruiker = Auth::user();
        $gebruiker->forceFill([
            'IsIngelogd' => 1,
            'Ingelogd' => now(),
            'Uitgelogd' => null,
        ])->save();

        $request->session()->regenerate();

        return redirect()
            ->intended(route('dashboard', absolute: false))
            ->with('status', 'Je bent succesvol ingelogd.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user instanceof Gebruiker) {
            $user->forceFill([
                'IsIngelogd' => 0,
                'Uitgelogd' => now(),
            ])->save();
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with('status', 'Je bent uitgelogd. Tot snel!');
    }
}
