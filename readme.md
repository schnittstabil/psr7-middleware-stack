# Psr7\Middleware\Stack [![Build Status](https://travis-ci.org/schnittstabil/psr7-middleware-stack.svg?branch=master)](https://travis-ci.org/schnittstabil/psr7-middleware-stack) [![Coverage Status](https://coveralls.io/repos/github/schnittstabil/psr7-middleware-stack/badge.svg?branch=master)](https://coveralls.io/github/schnittstabil/psr7-middleware-stack?branch=master) [![Code Climate](https://codeclimate.com/github/schnittstabil/psr7-middleware-stack/badges/gpa.svg)](https://codeclimate.com/github/schnittstabil/psr7-middleware-stack)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/f82b6d49-fa8c-45a1-8d27-e02569fb4dab/big.png)](https://insight.sensiolabs.com/projects/f82b6d49-fa8c-45a1-8d27-e02569fb4dab)

> Stack PSR-7 middlewares in a reusable way.


## Install

```sh
$ composer require schnittstabil/psr7-middleware-stack
```


## Usage

```php
use Schnittstabil\Psr7\Middleware\ImmutableStack as MiddlewareStack;

$appMiddlewares = MiddlewareStack::create()
  ->add($someMiddleware4)
  ->add($someMiddleware3)
  ->add(
    function (RequestInterface $req, ResponseInterface $res, callable $next) {
      $res->getBody()->write('Greetings from the the 2nd middleware.');
      return $next($req, $res);
    }
  )
  ->add($someMiddleware1);

```


### Slim Example

Suppose we want to [minify](https://github.com/mrclay/minify) the response of some, but not all routes by [Oscar Otero's psr7-middlewares](https://github.com/oscarotero/psr7-middlewares) in a reusable way.


#### Install dependencies:

```sh
$ composer require slim/slim
$ composer require oscarotero/psr7-middlewares
$ composer require mrclay/minify
$ composer require willdurand/negotiation
```


#### `app.php`

```php
<?php
require __DIR__.'/vendor/autoload.php';

use Slim\App;
use Slim\Http\Stream;
use Psr7Middlewares\Middleware;
use Schnittstabil\Psr7\Middleware\ImmutableStack as MiddlewareStack;

// setup oscarotero/psr7-middlewares
Middleware::setStreamFactory(function ($file, $mode) {
    return new Stream(fopen($file, $mode));
});

$app = new App();

$app->getContainer()['minifyMiddleware'] = function ($c) {
    return MiddlewareStack::create()
        ->add(Middleware::Minify())
        ->add(Middleware::FormatNegotiator());
};

$app->get('/', function (RequestInterface $request, ResponseInterface $response) {
    $body = '<!-- comment --><h1>Hello world!</h1><!-- comment -->';

    return $response->write($body);
})->add('minifyMiddleware');

$app->run();
?>
```

## License

MIT © [Michael Mayer](http://schnittstabil.de)
