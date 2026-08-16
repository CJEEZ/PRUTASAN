<?php
$url = 'http://127.0.0.1:8000/login';
$cookieFile = sys_get_temp_dir() . '/tmp_auth_cookie.txt';
@unlink($cookieFile);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
curl_close($ch);

echo "GET status: $httpCode\n";
$matches = [];
if (preg_match('/name="_token" value="([^"]+)"/', $response, $matches)) {
    $token = $matches[1];
    echo "CSRF token: $token\n";
} else {
    echo "CSRF token not found\n";
    exit(1);
}

$data = http_build_query([
    '_token' => $token,
    'email' => 'clarencejohn@02@gmail.com',
    'password' => 'password123',
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

echo "POST status: " . $info['http_code'] . "\n";
echo "POST location: " . ($info['redirect_url'] ?: 'none') . "\n";
$headers = explode("\r\n", substr($response, 0, strpos($response, "\r\n\r\n")));
foreach ($headers as $hdr) {
    if (stripos($hdr, 'Set-Cookie:') === 0) {
        echo "COOKIE: $hdr\n";
    }
}
$body = substr($response, strpos($response, "\r\n\r\n") + 4);
echo "BODY START:\n";
echo substr($body, 0, 1000);
