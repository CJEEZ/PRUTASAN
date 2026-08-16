<?php
$cookieFile = __DIR__ . '/tmp_curl_cookies.txt';
@unlink($cookieFile);

$ch = curl_init('http://127.0.0.1:8000/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
$response = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

$headers = substr($response, 0, $info['header_size']);
$body = substr($response, $info['header_size']);
preg_match('/name="_token" value="([^"]+)"/', $body, $m);
$token = $m[1] ?? 'NONE';
echo "TOKEN=$token\n";

echo "COOKIE_FILE_EXISTS=" . (file_exists($cookieFile) ? 'yes' : 'no') . "\n";
if (file_exists($cookieFile)) {
    echo file_get_contents($cookieFile) . "\n";
}

$ch = curl_init('http://127.0.0.1:8000/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    '_token' => $token,
    'email' => 'clarencejohn@02@gmail.com',
    'password' => 'password123',
]));
$response2 = curl_exec($ch);
$info2 = curl_getinfo($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$body2 = $response2;
$headers2 = substr($response2, 0, $info2['header_size']);
$body2 = substr($response2, $info2['header_size']);

curl_close($ch);

echo "HTTP_CODE=$httpCode\n";
echo "POST_HEADERS\n$headers2\n";
echo "POST_BODY_START\n" . substr($body2, 0, 1200) . "\n";
