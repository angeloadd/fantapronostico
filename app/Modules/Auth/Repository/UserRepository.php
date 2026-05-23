<?php

declare(strict_types=1);

namespace App\Modules\Auth\Repository;

use App\Modules\Auth\Exception\NoAuthenticatedUserFound;
use App\Modules\Auth\Models\User;
use App\Service\RequestProvider\RequestProviderServiceInterface;
use Illuminate\Contracts\Auth\StatefulGuard;

final readonly class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private RequestProviderServiceInterface $requestProviderService,
        private StatefulGuard $auth
    ) {}

    public function getAuthenticatedUser(): User
    {
        $user = $this->requestProviderService->request()->user() ?? $this->auth->user();

        if (!$user instanceof User) {
            throw NoAuthenticatedUserFound::create();
        }

        return $user;
    }
}
