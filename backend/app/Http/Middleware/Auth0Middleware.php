<?php

namespace App\Http\Middleware;

use Auth0\SDK\Auth0;
use Auth0\SDK\Exception\InvalidTokenException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Auth0Middleware
{
    public function __construct(private readonly Auth0 $auth0) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        try {
            $this->auth0->decode($token, tokenType: \Auth0\SDK\Token::TYPE_TOKEN);
        } catch (InvalidTokenException $e) {
            return response()->json(['message' => 'Unauthorized', 'reason' => $e->getMessage()], 401);
        }

        return $next($request);
    }
}
