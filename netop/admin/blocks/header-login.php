<?php
require_once("../config/settings.php");
$user = new User();
try{
  $logged_in = $user->loggedIn();
}
catch (Exception $e){
  echo $e->getMessage();
  $logged_in = false;
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<title>Acces zona administrare</title>

</head>
<body class="page-body">