<?php

namespace App\Exceptions;

use App\Services\DatabaseUnavailableResponder;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // MySQL sem responder (tipicamente o erro 1226: o alojamento limita
        // a 30 as ligacoes simultaneas). Em vez de um 500, serve-se a copia
        // guardada da pagina ou, em ultimo caso, a pagina de
        // indisponibilidade com 503. Devolver null deixa a excepcao seguir o
        // seu caminho normal, por isso os erros de programacao continuam a
        // dar 500 e a aparecer no log como tal.
        $this->renderable(function (Throwable $e, Request $request) {
            return app(DatabaseUnavailableResponder::class)->render($e, $request);
        });
    }
}
