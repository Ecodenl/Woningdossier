<?php
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Laravel\Fortify\Http\Controllers\ConfirmablePasswordController;
use Laravel\Fortify\Http\Controllers\ConfirmedPasswordStatusController;
use App\Http\Controllers\Cooperation\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Cooperation\Auth\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController;
use Laravel\Fortify\Http\Controllers\TwoFactorSecretKeyController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController;
use Laravel\Fortify\Http\Controllers\EmailVerificationPromptController;
use Laravel\Fortify\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\Cooperation\Auth\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\ConfirmedTwoFactorAuthenticationController;

// Fortify auth routes start
Route::get('/register', [RegisteredUserController::class, 'index'])
    ->middleware(['guest:'.config('fortify.guard')])
    ->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware(['guest:'.config('fortify.guard')]);

Route::get('check-existing-mail', [RegisteredUserController::class, 'checkExistingEmail'])->name('check-existing-email');

Route::as('auth.')->group(function () {
    $limiter = config('fortify.limiters.login');
    $guard = config('fortify.guard');
    $verificationLimiter = config('fortify.limiters.verification', '6,1');

    Route::get('/email/verify', [EmailVerificationPromptController::class, '__invoke'])
        ->middleware([config('fortify.auth_middleware', 'auth').':'.$guard])
        ->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware([config('fortify.auth_middleware', 'auth').':'.$guard, 'signed', 'throttle:'.$verificationLimiter])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware([config('fortify.auth_middleware', 'auth').':'.$guard, 'throttle:'.$verificationLimiter])
        ->name('verification.send');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->middleware(['guest:' . $guard])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware(array_filter([
        'guest:' . $guard, $limiter ? 'throttle:' . $limiter : null,
    ]))->name('login.submit');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('password/request', [PasswordResetLinkController::class, 'create'])->middleware(['guest:' . $guard])
        ->name('password.request.index');
    Route::post('password/request', [PasswordResetLinkController::class, 'store'])->middleware(['guest:' . $guard])
        ->name('password.request.store');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->middleware(['guest:' . $guard])
        ->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->middleware(['guest:' . $guard])
        ->name('password.update');

    // Two Factor Authentication...
    $enableViews = config('fortify.views', true);

    // Authentication...
    if ($enableViews) {
        Route::get('/login', [AuthenticatedSessionController::class, 'create'])
            ->middleware(['guest:'.config('fortify.guard')])
            ->name('login');

        Route::get('/user/confirm-password', [ConfirmablePasswordController::class, 'show'])
            ->name('password.confirm-show')
            ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')]);
    }

    Route::get('/user/confirmed-password-status', [ConfirmedPasswordStatusController::class, 'show'])
        ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
        ->name('password.confirmation');

    Route::post('/user/confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
        ->name('password.confirm');


    $twoFactorLimiter = config('fortify.limiters.two-factor');
//    if ($enableViews) {
        Route::get('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'create'])
            ->middleware(['guest:'.config('fortify.guard')])
            ->name('two-factor.login');
//    }

    Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store'])
        ->middleware(array_filter([
            'guest:'.config('fortify.guard'),
            $twoFactorLimiter ? 'throttle:'.$twoFactorLimiter : null,
        ]))->name('two-factor.challenge');

    $twoFactorMiddleware = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
        ? [config('fortify.auth_middleware', 'auth').':'.config('fortify.guard'), 'password.confirm:cooperation.auth.password.confirm-show']
        : [config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')];



    Route::post('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])
        ->middleware($twoFactorMiddleware)
        ->name('two-factor.enable');

    Route::post('/user/confirmed-two-factor-authentication', [ConfirmedTwoFactorAuthenticationController::class, 'store'])
        ->middleware($twoFactorMiddleware)
        ->name('two-factor.confirm');

    Route::delete('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy'])
        ->middleware($twoFactorMiddleware)
        ->name('two-factor.disable');

    Route::get('/user/two-factor-qr-code', [TwoFactorQrCodeController::class, 'show'])
        ->middleware($twoFactorMiddleware)
        ->name('two-factor.qr-code');

    Route::get('/user/two-factor-secret-key', [TwoFactorSecretKeyController::class, 'show'])
        ->middleware($twoFactorMiddleware)
        ->name('two-factor.secret-key');

//    Route::get('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'index'])
//        ->middleware($twoFactorMiddleware)
//        ->name('two-factor.recovery-codes');

//    Route::post('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'store'])
//        ->middleware($twoFactorMiddleware);
});
// Fortify auth routes end
