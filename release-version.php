<?php

require __DIR__.'/vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$c = include_once dirname(__FILE__).'/config/app.php';
$version = array_key_exists('version', $c) && ! empty($c['version']) ? $c['version'] : '1.0.0';

echo env('APP_VERSION', $version); exit;
