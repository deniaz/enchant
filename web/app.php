<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Enchant\Application;
use Symfony\Component\HttpFoundation\Response;

$app = new Application();

$app->setViewDirectory(__DIR__ . '/../views/');

$app->get('/', function() {
    return new Response('Index', 200);
});

$app->get('/profile', function() {
    return new Response('Profile', 200);
});

$app->run();