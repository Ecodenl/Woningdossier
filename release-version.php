<?php

require __DIR__.'/vendor/autoload.php';

$c = include_once dirname(__FILE__) . '/config/app.php';print $c['version'];exit;