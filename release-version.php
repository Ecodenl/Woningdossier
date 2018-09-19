<?php

require __DIR__.'/vendor/autoload.php';

$c = include_once dirname(__FILE__).'/config/app.php'; echo $c['version']; exit;
