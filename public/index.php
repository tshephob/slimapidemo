<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use DI\Container;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/connection.php';

$container = new Container();

$settings = require __DIR__ . '/../app/settings.php';
$settings($container);

AppFactory::setContainer($container);

$oApp = AppFactory::create();
$oApp->addBodyParsingMiddleware();
$oApp->addErrorMiddleware(true, true, true);


$oCurrencyroute = require __DIR__. '/../app/currencies.route.php';
$oCurrencyroute($oApp);


$oApp->run();
?>