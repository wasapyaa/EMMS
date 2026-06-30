<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganizerSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('organizer_id')) {
            return redirect('/login')->with('error', 'Session expired. Please log in again.');
        }

        return $next($request);
    }
}
