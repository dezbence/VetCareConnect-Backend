<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use App\Http\Controllers\BaseController;

class OnlyOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (PersonalAccessToken::findToken($request->bearerToken())->tokenable_type != "App\\Models\\Owner") {
            $baseController = new BaseController();
            abort($baseController->sendError('unauthorized',['error'=>'Gazda bejelentkezés szükséges!'],401));
        }
        return $next($request);
    }
}
