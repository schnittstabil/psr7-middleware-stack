<?php

namespace Schnittstabil\Psr7\MiddlewareStack;

/**
 * Immutable middleware stack.
 */
class MiddlewareStack implements MiddlewareStackInterface
{
    use CallableMiddlewareStackTrait;

    /**
     * Create new stack.
     *
     * @param callable $bottomMiddleware last middleware to be called
     */
    public function __construct(callable $bottomMiddleware = null)
    {
        $this->middlewareStack = $bottomMiddleware;
    }

    /**
     * Push a middleware onto the top of a new Stack instance.
     *
     * @param callable $newTopMiddleware the middleware to be pushed onto the top.
     *
     * @return static the new instance
     */
    public function add(callable $newTopMiddleware)
    {
        $clone = clone $this;

        return $clone->push($newTopMiddleware);
    }
}
