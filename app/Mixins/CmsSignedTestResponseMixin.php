<?php

declare(strict_types=1);

namespace App\Mixins;

use Closure;
use Illuminate\Testing\AssertableJsonString;
use Illuminate\Testing\TestResponse;

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
}
