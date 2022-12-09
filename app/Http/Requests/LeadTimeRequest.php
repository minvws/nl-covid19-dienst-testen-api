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
            'Datum' => ['required', 'string', 'date_format:Y-m-d'],
            // TODO: Add test types from Value Sets end point
            'Testtype' => ['required', 'string', Rule::in(['PCR', 'Antigeen', 'Antistoffen'])],
            'TestenAfgenomen' => ['required', 'integer', 'min:0'],
            'TestenMetResultaat' => ['required', 'integer', 'min:0'],
            'TestenPositief' => ['required', 'integer', 'min:0'],
            'TestenNegatief' => ['required', 'integer', 'min:0'],
            'TestenOndefinieerbaar' => ['required', 'integer', 'min:0'],
            'TestenAfwachtingResultaat' => ['required', 'integer', 'min:0'],
            'TestenAfwachtingValidatie' => ['required', 'integer', 'min:0'],
            'TestenZonderUitslag' => ['required', 'integer', 'min:0'],
        ];
    }
}
