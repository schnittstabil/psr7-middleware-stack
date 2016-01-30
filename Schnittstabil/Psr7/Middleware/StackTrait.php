<?php

namespace Schnittstabil\Psr7\Middleware;

/**
 * Implentations of stack operations.
 */
trait StackTrait
{
    protected $middlewareStack;

    /**
     * Push a middleware onto the top of this stack.
     *
     * @param callable $newTopMiddleware the middleware to be pushed onto the top.
     *
     * @return static $this
     */
    protected function push(callable $newTopMiddleware)
    {
        $oldMiddlewareStack = $this->middlewareStack;

        if ($oldMiddlewareStack === null) {
            $this->middlewareStack = $newTopMiddleware;

            return $this;
        }

        $this->middlewareStack = function ($request, $response, callable $next) use ($oldMiddlewareStack, $newTopMiddleware) {
            return $newTopMiddleware($request, $response, function ($req, $res) use ($next, $oldMiddlewareStack) {
                return $oldMiddlewareStack($req, $res, $next);
            });
        };

        return $this;
    }
}
