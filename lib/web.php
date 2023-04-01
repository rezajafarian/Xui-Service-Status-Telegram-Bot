<?php

include_once 'library.php';
include_once '../config.php';

if(!isset($_GET['domin']) || !isset($_GET['step'])) {
    exit(json_encode(['success' => false, 'msg' => 'server not found', 'status_code' => 404]));
}

$res = $connect->query("SELECT * FROM `panels` WHERE `domin` = '{$_GET['domin']}' LIMIT 1");

if($res->num_rows == 0) {
    exit(json_encode(['success' => false, 'msg' => 'server not found', 'status_code' => 404]));
}

$panel = $res->fetch_assoc();
$url_parts = parse_url($panel['login_link']);

$ip = $url_parts['host'];
$port = $url_parts['port'] ?? '';
$domin = $panel['domin'];
$ssl = "{$url_parts['scheme']}://";
$session = $panel['session'];

$info = new Info($ip, $port, $domin, $ssl, $session);

if($_GET['step'] == 'status' && isset($_GET['name'])) {
    $information = $info->ServiceStatus($_GET['name']) != null ? $info->ServiceStatus($_GET['name']) : 'service not found';
    exit(json_encode(['success' => true, 'results' => $information, 'status_code' => 200], 448));
} else {
    exit(json_encode(['success' => false, 'msg' => 'invalid step or name parameter', 'status_code' => 400], 448));
}
