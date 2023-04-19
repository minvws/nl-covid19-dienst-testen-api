<?php

// phpcs:disable PSR1.Files.SideEffects


declare(strict_types=1);

use App\Exceptions\CoronaCheckServiceException;
use App\Services\CoronaCheck\Service;
use GuzzleHttp\ClientInterface;
use Illuminate\Contracts\Cache\Repository;
use MinVWS\Crypto\Laravel\Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

it('creates a service with only a client', function () {
    $client = mock(ClientInterface::class)->expect();

    $service = new Service(
        client: $client,
    );

    expect($service)
        ->toBeInstanceOf(Service::class);
});

it('creates a service', function () {
    $client = mock(ClientInterface::class)->expect();
    $cache = mock(Repository::class)->expect();

    $service = new Service(
        client: $client,
        cacheRepository: $cache,
        signatureService: null,
        certificates: [],
        cacheTtl: 900,
    );

    expect($service)
        ->toBeInstanceOf(Service::class);
});

it('returns a cached array', function () {
    $client = mock(ClientInterface::class)->expect();
    $client->allows('request')->never();

    $cache = mock(Repository::class)->expect();
    $cache->expects('get')
        ->andReturns(['foo' => 'bar']);

    $cacheableService = new class (
        client: $client,
        cacheRepository: $cache,
        signatureService: null,
        certificates: [],
        cacheTtl: 900,
    ) extends Service {
        public const CACHE_KEY = 'test-cache-key';
    };

    expect($cacheableService)
        ->toBeInstanceOf(Service::class)
        ->fetch()->toBe(['foo' => 'bar']);
});


it('does not clear the cache', function () {
    $client = mock(ClientInterface::class)->expect();
    $client->allows('request')->never();

    $cache = mock(Repository::class)->expect();
    $cache->allows('forget')->never();

    $service = new Service(
        client: $client,
        cacheRepository: $cache,
        signatureService: null,
        certificates: [],
        cacheTtl: 900,
    );

    expect($service)
        ->toBeInstanceOf(Service::class)
        ->clearCache();
});

it('clears the cache', function () {
    $client = mock(ClientInterface::class)->expect();
    $client->allows('request')->never();

    $cache = mock(Repository::class)->expect();
    $cache->expects('forget')
        ->withArgs(['test-cache-key']);

    $cacheableService = new class (
        client: $client,
        cacheRepository: $cache,
        signatureService: null,
        certificates: [],
        cacheTtl: 900,
    ) extends Service {
        public const CACHE_KEY = 'test-cache-key';
    };

    expect($cacheableService)
        ->toBeInstanceOf(Service::class)
        ->clearCache();
});

it('fetches and returns content', function () {
    $cache = mock(Repository::class)->expect();
    $client = getMockClient(getMockClientMessageWithPayloadAndFakeSignature([
        'some' => 'content',
    ]));

    $service = new Service(
        client: $client,
        cacheRepository: $cache,
        signatureService: null,
        certificates: [],
        cacheTtl: 900,
    );

    expect($service)
        ->toBeInstanceOf(Service::class)
        ->fetch()->toBe([
            'some' => 'content',
        ]);
});


it('should not cache because there is no cache key', function () {
    $cache = mock(Repository::class)->expect();
    $cache->shouldNotReceive(['get', 'put']);
    $client = getMockClient(getMockClientMessageWithPayloadAndFakeSignature([
        'some' => 'content',
    ]));

    $service = new Service(
        client: $client,
        cacheRepository: $cache,
        signatureService: null,
        certificates: [],
        cacheTtl: 900,
    );

    expect($service)
        ->toBeInstanceOf(Service::class)
        ->fetch()->toBe([
            'some' => 'content',
        ]);
});

it('throws an exception when remote content does not contain signature', function () {
    $cache = mock(Repository::class)->expect();
    $client = getMockClient(getMockClientMessageWithPayloadAndMissingSignature([
        'some' => 'content',
    ]));

    $service = new Service(
        client: $client,
        cacheRepository: $cache,
        signatureService: null,
        certificates: [],
        cacheTtl: 900,
    );

    $service->fetch();
})->throws(CoronaCheckServiceException::class, "Remote content does not contain payload or signature");

it('throws an exception when remote content is not json', function () {
    $cache = mock(Repository::class)->expect();
    $client = getMockClient(getMockClientMessage('some content'));

    $service = new Service(
        client: $client,
        cacheRepository: $cache,
        signatureService: null,
        certificates: [],
        cacheTtl: 900,
    );

    $service->fetch();
})->throws(CoronaCheckServiceException::class, "Unable to fetch remote content or content is not json");

it('throws an exception when remote content payload is json but empty array as payload', function () {
    $cache = mock(Repository::class)->expect();
    $client = getMockClient(getMockClientMessageWithPayloadAndFakeSignature([]));

    $service = new Service(
        client: $client,
        cacheRepository: $cache,
        signatureService: null,
        certificates: [],
        cacheTtl: 900,
    );

    $service->fetch();
})->throws(CoronaCheckServiceException::class, "Remote content is not a valid JSON array");

it('throws an exception when remote content payload is base64 encoded but not json', function () {
    $cache = mock(Repository::class)->expect();
    $client = getMockClient(getMockClientMessageWithStringPayloadAndSignature(base64_encode('some content')));

    $service = new Service(
        client: $client,
        cacheRepository: $cache,
        signatureService: null,
        certificates: [],
        cacheTtl: 900,
    );

    $service->fetch();
})->throws(CoronaCheckServiceException::class, "Something went wrong with json decoding the payload");

it('fetches content, validates signature and returns content', function () {
    $signatureService = Factory::createSignatureCryptoService(
        certificatePath: base_path('tests/fixtures/certificates/app/app.pem'),
        certificateKeyPath: base_path('tests/fixtures/certificates/app/app.key'),
        certificateChain: base_path('tests/fixtures/certificates/app/ca.pem'),
        forceProcessSpawn: config('crypto.force_process_spawn'),
    );

    $content = [
        'some' => 'content',
    ];

    $cache = mock(Repository::class)->expect();
    $client = getMockClient(getMockClientMessageWithPayloadAndSignature(
        payload: $content,
        signature: $signatureService->sign(json_encode($content)),
    ));

    $service = new Service(
        client: $client,
        cacheRepository: $cache,
        signatureService: $signatureService,
        certificates: [],
        cacheTtl: 900,
    );

    expect($service)
        ->toBeInstanceOf(Service::class)
        ->fetch()->toBe([
            'some' => 'content',
        ]);
});

it('fetches content and doest not validates signature when wrong chain certificate provided', function () {
    $signatureService = Factory::createSignatureCryptoService(
        certificatePath: base_path('tests/fixtures/certificates/app/app.pem'),
        certificateKeyPath: base_path('tests/fixtures/certificates/app/app.key'),
        certificateChain: base_path('tests/fixtures/certificates/aanbieder-123/chain.pem'),
        forceProcessSpawn: config('crypto.force_process_spawn'),
    );

    $content = [
        'some' => 'content',
    ];

    $cache = mock(Repository::class)->expect();
    $client = getMockClient(getMockClientMessageWithPayloadAndSignature(
        payload: $content,
        signature: $signatureService->sign(json_encode($content)),
    ));

    $service = new Service(
        client: $client,
        cacheRepository: $cache,
        signatureService: $signatureService,
        certificates: [],
        cacheTtl: 900,
    );

    expect($service)
        ->toBeInstanceOf(Service::class)
        ->fetch();
})->throws(CoronaCheckServiceException::class, "Unable to validate remote content against any signature");

it('fetches content, validates signature, put content in cache and returns content', function () {
    $signatureService = Factory::createSignatureCryptoService(
        certificatePath: base_path('tests/fixtures/certificates/app/app.pem'),
        certificateKeyPath: base_path('tests/fixtures/certificates/app/app.key'),
        certificateChain: base_path('tests/fixtures/certificates/app/ca.pem'),
        forceProcessSpawn: config('crypto.force_process_spawn'),
    );

    $content = [
        'some' => 'content',
    ];

    $client = getMockClient(getMockClientMessageWithPayloadAndSignature(
        payload: $content,
        signature: $signatureService->sign(json_encode($content)),
    ));

    $cache = mock(Repository::class)->expect();
    $cache
        ->expects('get')
        ->andReturns(null)
        ->shouldReceive('put')
        ->once()
        ->with('test-cache-key', $content, 900);

    $cacheableService = new class (
        client: $client,
        cacheRepository: $cache,
        signatureService: null,
        certificates: [],
        cacheTtl: 900,
    ) extends Service {
        public const CACHE_KEY = 'test-cache-key';
    };

    expect($cacheableService)
        ->toBeInstanceOf(Service::class)
        ->fetch()->toBe([
            'some' => 'content',
        ]);
});

function getMockClient(StreamInterface $body): ClientInterface
{
    $response = mock(ResponseInterface::class)->expect();
    $response->expects('getBody')
        ->andReturns($body);

    $client = mock(ClientInterface::class)->expect();
    $client->expects('request')
        ->andReturns($response);

    return $client;
}

function getMockClientMessageWithStringPayloadAndSignature(string $payload): StreamInterface
{
    return getMockClientMessage(json_encode([
        'signature' => 'some-signature-that-is-not-verified',
        'payload' => $payload,
    ]));
}

function getMockClientMessageWithPayloadAndFakeSignature(array $payload): StreamInterface
{
    return getMockClientMessage(json_encode([
        'signature' => 'some-signature-that-is-not-verified',
        'payload' => base64_encode(json_encode($payload)),
    ]));
}

function getMockClientMessageWithPayloadAndSignature(array $payload, string $signature): StreamInterface
{
    return getMockClientMessage(json_encode([
        'payload' => base64_encode(json_encode($payload)),
        'signature' => $signature,
    ]));
}

function getMockClientMessageWithPayloadAndMissingSignature(array $payload): StreamInterface
{
    return getMockClientMessage(json_encode([
        'payload' => base64_encode(json_encode($payload)),
    ]));
}

function getMockClientMessage(string $content): StreamInterface
{
    $body = mock(StreamInterface::class)->expect();
    $body->expects('getContents')
        ->andReturns($content);

    return $body;
}
