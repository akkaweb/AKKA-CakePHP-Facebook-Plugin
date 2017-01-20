<?php
use Cake\Routing\Router;

Router::plugin('Akkaweb/Facebook', function ($routes) {
    $routes->fallbacks();
});
