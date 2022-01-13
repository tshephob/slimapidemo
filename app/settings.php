<?php
declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\Container;
use Monolog\Logger;

return function (Container $container) {

    $container->set('settings', function() {
        return [
            'name' => 'Example Slim Application',
            'displayErrorDetails' => true,
            'logErrorDetails' => true,
            'logErrors' => true,
            'logger' => [
                'name' => 'slim-app',
                'path' => __DIR__ . '/../logs/app.log',
                'level' => Logger::DEBUG 
            ],
            'connection' => [
                'host' => '127.0.0.1',
                'databasename' => 'currencyapi',
                'user' => 'root',
                'password' => '',
            ]
        ];
    });
};
