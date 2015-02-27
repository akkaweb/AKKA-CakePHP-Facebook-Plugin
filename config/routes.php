<?php
use Cake\Routing\Router;

Router::plugin('AkkaFacebook', function ($routes) {
    $routes->fallbacks();
});
