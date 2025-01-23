<?php

declare(strict_types=1);

use Psr\Http\Message\RequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;

return function(App $app)
{
    $app->add(function (RequestInterface $request, RequestHandlerInterface $handler) {

        $response = $handler->handle($request);
        $host = 'https://'.$_ENV['HOST'];

        if ($request->getMethod() == 'OPTIONS') {
            return $response
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, DELETE, OPTIONS')
                ->withHeader('Access-Control-Allow-Origin', $host)
                ->withHeader('Access-Control-Allow-Credentials', 'true')
                ->withHeader('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Features, Token, withCredentials')
                ->withHeader('Content-Type', 'text/plain charset=UTF-8')
                ->withHeader('Content-Length', 0)->withStatus(204);
        } else {
            return $response
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, DELETE, OPTIONS')
                ->withHeader('Access-Control-Allow-Origin', $host)
                ->withHeader('Access-Control-Allow-Credentials', 'true')
                ->withHeader('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Features, Token, withCredentials');
        }
    });
};
