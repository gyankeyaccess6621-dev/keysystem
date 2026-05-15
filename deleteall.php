<?php
session_start();

if(!isset($_SESSION['role'])){
header("Location: login.php");
exit;
}

$type = $_GET['type'];

$data =
json_decode(
file_get_contents('keys.json'),
true
);

$new = [];

foreach($data as $k){

if($k['type'] != $type){

$new[] = $k;

}

}

file_put_contents(
'keys.json',
json_encode(
$new,
JSON_PRETTY_PRINT
)
);

header("Location: dashboard.php");
?>