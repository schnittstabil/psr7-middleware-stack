<?php

namespace Schnittstabil\Psr7\Middleware;

/**
 * Immutable middleware stack.
 */
class Stack implements StackInterface
{
    use CallableStackTrait;

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

    /**
     * Create new stack.
     *
     * @param callable $bottomMiddleware last middleware to be called
     *
     * @return static a new stack instance
     */
    public static function create(callable $bottomMiddleware = null)
    {
        return new self($bottomMiddleware);
    }
}
