<?php

declare(strict_types=1);

namespace App\Enums;

enum EndpointType: string
{
    case LeadTime = 'lead-time';
    case TestRealisation = 'test-realisation';
    case TestResults = 'test-results';
}
