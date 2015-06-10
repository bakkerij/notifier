<?php
use Cake\Routing\Router;

Router::plugin('Notifier', function ($routes) {
    $routes->fallbacks();
});

Router::prefix('admin', function ($routes) {
    $routes->plugin('Notifier', ['path' => '/notifier'], function ($routes) {

        $routes->connect('/notifications/*', [
            'prefix'     => 'admin',
            'plugin'     => 'Notifier',
            'controller' => 'Notifications',
        ]);

        $routes->fallbacks('InflectedRoute');
    });
    $routes->fallbacks('InflectedRoute');
});