<?php

$key = $_GET['key'] ?? '';
$hwid = $_GET['hwid'] ?? '';
$app = $_GET['app'] ?? '';

$data =
json_decode(
file_get_contents('keys.json'),
true
);

foreach($data as $index => $k){

if(
$k['key'] == $key
&&
$k['app'] == $app
){

if(
isset($k['paused'])
&&
$k['paused']
){
die('KEY_PAUSED');
}

if(
strtotime(date("Y-m-d"))
>
strtotime($k['expiry'])
){
die('KEY_EXPIRED');
}

if(
!isset($k['hwid'])
||
$k['hwid'] == ''
){

$data[$index]['hwid'] =
$hwid;

file_put_contents(
'keys.json',
json_encode(
$data,
JSON_PRETTY_PRINT
)
);

die('KEY_LINKED');
}

if($k['hwid'] != $hwid){
die('INVALID_HWID');
}

if(!$k['active']){
die('KEY_INACTIVE');
}

die('VALID_KEY');

}
}

die('INVALID_KEY');

?>