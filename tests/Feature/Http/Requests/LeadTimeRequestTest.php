<?php

declare(strict_types=1);

use App\Http\Requests\LeadTimeRequest;

it('validates lead time request successful', function ($data) {
    $request = new LeadTimeRequest();

    $validator = \Illuminate\Support\Facades\Validator::make($data, $request->rules());

    expect($validator->passes())->toBeTrue()
        ->and($validator->errors()->all())->toBeEmpty();
})->with('leadTimes');

dataset('leadTimes', static function () {
    foreach (range(1, 5) as $ignored) {
        yield fn() => getLeadTimeData();
    }
});
