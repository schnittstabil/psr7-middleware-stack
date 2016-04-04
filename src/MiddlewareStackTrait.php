<?php

namespace Schnittstabil\Psr7\MiddlewareStack;

/**
 * Implentations of stack operations.
 */
trait MiddlewareStackTrait
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
        $oldStack = $this->middlewareStack;

        if ($oldStack === null) {
            $this->middlewareStack = $newTopMiddleware;

            return $this;
        }

        $this->middlewareStack = function ($request, $response, callable $next) use ($oldStack, $newTopMiddleware) {
            return $newTopMiddleware($request, $response, function ($req, $res) use ($next, $oldStack) {
                return $oldStack($req, $res, $next);
            });
        };

        return $this;
    }
}
