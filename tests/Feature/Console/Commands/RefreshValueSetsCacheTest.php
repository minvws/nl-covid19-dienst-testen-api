<?php

declare(strict_types=1);

use App\Exceptions\CoronaCheckServiceException;
use App\Services\CoronaCheck\ValueSetsInterface;
use Mockery\MockInterface;
use Symfony\Component\Console\Command\Command;

it('works with mock service', function () {
    config()->set('corona-check.value_sets.url', '');
    $this->mock(ValueSetsInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('clearCache')
            ->shouldReceive('fetch')
            ->andReturn([
            'mock' => [],
            'mock2' => 1,
        ]);
    });
    $this->artisan('value-sets:refresh')
        ->expectsOutput('Cache cleared')
        ->expectsOutput('Fetch remote value sets')
        ->expectsOutput('Found value sets:')
        ->
        expectsTable(
            ['Key', 'Item count'],
            [
                ['mock', 0],
                ['mock2', 1],
            ]
        );
});

it('fails on invalid remote', function () {
    $this->mock(ValueSetsInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('clearCache')
            ->shouldReceive('fetch')
            ->andThrow(new CoronaCheckServiceException('invalid'));
    });

    $this->artisan('value-sets:refresh')
        ->expectsOutput('Cache cleared')
        ->expectsOutput('Fetch remote value sets')
        ->assertExitCode(Command::FAILURE);
});
