<?php

declare(strict_types=1);

namespace App\Http\Requests;

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
     */
    public function validationData(): array
    {
        return self::getPayload();
    }
}
