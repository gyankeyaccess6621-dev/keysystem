<?php

header('Content-Type: application/json');

$data =
json_decode(
file_get_contents("php://input"),
true
);

/* GET DATA */

$key =
trim($data['key'] ?? '');

$app =
trim($data['app'] ?? '');

$hwid =
trim($data['hwid'] ?? '');

/* EMPTY CHECK */

if(
$key == ''
||
$app == ''
||
$hwid == ''
){

echo json_encode([
'status' => 'error',
'message' => 'missing_data'
]);

exit;

}

/* LOAD KEYS */

$keys = file_exists('keys.json')
? json_decode(file_get_contents('keys.json'), true)
: [];

if(!is_array($keys)){
$keys = [];
}

/* FIND KEY */

foreach($keys as $id => $k){

$saved_key =
$k['key'] ?? '';

if($saved_key != $key){
continue;
}

/* PAUSED */

if(($k['paused'] ?? false)){

echo json_encode([
'status' => 'paused'
]);

exit;

}

/* EXPIRED */

$expiry =
$k['expiry'] ?? '';

if(
$expiry != ''
&&
strtotime($expiry) < time()
){

echo json_encode([
'status' => 'expired'
]);

exit;

}

/* APPS */

$key_apps =
$k['apps'] ?? [];

if(!is_array($key_apps)){
$key_apps = [];
}

if(
!in_array(
$app,
$key_apps
)
){

echo json_encode([
'status' => 'invalid_app'
]);

exit;

}

/* HWID LOCK */

$saved_hwid =
$k['hwid'] ?? '';

/* FIRST LOGIN */

if($saved_hwid == ''){

$keys[$id]['hwid'] = $hwid;

file_put_contents(
'keys.json',
json_encode(
$keys,
JSON_PRETTY_PRINT
)
);

$saved_hwid = $hwid;

}

/* HWID CHECK */

if($saved_hwid != $hwid){

echo json_encode([
'status' => 'hwid_mismatch'
]);

exit;

}

/* ACTIVATE */

if(!($k['active'] ?? false)){

$keys[$id]['active'] = true;

file_put_contents(
'keys.json',
json_encode(
$keys,
JSON_PRETTY_PRINT
)
);

}

/* SUCCESS */

echo json_encode([

'status' => 'success',

'user' =>
$k['username'] ?? '',

'type' =>
$k['type'] ?? '',

'expiry' =>
$expiry,

'apps' =>
$key_apps

]);

exit;

}

/* INVALID */

echo json_encode([
'status' => 'invalid'
]);

?>