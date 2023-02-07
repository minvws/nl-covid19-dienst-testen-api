<?php

declare(strict_types=1);

use App\Http\Requests\TestResultsRequest;
use App\Services\CoronaCheck\ValueSetsServiceMock;

it('validates test results request successful', function ($data) {
    $request = new TestResultsRequest();

    $validator = \Illuminate\Support\Facades\Validator::make($data, $request->rules(new ValueSetsServiceMock()));

    expect($validator->passes())->toBeTrue()
        ->and($validator->errors()->all())->toBeEmpty();
})->with('TestResults');

dataset('TestResults', static function () {
    foreach (range(1, 5) as $ignored) {
        yield fn() => getTestResultsData();
    }
});
