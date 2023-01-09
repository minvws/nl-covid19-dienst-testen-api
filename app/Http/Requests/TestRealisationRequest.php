<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Exceptions\CoronaCheckServiceException;
use App\Services\CoronaCheck\ValueSetsInterface;
use Illuminate\Validation\Rule;

class TestRealisationRequest extends PayloadRequest
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
     * @throws CoronaCheckServiceException
     */
    public function rules(ValueSetsInterface $valueSetsService): array
    {
        return [
            'Aanbieder' => ['required', 'string'],
            'TeststraatID' => ['required', 'string'],
            'Datum' => ['required', 'string', 'date_format:Y-m-d', 'before_or_equal:now'],
            'Uur' => ['required', 'string', 'date_format:H:i'],
            'Testtype' => [
                'required',
                'string',
                Rule::in($valueSetsService->getCovid19LabTestManufacturerAndNameValues()),
            ],
            'TestenGeboekt' => ['required', 'integer', 'min:0'],
            'TestenAfgenomen' => ['required', 'integer', 'min:0'],
            'TestenMetResultaat' => ['required', 'integer', 'min:0'],
            'TestenMetResultaatAsprakenportaal' => ['required', 'integer', 'min:0'],
            'TestenMetResultaatAdhoc' => ['required', 'integer', 'min:0'],
            'Hertesten' => ['required', 'integer', 'min:0'],
            'TestenNoShows' => ['required', 'integer', 'min:0'],
            'TestenAfwachtingResultaat' => ['required', 'integer', 'min:0'],
            'TestenAfwachtingValidatie' => ['required', 'integer', 'min:0'],
            'TestenZonderUitslag' => ['required', 'integer', 'min:0'],
        ];
    }
}
