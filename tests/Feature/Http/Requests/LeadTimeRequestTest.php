<?php

declare(strict_types=1);

use App\Http\Requests\LeadTimeRequest;
use App\Services\CoronaCheck\ValueSetsServiceMock;

it('validates lead time request successful', function ($data) {
    $request = new LeadTimeRequest();

    $validator = \Illuminate\Support\Facades\Validator::make($data, $request->rules(new ValueSetsServiceMock()));

    expect($validator->passes())->toBeTrue()
        ->and($validator->errors()->all())->toBeEmpty();
})->with('leadTimes');

dataset('leadTimes', static function () {
    foreach (range(1, 5) as $ignored) {
        yield fn() => getLeadTimeData();
    }
});
