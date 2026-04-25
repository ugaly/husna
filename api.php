<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$id = isset($_GET['id']) ? preg_replace('/[^A-Za-z0-9]/', '', $_GET['id']) : '';
if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID required']); exit; }

$url = 'http://102.37.218.218:8004/api/active-user/' . $id . '/';

if (function_exists('curl_init')) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    if ($response === false || $code === 0) {
        http_response_code(502);
        echo json_encode(['error' => 'curl failed', 'detail' => $curlError]);
        exit;
    }
} else {
    $context = stream_context_create(['http' => ['timeout' => 10, 'ignore_errors' => true]]);
    $response = @file_get_contents($url, false, $context);
    if ($response === false) {
        http_response_code(502);
        echo json_encode(['error' => 'file_get_contents failed', 'detail' => error_get_last()['message'] ?? 'unknown']);
        exit;
    }
    $code = 200;
}

http_response_code($code);
echo $response;

