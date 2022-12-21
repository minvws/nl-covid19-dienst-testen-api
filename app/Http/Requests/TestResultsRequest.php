<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Exceptions\CoronaCheckServiceException;
use App\Services\CoronaCheck\ValueSetsService;
use Illuminate\Validation\Rule;

class TestResultsRequest extends PayloadRequest
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
    public function rules(ValueSetsService $valueSetsService): array
    {
        return [
            'Aanbieder' => ['required', 'string'],
            'Datum' => ['required', 'string', 'date_format:Y-m-d', 'before_or_equal:now'],
            'Testtype' => [
                'required',
                'string',
                Rule::in($valueSetsService->getCovid19LabTestManufacturerAndNameValues()),
            ],
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
