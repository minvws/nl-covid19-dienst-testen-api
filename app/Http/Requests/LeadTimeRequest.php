<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class LeadTimeRequest extends PayloadRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'Aanbieder' => ['required', 'string'],
            'TeststraatID' => ['required', 'string'],
            'Datum' => ['required', 'string', 'date_format:Y-m-d', 'before_or_equal:now'],
            // TODO: Add test types from Value Sets end point
            'Testtype' => ['required', 'string', Rule::in(['PCR', 'Antigeen', 'Antistoffen'])],
            'GemTijdIdentificatieUitslag' => ['required', 'integer', 'min:0'],
            'GemTijdIdentificatieEmail' => ['required', 'integer', 'min:0'],
        ];
    }
}
