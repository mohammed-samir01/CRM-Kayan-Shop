<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::latest()->first();

if (!$user) {
    echo "No users found.\n";
    exit;
}

echo "Checking User: " . $user->name . " (ID: " . $user->id . ")\n";
echo "Roles:\n";
foreach ($user->roles as $role) {
    echo "- " . $role->name . "\n";
    echo "  Permissions in Role:\n";
    foreach ($role->permissions as $perm) {
        echo "    - " . $perm->name . "\n";
    }
}

echo "Direct Permissions:\n";
foreach ($user->permissions as $perm) {
    echo "- " . $perm->name . "\n";
}

echo "Can 'view orders'? " . ($user->can('view orders') ? 'YES' : 'NO') . "\n";
echo "Can 'view dashboard'? " . ($user->can('view dashboard') ? 'YES' : 'NO') . "\n";
