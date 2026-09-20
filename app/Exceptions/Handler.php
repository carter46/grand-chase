<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
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
     * Never render HTTP error Blade views that call __()/translator.
     * Broken Hostinger package/config caches otherwise nest into FatalError.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $e)
    {
        try {
            if (! $this->container->bound('translator')) {
                return $this->renderPlain($request, $e);
            }

            return parent::render($request, $e);
        } catch (Throwable $renderError) {
            try {
                report($e);
            } catch (Throwable $ignored) {
                //
            }
            try {
                report($renderError);
            } catch (Throwable $ignored) {
                //
            }

            return $this->renderPlain($request, $e);
        }
    }

    /**
     * @param \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderHttpException(HttpExceptionInterface $e)
    {
        try {
            if (! $this->container->bound('translator')) {
                return $this->renderPlainStatus($e->getStatusCode(), $e->getMessage());
            }

            return parent::renderHttpException($e);
        } catch (Throwable $ignored) {
            return $this->renderPlainStatus($e->getStatusCode(), $e->getMessage());
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderPlain($request, Throwable $e)
    {
        $status = 500;
        if ($e instanceof HttpExceptionInterface) {
            $status = $e->getStatusCode();
        }

        $debug = false;
        try {
            $debug = (bool) config('app.debug');
        } catch (Throwable $ignored) {
            //
        }

        $message = $debug
            ? get_class($e) . ': ' . $e->getMessage()
            : ($status === 404 ? 'Not Found' : 'Server Error');

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'ok' => false,
                'error' => 'server_error',
                'message' => $message,
            ], $status);
        }

        return $this->renderPlainStatus($status, $message);
    }

    /**
     * @param int $status
     * @param string $message
     * @return \Illuminate\Http\Response
     */
    protected function renderPlainStatus($status, $message = '')
    {
        $status = (int) $status ?: 500;
        if ($message === '') {
            $message = $status === 404 ? 'Not Found' : 'Server Error';
        }
        $safe = htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return response(
            '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">'
            . '<meta name="viewport" content="width=device-width,initial-scale=1">'
            . '<title>' . $status . '</title></head>'
            . '<body style="margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;'
            . 'font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;background:#f8fafc;color:#0f172a;">'
            . '<div style="text-align:center;padding:24px;max-width:420px;">'
            . '<p style="margin:0 0 8px;font-size:48px;font-weight:700;color:#64748b;">' . $status . '</p>'
            . '<p style="margin:0;font-size:16px;line-height:1.5;color:#475569;">' . $safe . '</p>'
            . '</div></body></html>',
            $status,
            ['Content-Type' => 'text/html; charset=UTF-8']
        );
    }
}
