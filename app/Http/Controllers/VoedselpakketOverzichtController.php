<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateVoedselpakketStatusRequest;
use App\Http\Requests\VoedselpakketFilterRequest;
use App\Models\Gebruiker;
use App\Services\VoedselpakketOverzichtService;
use DomainException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class VoedselpakketOverzichtController extends Controller
{
    public function __construct(
        private readonly VoedselpakketOverzichtService $overzichtService
    ) {}

    /**
     * Toon overzicht gezinnen met voedselpakketten.
     */
    public function index(VoedselpakketFilterRequest $request): View
    {
        $selectedEetwensId = $request->validated('eetwens_id');
        $selectedEetwensId = $selectedEetwensId !== null
            ? (int) $selectedEetwensId
            : null;

        try {
            $gebruiker = $this->haalBevoegdeGebruikerOp($request);

            $eetwensen = $this->overzichtService->getActieveEetwensen();
            $gezinnen = $this->overzichtService->getGezinnenMetVoedselpakketten($selectedEetwensId);

            $feedbackType = null;
            $feedbackMessage = null;

            if ($selectedEetwensId !== null && $gezinnen->isEmpty()) {
                $feedbackType = 'warning';
                $feedbackMessage = 'Er zijn geen gezinnen bekend die de geselecteerde eetwens hebben.';
            }

            Log::info('Overzicht voedselpakketten geladen.', [
                'gebruiker_id' => $gebruiker->Id,
                'eetwens_id' => $selectedEetwensId,
                'aantal_gezinnen' => $gezinnen->count(),
            ]);

            return view('voedselpakketten.index', [
                'eetwensen' => $eetwensen,
                'gezinnen' => $gezinnen,
                'selectedEetwensId' => $selectedEetwensId,
                'feedbackType' => $feedbackType,
                'feedbackMessage' => $feedbackMessage,
            ]);
        } catch (AuthorizationException $exception) {
            Log::warning('Toegang geweigerd op overzicht voedselpakketten.', [
                'gebruiker_id' => $request->user()?->Id,
                'reden' => $exception->getMessage(),
            ]);

            abort(403, $exception->getMessage());
        } catch (Throwable $exception) {
            Log::error('Fout bij ophalen overzicht voedselpakketten.', [
                'gebruiker_id' => $request->user()?->Id,
                'eetwens_id' => $selectedEetwensId,
                'error' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            return view('voedselpakketten.index', [
                'eetwensen' => collect(),
                'gezinnen' => collect(),
                'selectedEetwensId' => $selectedEetwensId,
                'feedbackType' => 'danger',
                'feedbackMessage' => 'Er is iets misgegaan bij het ophalen van het overzicht. Probeer het opnieuw.',
            ]);
        }
    }

    /**
     * Toon alle voedselpakketten van één gezin.
     */
    public function showGezin(Request $request, int $gezin): View
    {
        try {
            $gebruiker = $this->haalBevoegdeGebruikerOp($request);

            $gezinDetails = $this->overzichtService->getGezinDetails($gezin);

            if ($gezinDetails === null) {
                abort(404);
            }

            $voedselpakketten = $this->verrijkVoedselpakketten(
                $this->overzichtService->getVoedselpakkettenVoorGezin($gezin)
            );

            Log::info('Voedselpakket details per gezin geladen.', [
                'gebruiker_id' => $gebruiker->Id,
                'gezin_id' => $gezin,
                'aantal_voedselpakketten' => $voedselpakketten->count(),
            ]);

            return view('voedselpakketten.show', [
                'gezin' => $gezinDetails,
                'voedselpakketten' => $voedselpakketten,
            ]);
        } catch (AuthorizationException $exception) {
            Log::warning('Toegang geweigerd op voedselpakket gezinsoverzicht.', [
                'gebruiker_id' => $request->user()?->Id,
                'gezin_id' => $gezin,
                'reden' => $exception->getMessage(),
            ]);

            abort(403, $exception->getMessage());
        } catch (Throwable $exception) {
            Log::error('Fout bij ophalen gezinsoverzicht voedselpakketten.', [
                'gebruiker_id' => $request->user()?->Id,
                'gezin_id' => $gezin,
                'error' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            abort(500, 'Er ging iets mis bij het laden van de voedselpakketten.');
        }
    }

    /**
     * Toon het formulier voor statuswijziging van één voedselpakket.
     */
    public function editStatus(Request $request, int $voedselpakket): View
    {
        try {
            $gebruiker = $this->haalBevoegdeGebruikerOp($request);

            $pakket = $this->overzichtService->getVoedselpakketVoorStatusWijziging($voedselpakket);

            if ($pakket === null) {
                abort(404);
            }

            $isWijzigenGeblokkeerd = (int) ($pakket->IsNietMeerIngeschreven ?? 0) === 1
                || (string) ($pakket->Status ?? '') === $this->overzichtService->getStatusNietMeerIngeschreven();

            Log::info('Statusformulier voedselpakket geladen.', [
                'gebruiker_id' => $gebruiker->Id,
                'voedselpakket_id' => $voedselpakket,
                'is_geblokkeerd' => $isWijzigenGeblokkeerd,
            ]);

            return view('voedselpakketten.edit-status', [
                'pakket' => $pakket,
                'isWijzigenGeblokkeerd' => $isWijzigenGeblokkeerd,
                'statusOpties' => [
                    $this->overzichtService->getStatusNietUitgereikt() => 'Niet Uitgereikt',
                    $this->overzichtService->getStatusUitgereikt() => 'Uitgereikt',
                ],
                'geselecteerdeStatus' => $isWijzigenGeblokkeerd
                    ? $this->overzichtService->getStatusNietUitgereikt()
                    : (string) ($pakket->Status ?? $this->overzichtService->getStatusNietUitgereikt()),
            ]);
        } catch (AuthorizationException $exception) {
            Log::warning('Toegang geweigerd op statusformulier voedselpakket.', [
                'gebruiker_id' => $request->user()?->Id,
                'voedselpakket_id' => $voedselpakket,
                'reden' => $exception->getMessage(),
            ]);

            abort(403, $exception->getMessage());
        } catch (Throwable $exception) {
            Log::error('Fout bij ophalen statusformulier voedselpakket.', [
                'gebruiker_id' => $request->user()?->Id,
                'voedselpakket_id' => $voedselpakket,
                'error' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            abort(500, 'Er ging iets mis bij het laden van de statuspagina.');
        }
    }

    /**
     * Verwerk de statuswijziging van één voedselpakket.
     */
    public function updateStatus(UpdateVoedselpakketStatusRequest $request, int $voedselpakket): RedirectResponse
    {
        $nieuweStatus = (string) $request->validated('status');

        try {
            $gebruiker = $this->haalBevoegdeGebruikerOp($request);

            $pakket = $this->overzichtService->getVoedselpakketVoorStatusWijziging($voedselpakket);

            if ($pakket === null) {
                abort(404);
            }

            if ((string) $pakket->Status === $nieuweStatus) {
                return redirect()
                    ->route('voedselpakketten.status.edit', ['voedselpakket' => $voedselpakket])
                    ->with('voedselpakket_status_ongewijzigd', 'De status is niet gewijzigd.');
            }

            $this->overzichtService->wijzigVoedselpakketStatus($voedselpakket, $nieuweStatus);

            Log::info('Status voedselpakket gewijzigd.', [
                'gebruiker_id' => $gebruiker->Id,
                'voedselpakket_id' => $voedselpakket,
                'oude_status' => (string) $pakket->Status,
                'nieuwe_status' => $nieuweStatus,
            ]);

            return redirect()
                ->route('voedselpakketten.status.edit', ['voedselpakket' => $voedselpakket])
                ->with('voedselpakket_status_wijziging_gelukt', 'De wijziging is doorgevoerd');
        } catch (AuthorizationException $exception) {
            Log::warning('Toegang geweigerd op statusupdate voedselpakket.', [
                'gebruiker_id' => $request->user()?->Id,
                'voedselpakket_id' => $voedselpakket,
                'reden' => $exception->getMessage(),
            ]);

            abort(403, $exception->getMessage());
        } catch (DomainException $exception) {
            Log::warning('Statusupdate voedselpakket afgewezen op businessregel.', [
                'gebruiker_id' => $request->user()?->Id,
                'voedselpakket_id' => $voedselpakket,
                'reden' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('voedselpakketten.status.edit', ['voedselpakket' => $voedselpakket])
                ->with('voedselpakket_status_wijziging_mislukt', $exception->getMessage());
        } catch (Throwable $exception) {
            Log::error('Fout bij wijzigen status voedselpakket.', [
                'gebruiker_id' => $request->user()?->Id,
                'voedselpakket_id' => $voedselpakket,
                'nieuwe_status' => $nieuweStatus,
                'error' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            return redirect()
                ->route('voedselpakketten.status.edit', ['voedselpakket' => $voedselpakket])
                ->with(
                    'voedselpakket_status_wijziging_mislukt',
                    'Er ging iets onverwachts mis bij het wijzigen van de status.'
                );
        }
    }

    /**
     * Controleer of de ingelogde gebruiker dit onderdeel mag openen.
     *
     * @throws AuthorizationException
     */
    private function haalBevoegdeGebruikerOp(Request $request): Gebruiker
    {
        /** @var Gebruiker|null $gebruiker */
        $gebruiker = $request->user();

        if (! $gebruiker instanceof Gebruiker) {
            throw new AuthorizationException('Je moet ingelogd zijn.');
        }

        if (! $this->overzichtService->heeftToegangTotVoedselpakketOverzicht((int) $gebruiker->Id)) {
            throw new AuthorizationException(
                'Alleen bevoegde vrijwilligers of managers hebben toegang tot dit overzicht.'
            );
        }

        return $gebruiker;
    }

    /**
     * Verrijk data voor weergave in het pakketoverzicht.
     */
    private function verrijkVoedselpakketten(Collection $voedselpakketten): Collection
    {
        return $voedselpakketten->map(function (object $pakket): object {
            $pakket->StatusLabel = $this->maakStatusLabel((string) ($pakket->Status ?? ''));
            $pakket->DatumUitgifteLabel = $this->formatteerDatum($pakket->DatumUitgifte ?? null);
            $pakket->KanStatusWijzigen = (string) ($pakket->Status ?? '') !== $this->overzichtService->getStatusNietMeerIngeschreven();

            return $pakket;
        });
    }

    /**
     * Maak nette statuslabels voor de weergave.
     */
    private function maakStatusLabel(string $status): string
    {
        return match ($status) {
            'Uitgereikt' => 'Uitgereikt',
            'NietUitgereikt' => 'Niet Uitgereikt',
            'NietMeerIngeschreven' => 'Niet Meer Ingeschreven',
            default => $status !== '' ? $status : '-',
        };
    }

    /**
     * Formatteer datum voor tabelweergave.
     */
    private function formatteerDatum(mixed $datum): string
    {
        if ($datum === null || $datum === '') {
            return '-';
        }

        try {
            return Carbon::parse((string) $datum)->format('d-m-Y');
        } catch (Throwable) {
            return (string) $datum;
        }
    }
}
