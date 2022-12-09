<?php

declare(strict_types=1);

use App\Http\Requests\LeadTimeRequest;

use function Pest\Faker\faker;

it('validates lead time request successful', function ($data) {
    $request = new LeadTimeRequest();

    $validator = \Illuminate\Support\Facades\Validator::make($data, $request->rules());

    expect($validator->passes())->toBeTrue()
        ->and($validator->errors()->all())->toBeEmpty();
})->with('leadTimes');

dataset('leadTimes', static function () {
    $faker = faker();

    foreach (range(1, 5) as $ignored) {
        yield fn() => [
            'Aanbieder' => $faker->company(),
            'Datum' => $faker->date(),
            'Testtype' => $faker->randomElement(['PCR', 'Antigeen', 'Antistoffen']),
            'TestenAfgenomen' => $faker->numberBetween(0, 1000000),
            'TestenMetResultaat' => $faker->numberBetween(0, 1000000),
            'TestenPositief' => $faker->numberBetween(0, 1000000),
            'TestenNegatief' => $faker->numberBetween(0, 1000000),
            'TestenOndefinieerbaar' => $faker->numberBetween(0, 1000000),
            'TestenAfwachtingResultaat' => $faker->numberBetween(0, 1000000),
            'TestenAfwachtingValidatie' => $faker->numberBetween(0, 1000000),
            'TestenZonderUitslag' => $faker->numberBetween(0, 1000000),
        ];
    }
});
