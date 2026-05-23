<?php

declare(strict_types=1);

namespace App\Modules\Auth;

use App\Modules\Auth\Fortify\CreateNewUser;
use App\Modules\Auth\Fortify\ResetUserPassword;
use App\Modules\Auth\Fortify\UpdateUserPassword;
use App\Modules\Auth\Fortify\UpdateUserProfileInformation;
use App\Modules\Auth\Repository\UserRepository;
use App\Modules\Auth\Repository\UserRepositoryInterface;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Fortify;

final class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadViewsFrom((__DIR__.'/Views'), 'auth');

        $this->app->extend(
            RegisterResponse::class,
            static fn () => new class() implements RegisterResponse
            {
                public function toResponse($request): RedirectResponse
                {
                    return redirect('api/email/verify');
                }
            }
        );

        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    public function boot(): void
    {
        Blade::anonymousComponentPath(__DIR__.'/Views', 'auth');

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', static function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower((string) $request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        Fortify::loginView(static fn () => view('auth::index', ['pageName' => 'login']));
        Fortify::registerView(static fn () => view('auth::index', ['pageName' => 'register']));
        Fortify::requestPasswordResetLinkView(static fn () => view('auth::index', ['pageName' => 'request-password-reset']));
        Fortify::resetPasswordView(static fn () => view('auth::index', ['pageName' => 'reset-password']));
        Fortify::verifyEmailView(static fn () => view('auth::index', ['pageName' => 'verify-email']));
    }
}
