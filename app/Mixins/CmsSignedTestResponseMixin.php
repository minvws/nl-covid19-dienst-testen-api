<?php

declare(strict_types=1);

namespace App\Mixins;

use Closure;
use Illuminate\Testing\Assert;
use Illuminate\Testing\AssertableJsonString;
use Illuminate\Testing\TestResponse;
use MinVWS\Crypto\Laravel\Factory;
use MinVWS\Crypto\Laravel\Service\Signature\SignatureVerifyConfig;

/**
 * This class is only used to get mixed into \Illuminate\Testing\TestResponse
 *
 * @mixin TestResponse
 */
class CmsSignedTestResponseMixin
{
    /**
     * @phpstan-ignore-next-line (Dont know array content)
     * @return Closure(array $path): TestResponse
     */
    public function assertPayloadPath(): Closure
    {
        return function (array $path): TestResponse {
            /** @var TestResponse $this */
            $rawPayload = $this->json('payload');
            if (!is_string($rawPayload)) {
                $rawPayload = '';
            }

            (new AssertableJsonString(base64_decode($rawPayload)))
                ->assertSubset($path);

            return $this;
        };
    }

    /**
     * @return Closure(string $certPath, string $chainPath): TestResponse
     */
    public function assertSignedWith(): Closure
    {
        return function (string $certPath, ?string $chainPath): TestResponse {

            /** @var TestResponse $this */
            $payload = $this->json('payload');
            if (!is_string($payload)) {
                $payload = '';
            }

            $signature = $this->json('signature');
            if (!is_string($signature)) {
                $signature = '';
            }

            $config = (new SignatureVerifyConfig())
                ->setNoVerify(true)
            ;

            $signatureService = Factory::createSignatureCryptoService(
                certificateChain: $chainPath,
                forceProcessSpawn: (bool) config('crypto.force_process_spawn'),
            );

            $valid = $signatureService->verify(
                signedPayload: $signature,
                content: base64_decode($payload),
                detachedCertificate: (string) file_get_contents($certPath),
                verifyConfig: $config,
            );

            Assert::assertTrue($valid);

            return $this;
        };
    }
}
