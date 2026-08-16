<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

$session = app('session');
$session->start();
$sessionStore = $session->driver();

$req = Request::create('/login', 'GET');
$req->setLaravelSession($sessionStore);
$response = $app->handle($req);
$body = $response->getContent();
echo "AFTER_GET_INTENDED=" . ($sessionStore->get('url.intended') ?? 'NONE') . PHP_EOL;

echo "AFTER_GET_SESSION_ID=" . $sessionStore->getId() . PHP_EOL;

$postReq = Request::create('/login', 'POST', [
    '_token' => 'dummy',
    'email' => 'clarencejohn@02@gmail.com',
    'password' => 'password123',
]);
$postReq->setLaravelSession($sessionStore);
$postReq->headers->set('referer', 'http://127.0.0.1:8000/login');
$response2 = $app->handle($postReq);
echo "STATUS=" . $response2->getStatusCode() . PHP_EOL;
echo "CONTENT=" . substr($response2->getContent(), 0, 300) . PHP_EOL;

echo "AFTER_POST_INTENDED=" . ($sessionStore->get('url.intended') ?? 'NONE') . PHP_EOL;
