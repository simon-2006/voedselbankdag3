<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoedselpakketFilterRequest;
use App\Models\Gebruiker;
use App\Services\VoedselpakketOverzichtService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Throwable;

class VoedselpakketOverzichtController extends Controller
{
    public function __construct(private readonly VoedselpakketOverzichtService $overzichtService)
    {
        // Dependency Injection houdt controller dun en volgt de MVC-structuur.
    }

    public function index(VoedselpakketFilterRequest $request): View
    {
        /** @var Gebruiker|null $gebruiker */
        $gebruiker = $request->user();
        $selectedEetwensId = $request->validated('eetwens_id');
        $selectedEetwensId = $selectedEetwensId !== null ? (int) $selectedEetwensId : null;

        try {
            if (! $gebruiker instanceof Gebruiker) {
                throw new AuthorizationException('Je moet ingelogd zijn.');
            }

            if (! $this->overzichtService->isManager((int) $gebruiker->Id)) {
                throw new AuthorizationException('Alleen managers hebben toegang tot dit overzicht.');
            }

            $eetwensen = $this->overzichtService->getActieveEetwensen();
            $gezinnen = $this->overzichtService->getGezinnenMetVoedselpakketten($selectedEetwensId);

            $feedbackType = null;
            $feedbackMessage = null;

            if ($selectedEetwensId !== null && $gezinnen->isEmpty()) {
                $feedbackType = 'warning';
                $feedbackMessage = 'Er zijn geen gezinnen bekent die de geselecteerde eetwens hebben';
            }

            Log::channel('voedselpakket')->info('Overzicht voedselpakketten geladen.', [
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
            Log::channel('voedselpakket')->warning('Toegang geweigerd op overzicht voedselpakketten.', [
                'gebruiker_id' => $gebruiker?->Id,
                'reden' => $exception->getMessage(),
            ]);

            abort(403, $exception->getMessage());
        } catch (Throwable $exception) {
            Log::channel('voedselpakket')->error('Fout bij ophalen overzicht voedselpakketten.', [
                'gebruiker_id' => $gebruiker?->Id,
                'eetwens_id' => $selectedEetwensId,
                'error' => $exception->getMessage(),
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
}
