<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

$owner_user = "JAI MATA DI";
$owner_pass = "HAR HAR MAHADEV";

if(isset($_POST['owner_login'])){

$user = trim($_POST['username']);
$pass = trim($_POST['password']);

if(
$user == $owner_user
&&
$pass == $owner_pass
){

$_SESSION['role'] = "owner";
$_SESSION['username'] = "OWNER";

header("Location: dashboard.php");
exit;

}else{

$error = "INVALID OWNER LOGIN";

}
}

if(isset($_POST['key_login'])){

$key = trim($_POST['key']);

$data =
json_decode(
file_get_contents('keys.json'),
true
);

foreach($data as $index => $k){

if($k['key'] == $key){

$_SESSION['role'] =
$k['type'];

$_SESSION['username'] =
$k['username'];

$_SESSION['key_index'] =
$index;

header("Location: dashboard.php");
exit;

}
}

$error = "INVALID KEY";

}
?>

<!DOCTYPE html>
<html>

<head>

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
GYAN AUTHENTICATION
</title>

<link rel="stylesheet"
href="css/style.css?v=<?php echo time(); ?>">

</head>

<body>

<div class="centerbox">

<div class="loginbox">

<h1>
GYAN AUTHENTICATION
</h1>

<p class="subtitle">
Premium Key Authentication System
</p>

<?php
if(isset($error)){
echo "<div class='error'>$error</div>";
}
?>

<form method="POST">

<input
type="text"
name="username"
placeholder="OWNER USERNAME"
required
>

<input
type="password"
name="password"
placeholder="OWNER PASSWORD"
required
>

<button name="owner_login">
OWNER LOGIN
</button>

</form>

<div class="line"></div>

<form method="POST">

<input
type="text"
name="key"
placeholder="ENTER YOUR KEY"
required
>

<button name="key_login">
KEY LOGIN
</button>

</form>

<a
href="https://discord.gg/6E3uQrYcEA"
target="_blank"
>

<button type="button">
JOIN DISCORD
</button>

</a>

</div>

</div>

</body>
</html>