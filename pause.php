<?php
session_start();

if(!isset($_SESSION['role'])){
header("Location: login.php");
exit;
}

$id = $_GET['id'];

$data =
json_decode(
file_get_contents('keys.json'),
true
);

$data[$id]['paused'] =
!$data[$id]['paused'];

file_put_contents(
'keys.json',
json_encode(
$data,
JSON_PRETTY_PRINT
)
);

header("Location: dashboard.php");
?>