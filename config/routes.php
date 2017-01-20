<?php
use Cake\Routing\Router;

Router::plugin('Facebook', function ($routes) {
    $routes->fallbacks();
});
