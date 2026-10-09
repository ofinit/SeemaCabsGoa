<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // 419 (CSRF token mismatch, usually an expired session or a form left
        // open across a deploy): send the user back to the form with their
        // input and a clear message instead of a bare "Page Expired" screen.
        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() !== 419 || $request->expectsJson() || $request->is('api/*')) {
                return null;
            }

            return redirect()->back()
                ->withInput($request->except(['_token', 'password', 'current_password', 'old_password', 'new_password', 'confirm_password']))
                ->with('error', 'Your session expired, so the form was not saved. Please check the details and submit again.');
        });

        $this->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return null;
            }
            return response()->view('errors.404', [], 404);
        });
    }
}
