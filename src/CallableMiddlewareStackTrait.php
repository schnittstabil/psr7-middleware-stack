<?php

namespace Schnittstabil\Psr7\MiddlewareStack;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Middleware __invoke implementation for middleware stacks.
 */
trait CallableMiddlewareStackTrait
{
    use MiddlewareStackTrait;

    /**
     * Invoke stacked middlewares.
     *
     * @param RequestInterface|mixed  $request  request object
     * @param ResponseInterface|mixed $response response object
     * @param callable                $next     next middleware
     *
     * @return ResponseInterface PSR7 response object
     */
    public function __invoke($request, $response, callable $next)
    {
        $middlewareStack = $this->middlewareStack;

        if ($middlewareStack === null) {
            return $next($request, $response);
        }

        return $middlewareStack($request, $response, $next);
    }
}
