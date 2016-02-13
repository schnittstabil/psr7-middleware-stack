<?php

namespace Schnittstabil\Psr7\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Stack Tests.
 */
class StackTest extends \PHPUnit_Framework_TestCase
{
    protected static $bounce;

    public static function setUpBeforeClass()
    {
        self::$bounce = function (RequestInterface $request, ResponseInterface $response) {
            return $response;
        };
    }

    public static function tearDownAfterClass()
    {
        self::$bounce = null;
    }

    public function testEmptyStackShouldBeMiddleware()
    {
        $expected = $this->getMock(ResponseInterface::class);

        $sut = (new Stack());

        $reqDummy = $this->getMock(RequestInterface::class);
        $resDummy = $this->getMock(ResponseInterface::class);
        $next = function (RequestInterface $request, ResponseInterface $response) use ($expected) {
            return $expected;
        };

        $this->assertSame($expected, $sut($reqDummy, $resDummy, $next));
    }

    public function testStackShouldBeMiddleware()
    {
        $expected = $this->getMock(ResponseInterface::class);

        $sut = (new Stack())->add(
            function (RequestInterface $request, ResponseInterface $response, callable $next) use ($expected) {
                return $expected;
            }
        );

        $reqDummy = $this->getMock(RequestInterface::class);
        $resDummy = $this->getMock(ResponseInterface::class);
        $nextDummy = function () {
        };

        $this->assertSame($expected, $sut($reqDummy, $resDummy, $nextDummy));
    }

    public function testStackShouldRespectOrder()
    {
        $sut = (new Stack())->add(
            function (RequestInterface $request, ResponseInterface $response, callable $next) {
                $response->getBody()->write('3rd');

                return $next($request, $response);
            }
        )->add(
            function (RequestInterface $request, ResponseInterface $response, callable $next) {
                $response->getBody()->write('2nd');

                return $next($request, $response);
            }
        )->add(
            function (RequestInterface $request, ResponseInterface $response, callable $next) {
                $response->getBody()->write('1st');

                return $next($request, $response);
            }
        );

        $req = $this->getMock(RequestInterface::class);
        $res = $this->getMock(ResponseInterface::class);
        $stream = $this->getMockForAbstractClass(StreamInterface::class);

        $res->expects($this->exactly(3))
            ->method('getBody')
            ->willReturn($stream);

        $stream->expects($this->exactly(3))
            ->method('write')
            ->withConsecutive(
                array($this->equalTo('1st')),
                array($this->equalTo('2nd')),
                array($this->equalTo('3rd'))
            );

        $this->assertSame($res, $sut($req, $res, self::$bounce));
    }

    public function testStackShouldBeStackable()
    {
        $trd = (new Stack())->add(
            function (RequestInterface $request, ResponseInterface $response, callable $next) {
                $response->getBody()->write('3rd');

                return $next($request, $response);
            }
        );

        $snd = (new Stack())->add(
            function (RequestInterface $request, ResponseInterface $response, callable $next) {
                $response->getBody()->write('2nd');

                return $next($request, $response);
            }
        );

        $fst = (new Stack())->add(
            function (RequestInterface $request, ResponseInterface $response, callable $next) {
                $response->getBody()->write('1st');

                return $next($request, $response);
            }
        );

        $sut = (new Stack())
            ->add($trd)
            ->add($snd)
            ->add($fst);

        $req = $this->getMock(RequestInterface::class);
        $res = $this->getMock(ResponseInterface::class);
        $stream = $this->getMockForAbstractClass(StreamInterface::class);

        $res->expects($this->exactly(3))
            ->method('getBody')
            ->willReturn($stream);

        $stream->expects($this->exactly(3))
            ->method('write')
            ->withConsecutive(
                array($this->equalTo('1st')),
                array($this->equalTo('2nd')),
                array($this->equalTo('3rd'))
            );

        $this->assertSame($res, $sut($req, $res, self::$bounce));
    }

    public function testStackShouldBeImmutable()
    {
        $trd = (new Stack())->add(
            function (RequestInterface $request, ResponseInterface $response, callable $next) {
                $response->getBody()->write('3rd');

                return $next($request, $response);
            }
        );

        $snd = (new Stack())->add(
            function (RequestInterface $request, ResponseInterface $response, callable $next) {
                $response->getBody()->write('2nd');

                return $next($request, $response);
            }
        );

        $fst = (new Stack())->add(
            function (RequestInterface $request, ResponseInterface $response, callable $next) {
                $response->getBody()->write('1st');

                return $next($request, $response);
            }
        );

        $sut = (new Stack())->add($trd)->add($snd);

        $sut->add($fst);

        $req = $this->getMock(RequestInterface::class);
        $res = $this->getMock(ResponseInterface::class);
        $stream = $this->getMockForAbstractClass(StreamInterface::class);

        $res->expects($this->exactly(2))
            ->method('getBody')
            ->willReturn($stream);

        $stream->expects($this->exactly(2))
            ->method('write')
            ->withConsecutive(
                array($this->equalTo('2nd')),
                array($this->equalTo('3rd'))
            );

        $this->assertSame($res, $sut($req, $res, self::$bounce));
    }
}
