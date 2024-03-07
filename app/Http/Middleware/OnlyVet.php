<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use App\Http\Controllers\BaseController;

class OnlyVet extends BaseController
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (PersonalAccessToken::findToken($request->bearerToken())->tokenable_type != "App\\Models\\Vet") {
            $baseController = new BaseController();
            abort($baseController->sendError('unauthorized',['error'=>'Orvos bejelentkezés szükséges!'],401));
        }
        return $next($request);
    }
}
