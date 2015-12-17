<?php
if (!$logged_in){
    header("Location: login.php");
}else{
    header("Location: books.php");
}
?>