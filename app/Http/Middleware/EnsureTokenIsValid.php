<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $access_token = $request->header('Authorization');
        if (!$access_token) {
            return response()->json(['error' => 'Access token is required'], 401);
        }
        $access_token = str_replace('Bearer ', '', $access_token);
        $user = \App\Models\User::where('access_token', $access_token)->first();
        if (!$user) {
            return response()->json(['error' => 'Invalid access token'], 401);
        }
        // Optionally, you can set the user in the request for further use
        return $next($request);
    }


}
