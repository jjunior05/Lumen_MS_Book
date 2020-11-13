<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class AuthenticateAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /**
         * Condição para verificar as possíveis chaves.
         * Se for verdadeiro, continuar com a requisição
         *
         */
        $validSecrets = explode(',', env('ACCEPTED_SECRETS'));
        $header = $request->header('Authorization');

        if (in_array($header, $validSecrets)) {
            return $next($request);
        }

        /**
         * Senão abortar a requisição.
         *
         */
        abort(Response::HTTP_UNAUTHORIZED);
    }
}
