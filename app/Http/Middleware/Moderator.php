<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

use App\Models\Enums\Role;

class Moderator
{

    public function handle(Request $request, Closure $next): Response
    {
        if ( Auth::check() && Auth::user()->role === Role::MODERATOR->value)
        {
            return $next($request);
        }
        abort(404);
    }
}
