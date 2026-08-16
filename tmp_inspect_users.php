<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$users = App\Models\User::take(5)->get(['id','name','email','role']);
foreach ($users as $u) {
    echo $u->id . '|' . $u->name . '|' . $u->email . '|' . $u->role . PHP_EOL;
}
