<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }

    protected function unauthenticated($request, array $guards){
        $baseController = new BaseController();
        abort($baseController->sendError('unauthorized',['error'=>'Bejelentkezés szükséges!'],401));
    }
}
