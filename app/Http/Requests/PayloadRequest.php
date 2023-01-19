<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Exceptions\InvalidCmsSignatureException;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @todo: check if whe can use generics and specify the type of the payload in the specific request class
 */
class PayloadRequest extends FormRequest
{
    /**
     * Get data to be validated from the request.
     *
     * @return array<mixed>
     * @throws InvalidCmsSignatureException
     */
    public function validationData(): array
    {
        return self::getPayload();
    }

    /**
     * @throws InvalidCmsSignatureException
     */
    public function getProvider(): string
    {
        $provider = $this->validationData()['Aanbieder'];
        if (!is_string($provider)) {
            return '';
        }

        return $provider;
    }

    /**
     * @return array<string, bool|float|int|string|null>
     */
    public function getSafePayload(): array
    {
        /** @var array<string, bool|float|int|string|null> $data */
        $data = $this->safe()->toArray();

        return $data;
    }
}
