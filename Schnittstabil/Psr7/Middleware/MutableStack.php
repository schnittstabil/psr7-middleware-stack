<?php

namespace Schnittstabil\Psr7\Middleware;

/**
 * Mutable middleware stack.
 */
class MutableStack implements Stack
{
    use CallableStackTrait {
        push as public add;
    }

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
