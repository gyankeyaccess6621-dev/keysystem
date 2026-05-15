<?php

session_start();

if(
!isset($_SESSION['role'])
||
$_SESSION['role'] != 'owner'
){
die("ACCESS DENIED");
}

$id = $_GET['id'];

$apps =
json_decode(
file_get_contents('apps.json'),
true
);

unset($apps[$id]);

$apps =
array_values($apps);

file_put_contents(
'apps.json',
json_encode(
$apps,
JSON_PRETTY_PRINT
)
);

header("Location: create_app.php");

?>