<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/login', 'POST', [
    'email' => 'clarencejohn@02@gmail.com',
    'password' => 'password',
]);
$request->setLaravelSession(app('session')->driver());

$response = $app->handle($request);
echo $response->getStatusCode() . PHP_EOL;
if (method_exists($response, 'getTargetUrl')) {
    echo $response->getTargetUrl() . PHP_EOL;
}
if (method_exists($response, 'getContent')) {
    echo substr($response->getContent(), 0, 400) . PHP_EOL;
}
$app->terminate($request, $response);
