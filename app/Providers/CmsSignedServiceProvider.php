<?php

declare(strict_types=1);

namespace App\Providers;

use App\Mixins\CmsSignedRequestMixin;
use App\Mixins\CmsSignedTestResponseMixin;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\TestResponse;

class CmsSignedServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    /**
     * @throws \ReflectionException
     */
    public function boot(): void
    {
        Request::mixin(new CmsSignedRequestMixin());
        TestResponse::mixin(new CmsSignedTestResponseMixin());
    }
}
