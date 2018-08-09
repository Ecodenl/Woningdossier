<?php

namespace App\Exceptions;

use App\Helpers\RoleHelper;
use App\Models\Cooperation;
use Exception;
use Illuminate\Auth\AuthenticationException;
use \Spatie\Permission\Exceptions\UnauthorizedException as SpatieUnauthorizedException;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Models\Role;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
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
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Illuminate\Auth\AuthenticationException  $exception
	 * @return \Illuminate\Http\Response
	 */
	protected function unauthenticated($request, AuthenticationException $exception)
	{
		if ($request->expectsJson()) {
			return response()->json(['error' => 'Unauthenticated.'], 401);
		}

			$cooperationId = $request->session()->get( 'cooperation' );
			if ( is_null( $cooperationId ) ) {
				return redirect()->route( 'index' );
			}
			$cooperation = Cooperation::find( $cooperationId );
			if ( ! $cooperation instanceof Cooperation ) {
				return redirect()->route( 'index' );
			}

			if($request->routeIs('cooperation.admin.*')){
				return redirect()->route('cooperation.admin.login', ['cooperation' => $cooperation]);
			}

			return redirect()->route( 'cooperation.login',
				compact( 'cooperation' ) );
	}

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
	public function report(Exception $exception)
	{
		if (app()->bound('sentry') && $this->shouldReport($exception)) {
			app('sentry')->captureException($exception);
		}

		parent::report($exception);
	}

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // Handle the exception if the user is not authorized / has the right roles

        if ($exception instanceof SpatieUnauthorizedException && session()->exists('role_id')) {

            $authorizedRole = Role::find(session('role_id'));

            return redirect(url(RoleHelper::getUrlByRoleName($authorizedRole->name)))->with('warning', __('default.messages.exceptions.no-right-roles'));
        }

        if ($exception instanceof UnauthorizedException) {
            return redirect(route('cooperation.tool.index'));
        }

        return parent::render($request, $exception);
    }
}
