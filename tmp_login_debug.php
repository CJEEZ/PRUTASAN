<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$session = app('session');
$session->start();
$sessionStore = $session->driver();

$attempt = Illuminate\Support\Facades\Auth::guard('web')->attempt([
    'email' => 'clarencejohn@02@gmail.com',
    'password' => 'password123',
]);
echo 'ATTEMPT=' . ($attempt ? 'YES' : 'NO') . PHP_EOL;

$loginPage = Illuminate\Http\Request::create('/login', 'GET');
$loginPage->setLaravelSession($sessionStore);
$loginResponse = $app->handle($loginPage);
$body = $loginResponse->getContent();

preg_match('/name="_token" value="([^"]+)"/', $body, $m);
$token = $m[1] ?? 'NONE';
echo "TOKEN=$token\n";

$postRequest = Illuminate\Http\Request::create('/login', 'POST', [
    '_token' => $token,
    'email' => 'clarencejohn@02@gmail.com',
    'password' => 'password',
]);
$postRequest->setLaravelSession($sessionStore);
$postRequest->headers->set('referer', 'http://127.0.0.1:8000/login');

$response = $app->handle($postRequest);

echo 'STATUS=' . $response->getStatusCode() . PHP_EOL;
if (method_exists($response, 'getTargetUrl')) {
    echo 'TARGET=' . ($response->getTargetUrl() ?? 'NONE') . PHP_EOL;
} else {
    echo 'TARGET=NONE' . PHP_EOL;
}
echo substr($response->getContent(), 0, 2000) . PHP_EOL;

$app->terminate($postRequest, $response);
