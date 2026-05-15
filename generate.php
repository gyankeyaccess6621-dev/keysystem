<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if(!isset($_SESSION['role'])){
header("Location: login.php");
exit;
}

$role =
$_SESSION['role'];

$username =
$_SESSION['username'];

$apps = file_exists('apps.json')
? json_decode(file_get_contents('apps.json'), true)
: [];

$data = file_exists('keys.json')
? json_decode(file_get_contents('keys.json'), true)
: [];

if(!is_array($apps)) $apps = [];
if(!is_array($data)) $data = [];

$current_admin = null;

foreach($data as $k){

if(
isset($k['username'])
&&
$k['username'] == $username
){

$current_admin = $k;
break;

}

}

/* GENERATE */

if(isset($_POST['generate'])){

$new_user =
strtoupper(
trim($_POST['username'])
);

$days =
intval($_POST['days']);

$type =
$_POST['type'];

$user_apps =
$_POST['app'] ?? [];

$admin_apps =
$_POST['apps'] ?? [];

if(!is_array($user_apps)){
$user_apps = [];
}

if(!is_array($admin_apps)){
$admin_apps = [];
}

/* DUPLICATE USERNAME */

foreach($data as $check){

if(
strtoupper($check['username'])
==
$new_user
){

$error =
"USERNAME ALREADY EXISTS";

break;

}

}

/* ADMIN LIMIT */

if(
$role == "admin"
&&
$type != "user"
){
die("ACCESS DENIED");
}

if(!isset($error)){

$random =
strtoupper(
substr(md5(rand()),0,6)
);

$key =
"GYAN-".
strtoupper($type).
"-".
$random;

$expiry =
date(
"Y-m-d",
strtotime("+$days days")
);

/* APPS */

$final_apps = [];

if($type == "admin"){

$final_apps = $admin_apps;

}else{

$final_apps = $user_apps;

}

$new = [

'username' => $new_user,

'key' => $key,

'apps' => $final_apps,

'expiry' => $expiry,

'type' => $type,

'created_by' => $username,

'hwid' => '',

'active' => false,

'paused' => false

];

$data[] = $new;

file_put_contents(
'keys.json',
json_encode(
$data,
JSON_PRETTY_PRINT
)
);

$success = $key;

}

}
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0"
>

<title>
Generate Key
</title>

<link
rel="stylesheet"
href="css/style.css?v=<?php echo time(); ?>"
>

<script>

if(window.history.replaceState){

window.history.replaceState(
null,
null,
window.location.href
);

}

</script>

</head>

<body>

<div class="centerbox">

<div class="loginbox">

<h1>
GENERATE KEY
</h1>

<p class="subtitle">
Create User & Admin Keys
</p>

<?php

if(isset($error)){

echo "
<div class='error'>
".$error."
</div>
";

}

?>

<form method="POST">

<input
type="text"
name="username"
placeholder="ENTER USERNAME"
required
>

<input
type="number"
name="days"
placeholder="ENTER DAYS"
required
>

<select
name="type"
id="typeSelect"
onchange="toggleApps()"
>

<option value="user">
USER
</option>

<?php if($role == "owner"){ ?>

<option value="admin">
ADMIN
</option>

<?php } ?>

</select>

<!-- USER APPS -->

<div id="userAppsBox">

<div class="line"></div>

<h3>
SELECT USER APPLICATIONS
</h3>

<?php

foreach($apps as $a){

$app_name =
$a['app'] ?? '';

if($app_name == ''){
continue;
}

if(
$role == "admin"
&&
isset($current_admin['apps'])
&&
is_array($current_admin['apps'])
&&
!in_array(
$app_name,
$current_admin['apps']
)
){
continue;
}

echo "

<label
style='display:block;margin-top:10px;'
>

<input
type='checkbox'
name='app[]'
value='".$app_name."'
style='width:auto;margin-right:8px;'
>

".$app_name."

</label>

";

}
?>

</div>

<!-- ADMIN APPS -->

<div
id="adminAppsBox"
style="display:none;"
>

<div class="line"></div>

<h3>
SELECT ADMIN APPLICATIONS
</h3>

<?php

foreach($apps as $a){

$app_name =
$a['app'] ?? '';

if($app_name == ''){
continue;
}

echo "

<label
style='display:block;margin-top:10px;'
>

<input
type='checkbox'
name='apps[]'
value='".$app_name."'
style='width:auto;margin-right:8px;'
>

".$app_name."

</label>

";

}
?>

</div>

<button name="generate">
GENERATE KEY
</button>

</form>

<?php if(isset($success)){ ?>

<div class="successbox">

<h2 id="generatedKey">
<?php echo $success; ?>
</h2>

<div class="value">

<?php

echo implode(
" , ",
$final_apps
);

?>

</div>

<button onclick="copyKey()">
COPY KEY
</button>

</div>

<?php } ?>

<a href="dashboard.php">

<button>
BACK TO DASHBOARD
</button>

</a>

</div>

</div>

<script>

function copyKey(){

var text =
document.getElementById(
'generatedKey'
).innerText;

navigator.clipboard.writeText(text);

alert('KEY COPIED');

}

function toggleApps(){

var type =
document.getElementById(
'typeSelect'
).value;

if(type == "admin"){

document.getElementById(
'adminAppsBox'
).style.display = "block";

document.getElementById(
'userAppsBox'
).style.display = "none";

}else{

document.getElementById(
'adminAppsBox'
).style.display = "none";

document.getElementById(
'userAppsBox'
).style.display = "block";

}

}

</script>

</body>
</html>