<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

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
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Avoid nested 500s when the container cannot resolve translator/views
     * (common after a failed Hostinger pull / broken bootstrap/cache).
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $e)
    {
        try {
            return parent::render($request, $e);
        } catch (Throwable $renderError) {
            report($e);
            report($renderError);

            $debug = false;
            try {
                $debug = (bool) config('app.debug');
            } catch (Throwable $ignored) {
                //
            }

            $message = $debug
                ? get_class($e) . ': ' . $e->getMessage()
                : 'Server Error';

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'ok' => false,
                    'error' => 'server_error',
                    'message' => $message,
                ], 500);
            }

            $safe = htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

            return response(
                '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Server Error</title></head>'
                . '<body style="font-family:sans-serif;padding:2rem;">'
                . '<h1>Server Error</h1><p>' . $safe . '</p>'
                . '<p>If this persists after deploy, delete bootstrap/cache/*.php '
                . 'and storage/framework/views/*, then reload.</p>'
                . '</body></html>',
                500,
                ['Content-Type' => 'text/html; charset=UTF-8']
            );
        }
    }
}
