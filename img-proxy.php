<?php
header('Access-Control-Allow-Origin: *');

$url = isset($_GET['url']) ? $_GET['url'] : '';
if (!$url || !preg_match('#^http://102\.37\.218\.218:8004/#', $url)) {
    http_response_code(400);
    exit;
}

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$data = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($data === false || $httpCode === 0) {
    http_response_code(502);
    exit;
}

$ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
$types = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'];
$contentType = $types[$ext] ?? 'image/jpeg';

header('Content-Type: ' . $contentType);
header('Cache-Control: public, max-age=86400');
echo $data;
