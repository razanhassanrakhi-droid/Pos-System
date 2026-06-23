<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();
echo "Username: " . $user->username . "\n";
echo "Full Name: " . print_r($user->full_name, true) . "\n";
echo "Email: " . $user->email . "\n";
