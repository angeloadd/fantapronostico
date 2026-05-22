<?php

declare(strict_types=1);

namespace App\Service\RequestProvider;

use Illuminate\Http\Request;

final readonly class RequestProviderService implements RequestProviderServiceInterface
{
    public function __construct(private Request $request) {}

    public function request(): Request
    {
        return $this->request;
    }
}
