<?php
$cookieFile = __DIR__ . '/tmp_csrf_cookies.txt';
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

preg_match('/set-cookie:\s*([^=;]+)=([^;]+)/i', $headers, $m);
echo "HEADERS\n$headers\n";
echo "COOKIE_MATCH=" . ($m[1] ?? 'NONE') . '=' . ($m[2] ?? 'NONE') . "\n";
echo "BODY_TOKEN_MATCH=" . (preg_match('/name="_token" value="([^"]+)"/', $body, $t) ? $t[1] : 'NONE') . "\n";

$body2 = file_get_contents($cookieFile);
echo "COOKIE_FILE\n$body2\n";
