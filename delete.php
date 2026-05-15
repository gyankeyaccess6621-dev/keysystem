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

unset($data[$id]);

file_put_contents(
'keys.json',
json_encode(
array_values($data),
JSON_PRETTY_PRINT
)
);

header("Location: dashboard.php");
?>