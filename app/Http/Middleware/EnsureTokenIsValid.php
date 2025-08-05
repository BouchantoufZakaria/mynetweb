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
        $access_token = $request->bearerToken();
        if (!$access_token) {
            return response()->json(['message' => 'You are Not Authorized to do that ... xd '], 401);
        }
        $user = \App\Models\User::where('access_token', $access_token)->first();
        if (!$user) {
            return response()->json(['message' => 'Invalid access token'], 401);
        }
        // Optionally, you can set the user in the request for further use
        return $next($request);
    }


}
