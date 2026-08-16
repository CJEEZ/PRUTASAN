<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$app->make('session')->start();
$token = csrf_token();
echo "TOKEN=$token\n";
$sessionId = session()->getId();
echo "SESSION_ID=$sessionId\n";

$row = DB::table('sessions')->where('id', $sessionId)->first();
echo "ROW_EXISTS=" . ($row ? 'yes' : 'no') . "\n";
if ($row) {
    echo "PAYLOAD=" . substr($row->payload, 0, 400) . "\n";
}

$rows = DB::table('sessions')->latest('last_activity')->limit(3)->get(['id','last_activity','payload']);
foreach ($rows as $r) {
    echo '---' . $r->id . '---' . PHP_EOL;
    echo substr($r->payload, 0, 200) . PHP_EOL;
}
