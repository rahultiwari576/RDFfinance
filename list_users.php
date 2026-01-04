<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$users = DB::table('users')->select('name', 'email', 'aadhar_number')->get();
echo "=== Registered Users ===\n";
foreach($users as $user) {
    echo "Name: {$user->name} | Email: {$user->email} | Aadhar: " . ($user->aadhar_number ?: 'NOT SET') . "\n";
}
