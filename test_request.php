<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/forgot-password', 'POST', ['login' => 'mo']);
$response = $kernel->handle($request);

echo "Status Code: " . $response->getStatusCode() . "\n";
echo "Content length: " . strlen($response->getContent()) . "\n";
