<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if(!isset($_SESSION['role'])){
header("Location: login.php");
exit;
}

$role = $_SESSION['role'];
$username = $_SESSION['username'];

$data = file_exists('keys.json')
? json_decode(file_get_contents('keys.json'), true)
: [];

$apps = file_exists('apps.json')
? json_decode(file_get_contents('apps.json'), true)
: [];

if(!is_array($data)) $data = [];
if(!is_array($apps)) $apps = [];

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

/* ACTIVATE */

if(
($role == "user" || $role == "admin")
&&
isset($_SESSION['key_index'])
){

$index = $_SESSION['key_index'];

if(
isset($_GET['activate'])
&&
isset($data[$index])
&&
!$data[$index]['active']
){

$data[$index]['active'] = true;

file_put_contents(
'keys.json',
json_encode($data, JSON_PRETTY_PRINT)
);

header("Location: dashboard.php");
exit;

}

}

/* FILTER */

$filter =
$_GET['filter'] ?? 'user';

$app_filter =
$_GET['app'] ?? '';

/* STATS */

$total = count($data);

$admins = 0;
$users = 0;
$active = 0;

foreach($data as $k){

if(($k['type'] ?? '') == "admin"){
$admins++;
}

if(($k['type'] ?? '') == "user"){
$users++;
}

if(($k['active'] ?? false)){
$active++;
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
Dashboard
</title>

<link
rel="stylesheet"
href="css/style.css?v=<?php echo time(); ?>"
>

<script>

window.addEventListener(
"pageshow",
function(event){

if(
event.persisted
||
window.performance.navigation.type === 2
){

window.location.reload();

}

}
);

</script>

</head>

<body>

<div class="main">

<!-- TOPBAR -->

<div class="topbar">

<h1>
GYAN AUTHENTICATION
</h1>

<div class="badge">
<?php echo strtoupper($role); ?>
</div>

</div>

<!-- BUTTONS -->

<div class="btns">

<?php if($role == "owner"){ ?>

<a href="create_app.php">
<button>
MANAGE APP
</button>
</a>

<?php } ?>

<a href="generate.php">
<button>
GENERATE KEY
</button>
</a>

<a href="logout.php">
<button>
LOGOUT
</button>
</a>

<?php if($role == "owner"){ ?>

<a
href="deleteall.php?type=user"
onclick="return confirm('DELETE ALL USERS ?')"
>
<button>
DELETE USERS
</button>
</a>

<a
href="deleteall.php?type=admin"
onclick="return confirm('DELETE ALL ADMINS ?')"
>
<button>
DELETE ADMINS
</button>
</a>

<?php } ?>

</div>

<!-- APPLICATIONS -->

<div class="card">

<div
class="keytitle"
onclick="toggleApps()"
style="
display:flex;
justify-content:space-between;
align-items:center;
cursor:pointer;
"
>

<span>
APPLICATIONS
</span>

<span id="appArrow">
▼
</span>

</div>

<div
id="appsBox"
style="
display:none;
margin-top:20px;
"
>

<div class="grid">

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

<a href='?filter=user&app=".$app_name."'>

<div class='card statcard'>

<div class='label'>
".$app_name."
</div>

</div>

</a>

";

}
?>

</div>

</div>

</div>

<br>

<!-- STATS -->

<div class="grid">

<a href="?filter=all">

<div class="card statcard">

<div class="label">
TOTAL KEYS
</div>

<div class="bigvalue">
<?php echo $total; ?>
</div>

</div>

</a>

<a href="?filter=admin">

<div class="card statcard">

<div class="label">
ADMIN KEYS
</div>

<div class="bigvalue">
<?php echo $admins; ?>
</div>

</div>

</a>

<a href="?filter=user">

<div class="card statcard">

<div class="label">
USER KEYS
</div>

<div class="bigvalue">
<?php echo $users; ?>
</div>

</div>

</a>

<a href="?filter=active">

<div class="card statcard">

<div class="label">
ACTIVE KEYS
</div>

<div class="bigvalue">
<?php echo $active; ?>
</div>

</div>

</a>

</div>

<br>

<!-- KEYS -->

<div class="grid">

<?php

foreach($data as $id => $k){

$type =
$k['type'] ?? '';

if($filter != ''){

if(
$filter == "admin"
&&
$type != "admin"
){
continue;
}

if(
$filter == "user"
&&
$type != "user"
){
continue;
}

if(
$filter == "active"
&&
!($k['active'] ?? false)
){
continue;
}

}

if($app_filter != ''){

$key_apps =
$k['apps'] ?? [];

if(!is_array($key_apps)){
$key_apps = [];
}

if(
!in_array(
$app_filter,
$key_apps
)
){
continue;
}

}

if(
$role == "admin"
&&
($k['created_by'] ?? '') != $username
){
continue;
}

?>

<div class="card">

<div
class="keytitle"
onclick="toggleDetails('detail<?php echo $id; ?>')"
style="
display:flex;
justify-content:space-between;
align-items:center;
cursor:pointer;
"
>

<span>
<?php echo $k['username'] ?? 'UNKNOWN'; ?>
</span>

<span>
▼
</span>

</div>

<div
id="detail<?php echo $id; ?>"
style="display:none;"
>

<div class="line"></div>

<div class="label">
APPLICATIONS
</div>

<div class="value">

<?php

$key_apps =
$k['apps'] ?? [];

if(
is_array($key_apps)
&&
count($key_apps) > 0
){

echo implode(
" , ",
$key_apps
);

}else{

echo "NO APP";

}

?>

</div>

<div class="label">
KEY
</div>

<div class="value">
<?php echo $k['key'] ?? ''; ?>
</div>

<div class="label">
TYPE
</div>

<div class="value">
<?php echo strtoupper($type); ?>
</div>

<div class="label">
EXPIRY
</div>

<div class="value">
<?php echo $k['expiry'] ?? ''; ?>
</div>

<div class="label">
STATUS
</div>

<div class="status">

<?php

if(($k['paused'] ?? false)){

echo "PAUSED";

}else{

echo ($k['active'] ?? false)
? "ACTIVE"
: "INACTIVE";

}

?>

</div>

<?php

if(
$role == "owner"
||
(
$role == "admin"
&&
$type == "user"
)
){

?>

<a href="pause.php?id=<?php echo $id; ?>">

<button>

<?php

echo ($k['paused'] ?? false)
? "RESUME KEY"
: "PAUSE KEY";

?>

</button>

</a>

<a
href="delete.php?id=<?php echo $id; ?>"
onclick="return confirm('DELETE KEY ?')"
>

<button>
DELETE KEY
</button>

</a>

<?php } ?>

</div>

</div>

<?php } ?>

</div>

</div>

<script>

function toggleApps(){

var box =
document.getElementById('appsBox');

var arrow =
document.getElementById('appArrow');

if(box.style.display == "none"){

box.style.display = "block";
arrow.innerHTML = "▲";

}else{

box.style.display = "none";
arrow.innerHTML = "▼";

}

}

function toggleDetails(id){

var box =
document.getElementById(id);

if(box.style.display == "none"){

box.style.display = "block";

}else{

box.style.display = "none";

}

}

</script>

</body>
</html>