<?php

namespace Schnittstabil\Psr7\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr7Middlewares\Middleware;
use Slim\App;
use Slim\Http\Stream;
use Schnittstabil\Psr7\MiddlewareStack\MiddlewareStack;

class StackUsageTest extends \PHPUnit_Framework_TestCase
{
    public function requestFactory($method = 'GET', $scriptName = '/index.php', $uri = '/')
    {
        $env = \Slim\Http\Environment::mock(
            [
                'SCRIPT_NAME' => $scriptName,
                'REQUEST_URI' => $uri,
                'REQUEST_METHOD' => $method,
                'HTTP_ACCEPT_ENCODING' => 'gzip,deflate',
                'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            ]
        );

        return new \Slim\Http\Request(
            $method,
            \Slim\Http\Uri::createFromEnvironment($env),
            \Slim\Http\Headers::createFromEnvironment($env),
            [],
            $env->all(),
            new \Slim\Http\RequestBody()
        );
    }

    public function testSlimUsage()
    {
        // --- usage start ---

        /*
         * Install dependencies:
         *
         *     composer require slim/slim
         *     composer require oscarotero/psr7-middlewares
         *     composer require mrclay/minify
         *     composer require willdurand/negotiation
         */

        // setup oscarotero/psr7-middlewares
        Middleware::setStreamFactory(function ($file, $mode) {
            return new Stream(fopen($file, $mode));
        });

        $app = new App();

        $app->getContainer()['minifyMiddleware'] = function ($c) {
            return (new MiddlewareStack())
                ->add(Middleware::Minify())
                ->add(Middleware::FormatNegotiator());
        };

        $app->get('/', function (RequestInterface $request, ResponseInterface $response) {
            $body = '<!-- comment --><h1>Hello world!</h1><!-- comment -->';

            return $response->write($body);
        })->add('minifyMiddleware');

        // --- usage end ---

        $req = $this->requestFactory();
        $res = $app($req, new \Slim\Http\Response());

        $this->assertEquals('<h1>Hello world!</h1>', (string) $res->getBody());
    }
}
