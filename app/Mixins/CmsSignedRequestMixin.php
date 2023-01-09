<?php

declare(strict_types=1);

namespace App\Mixins;

use App\Exceptions\InvalidCmsRequestException;
use Closure;
use Illuminate\Http\Request;
use JsonException;

/**
 * This class is only used to get mixed into \Illuminate\Http\Request
 *
 * @mixin Request
 */
class CmsSignedRequestMixin
{
    /**
     * @return Closure(): array<mixed>
     */
    public function getPayload(): Closure
    {
        return function (): array {
            /**
             * @var Request $this
             * @var string $jsonPayload
             */
            $jsonPayload = $this->getJsonPayload();
            try {
                $payload = json_decode($jsonPayload, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                throw new InvalidCmsRequestException();
            }

            throw_if(!is_array($payload), InvalidCmsRequestException::class);

            return $payload;
        };
    }

    /**
     * @return Closure(): string
     */
    public function getSignature(): Closure
    {
        return function (): string {
            $signature = $this->json('signature');
            throw_if(empty($signature) || !is_string($signature), new InvalidCmsRequestException());

            return $signature;
        };
    }

    /**
     * @return Closure(): string
     */
    public function getProvider(): Closure
    {
        return function (): string {
            /** @var $this Request  */
            $payload = $this->getPayload();
            throw_if(empty($payload['Aanbieder']), new InvalidCmsRequestException());

            return $payload['Aanbieder'];
        };
    }

    /**
     * @return Closure(): string
     */
    public function getJsonPayload(): Closure
    {
        return function (): string {
            $payload = $this->json('payload');
            throw_if(empty($payload) || !is_string($payload), new InvalidCmsRequestException());

            return base64_decode($payload);
        };
    }
}
