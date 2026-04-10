<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Gebruiker;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVoedselpakketStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof Gebruiker;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in(['Uitgereikt', 'NietUitgereikt']),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Selecteer een status voor het voedselpakket.',
            'status.in' => 'De geselecteerde status is ongeldig.',
        ];
    }
}
