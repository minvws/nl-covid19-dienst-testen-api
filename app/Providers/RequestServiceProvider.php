<?php

declare(strict_types=1);

namespace App\Providers;

use App\Mixins\CmsSignedRequestMixin;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class RequestServiceProvider extends ServiceProvider
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
    }
}
