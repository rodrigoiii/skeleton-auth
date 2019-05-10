<?php

namespace App\Middlewares\Auth;

use Core\BaseMiddleware;
use Core\Log;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\NotFoundException;

class XhrMiddleware extends BaseMiddleware
{
    /**
     * Block not ajax request.
     *
     * @param  Request $request
     * @param  Response $response
     * @param  callable $next
     * @return callable
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        if (!$request->isXhr())
        {
            Log::error("Error: Request is not xhr");
            throw new NotFoundException($request, $response);
        }

        return $next($request, $response);
    }
}
