<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ServerIpAddressMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $serverIpAddress = config('app.server_ip');

        $serverIpIsValid = (
            filled($serverIpAddress)
            && gethostbyname($_SERVER['SERVER_NAME']) === $serverIpAddress
        );

        abort_if (! $serverIpIsValid, 403, 'Access denied');

        return $next($request);
    }
}
