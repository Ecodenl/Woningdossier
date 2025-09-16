<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Responses\LoginResponse;
use App\Responses\LogoutResponse;
use App\Responses\PasswordResetResponse;
use App\Responses\RegisterResponse;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Responses\FailedTwoFactorLoginResponse;
use Laravel\Fortify\Http\Responses\LoginResponse as FortifyLoginResponse;
use Laravel\Fortify\Http\Responses\LogoutResponse as FortifyLogoutResponse;
use Laravel\Fortify\Http\Responses\PasswordResetResponse as FortifyPasswordResetResponse;
use Laravel\Fortify\Http\Responses\RegisterResponse as FortifyRegisterResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
         Fortify::ignoreRoutes();

        $this->app->bind(
            FortifyRegisterResponse::class,
            RegisterResponse::class,
        );
        $this->app->bind(
            FortifyPasswordResetResponse::class,
            PasswordResetResponse::class
        );
        $this->app->bind(
            FortifyLoginResponse::class,
            LoginResponse::class
        );
        $this->app->bind(
            FortifyLogoutResponse::class,
            LogoutResponse::class
        );

        $this->app->bind(
            FailedTwoFactorLoginResponse::class,
            \App\Responses\FailedTwoFactorLoginResponse::class
        );

        $this->app->bind(
            TwoFactorLoginResponse::class,
            \App\Responses\TwoFactorLoginResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);


        $this->app->singleton(
            RedirectIfTwoFactorAuthenticatable::class,
            \App\Actions\Fortify\RedirectIfTwoFactorAuthenticatable::class
        );

        RateLimiter::for('login', function (Request $request) {
            return app()->isLocal() ? Limit::none() : Limit::perMinute(50)->by($request->email . $request->ip());
        });

        Fortify::registerView(function () {
            return view('cooperation.auth.register');
        });
        Fortify::verifyEmailView(function () {
            return view('cooperation.auth.verify');
        });
        Fortify::loginView(function () {
            return view('cooperation.auth.login');
        });
        Fortify::confirmPasswordView(function () {
            return view('cooperation.auth.confirm-password');
        });
        Fortify::requestPasswordResetLinkView(function () {
            return view('cooperation.auth.passwords.request.index');
        });
        Fortify::resetPasswordView(function (Request $request) {
            $token = $request->route('token');
            return view('cooperation.auth.passwords.reset.show', compact('token'));
        });

        Fortify::twoFactorChallengeView(function () {
            return view('cooperation.auth.two-factor-challenge');
        });
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return route('cooperation.auth.password.reset', compact('token'));
        });
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
