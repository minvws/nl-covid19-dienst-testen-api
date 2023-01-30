<?php

declare(strict_types=1);

use App\Http\Requests\TestRealisationRequest;
use App\Services\CoronaCheck\ValueSetsServiceMock;

it('validates test realisation request successful', function ($data) {
    $request = new TestRealisationRequest();

    $validator = \Illuminate\Support\Facades\Validator::make($data, $request->rules(new ValueSetsServiceMock()));

    expect($validator->passes())->toBeTrue()
        ->and($validator->errors()->all())->toBeEmpty();
})->with('TestRealisations');

dataset('TestRealisations', static function () {
    foreach (range(1, 5) as $ignored) {
        yield fn() => getTestRealisationData();
    }
});
