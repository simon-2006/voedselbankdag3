<?php

namespace App\Http\Requests;

use App\Models\Gebruiker;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class VoedselpakketFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof Gebruiker;
    }

    public function rules(): array
    {
        if (! Schema::hasTable('Eetwens')) {
            return [
                'eetwens_id' => ['nullable', 'integer', 'min:1'],
            ];
        }

        return [
            'eetwens_id' => [
                'nullable',
                'integer',
                Rule::exists('Eetwens', 'Id')->where(static function ($query) {
                    $query->where('IsActief', 1);
                }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'eetwens_id.integer' => 'De geselecteerde eetwens is ongeldig.',
            'eetwens_id.exists' => 'De gekozen eetwens bestaat niet of is niet actief.',
        ];
    }
}
