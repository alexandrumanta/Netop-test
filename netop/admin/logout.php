<?php
require("../config/settings.php");

User::Logout();

header('Location: index.php');
?>