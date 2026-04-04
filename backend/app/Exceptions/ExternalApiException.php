<?php

namespace App\Exceptions;

use RuntimeException;

class ExternalApiException extends RuntimeException
{
    public function __construct(string $service, ?\Throwable $previous = null)
    {
        parent::__construct("External API unavailable: {$service}", 503, $previous);
    }
}
