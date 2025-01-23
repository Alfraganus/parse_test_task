<?php

use App\Controller\AdsImportController;
use App\Routes\GetTestInfo;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface;

return static function(App $app):void
{
    $app->get('/', function (RequestInterface $req, ResponseInterface $res): ResponseInterface {
        $res->getBody()->write("it works");
        return $res->withHeader('Content-Type', 'text/plain');
    });

    # CORS Pre-Flight OPTIONS Request Handler
    $app->options('/api/{routes:.+}', function (RequestInterface $request, ResponseInterface $response): ResponseInterface {
        return $response;
    });

    $app->group('/api', function (RouteCollectorProxyInterface $group) {
        $group->get('/test',GetTestInfo::class);
        $group->post('/import-ads-data', [AdsImportController::class, 'import']);
    });

    $app->addBodyParsingMiddleware();
};
