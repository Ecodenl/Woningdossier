<?php

namespace App\Exceptions;

use Throwable;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Helpers\RoleHelper;
use App\Models\Cooperation;
use App\Models\CooperationRedirect;
use App\Models\Role;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Exceptions\UnauthorizedException as SpatieUnauthorizedException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        // get them directly from the session itself
        // the HoomdossierSession can only be used on authenticated parts.
        $cooperationId = session()->get('cooperation', null);

        if (is_null($cooperationId)) {
            return redirect()->route('index');
        }

        $cooperation = Cooperation::find($cooperationId);
        if (! $cooperation instanceof Cooperation) {
            return redirect()->route('index');
        }

        return redirect()->route('cooperation.auth.login', compact('cooperation'));
    }

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @return void
     */
    public function report(Throwable $exception)
    {
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        // Handle the exception if the role in the session is not associated with the user itself.
        if ($exception instanceof RoleInSessionHasNoAssociationWithUser) {
            // try to obtain a role from the user.
            $role = Hoomdossier::user()->roles()->first();

            if ($role instanceof Role) {
                HoomdossierSession::setRole($role);

                return redirect(route('cooperation.home'));
            } else {
                Hoomdossier::user()->logout();

                return redirect()->route('cooperation.home');
            }
        }

        // Handle the exception if the user is not authorized / has the right roles
        if ($exception instanceof SpatieUnauthorizedException && HoomdossierSession::hasRole()) {
            // the role the user currently has in his session
            $authorizedRole = HoomdossierSession::getRole(true);

            return redirect(
                url(RoleHelper::getUrlByRoleName($authorizedRole->name))
            )->with('warning', __('default.messages.exceptions.no-right-roles'));
        }

        // The user is not authorized at all.
        if ($exception instanceof UnauthorizedException) {
            return redirect()->route('cooperation.home');
        }

        if ($exception instanceof ModelNotFoundException) {
            $cooperation = $request->route('cooperation');
            if (!empty($cooperation)) {
                Log::debug("cooperation is not empty ( = '" . $cooperation . "')");
                $redirect = CooperationRedirect::from($cooperation)->first();

                if ($redirect instanceof CooperationRedirect) {
                    Log::debug("Redirect to " . str_ireplace(
                            $cooperation,
                            $redirect->cooperation->slug,
                            $request->url()
                        ));
                    return redirect(
                        str_ireplace(
                            $cooperation,
                            $redirect->cooperation->slug,
                            $request->url()
                        )
                    );
                }
            }
        }

        return parent::render($request, $exception);
    }
}
