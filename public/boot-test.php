<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

$start = microtime(true);

require __DIR__.'/../vendor/autoload.php';
$bootTime = round((microtime(true) - $start) * 1000, 2);
echo "Autoload: {$bootTime}ms\n";

$start = microtime(true);
$app = require_once __DIR__.'/../bootstrap/app.php';
$bootTime = round((microtime(true) - $start) * 1000, 2);
echo "Bootstrap: {$bootTime}ms\n";

$start = microtime(true);
$kernel = $app->make(Kernel::class);
$kernelTime = round((microtime(true) - $start) * 1000, 2);
echo "Kernel: {$kernelTime}ms\n";

$start = microtime(true);
$request = Request::capture();
$requestTime = round((microtime(true) - $start) * 1000, 2);
echo "Request: {$requestTime}ms\n";

$start = microtime(true);
$response = $kernel->handle($request);
$handleTime = round((microtime(true) - $start) * 1000, 2);
echo "Handle (includes rendering): {$handleTime}ms\n";
