<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        // Handle CSRF token mismatch (419 error) - Session expired
        if ($e instanceof TokenMismatchException) {
            // Clear the session
            session()->flush();
            
            return redirect()
                ->route('login')
                ->with('session_expired', 'Your session has expired. Please login again to continue.');
        }

        // Handle MethodNotAllowedException (e.g., GET request on POST-only routes)
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
            // Check if it's a logout request
            if (str_contains($request->getRequestUri(), 'logout')) {
                session()->flush();
                return redirect()->route('login');
            }
            
            // For other method not allowed errors, show a friendly message
            return redirect()
                ->back()
                ->withErrors(['error' => 'Invalid request method. Please try again.'])
                ->withInput();
        }

        return parent::render($request, $e);
    }
}
