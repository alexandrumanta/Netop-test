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
if (!$logged_in){
    header("Location: login.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<title>Library</title>

<script type="text/javascript">
  function ConfirmDelete() {
    var x = confirm("Delete?");
    if (x)
      return true;
    else
      return false;
  }
</script>

</head>
<body class="page-body">
	<div class="page-container">
		<div class="sidebar-menu">
			<div class="sidebar-menu-inner">
				<header class="logo-env">
					<div class="logo">
						<a href="<?php echo HOST;?>" class="logo-expanded">
							<img src="../assets/images/logo.png" width="100" alt="" />
						</a>
						<h1>Library</h1>
					</div>
				</header>
				<ul class="main-menu">
					<li>
						<a href="books.php">
							<i class="fa fa-book"></i>
							<span class="title">Lista Carti</span>
						</a>
					</li>
					<li>
						<a href="books.php?action=add">
							<i class="fa fa-pencil"></i>
							<span class="title">Adauga Carti</span>
						</a>
					</li>
					<li>
						<a href="categories.php">
							<i class="fa fa-list"></i>
							<span class="title">Lista Categorii</span>
						</a>
					</li>
					<li>
						<a href="categories.php?action=add">
							<i class="fa fa-plus"></i>
							<span class="title">Adauga Categorii</span>
						</a>
					</li>
				</ul>
			</div>
		</div>