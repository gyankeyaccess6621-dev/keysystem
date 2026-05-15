<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if(
!isset($_SESSION['role'])
||
$_SESSION['role'] != "owner"
){
die("ACCESS DENIED");
}

/* LOAD APPS */

$apps = file_exists('apps.json')
? json_decode(file_get_contents('apps.json'), true)
: [];

if(!is_array($apps)){
$apps = [];
}

/* CREATE APP */

if(isset($_POST['create'])){

$app =
trim($_POST['app']);

$app =
strtoupper($app);

if($app != ''){

$exists = false;

foreach($apps as $a){

if(
strtoupper($a['app'])
==
$app
){

$exists = true;
break;

}

}

if($exists){

$error =
"APPLICATION ALREADY EXISTS";

}else{

$apps[] = [
'app' => $app
];

file_put_contents(
'apps.json',
json_encode(
$apps,
JSON_PRETTY_PRINT
)
);

header("Location: create_app.php");
exit;

}

}

}

/* DELETE APP */

if(isset($_GET['delete'])){

$id =
intval($_GET['delete']);

if(isset($apps[$id])){

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
exit;

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
Manage Applications
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

<style>

.createbox{
width:100%;
max-width:550px;
}

.appcard{
margin-top:12px;
}

.appflex{
display:flex;
justify-content:space-between;
align-items:center;
gap:10px;
flex-wrap:wrap;
}

.deletebtn{
width:170px;
}

</style>

</head>

<body>

<div class="centerbox">

<div class="loginbox createbox">

<h1>
MANAGE APP
</h1>

<p class="subtitle">
Create & Delete Applications
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

<!-- CREATE APP -->

<form method="POST">

<input
type="text"
name="app"
placeholder="ENTER APPLICATION NAME"
required
>

<button name="create">
CREATE APPLICATION
</button>

</form>

<div class="line"></div>

<h2>
ALL APPLICATIONS
</h2>

<?php

if(count($apps) == 0){

echo "

<div class='card appcard'>

<div class='value'>
NO APPLICATION FOUND
</div>

</div>

";

}else{

foreach($apps as $id => $a){

$app_name =
$a['app'] ?? '';

echo "

<div class='card appcard'>

<div class='appflex'>

<div class='value'>
".$app_name."
</div>

<a
href='?delete=".$id."'
class='deletebtn'
onclick=\"return confirm('DELETE APPLICATION ?')\"
>

<button>
DELETE APP
</button>

</a>

</div>

</div>

";

}

}

?>

<div class="line"></div>

<a href="dashboard.php">

<button>
BACK TO DASHBOARD
</button>

</a>

</div>

</div>

</body>
</html>