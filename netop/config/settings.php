<?php
/*********************************
Setari path
*********************************/
	
define('DOC_ROOT',$_SERVER['DOCUMENT_ROOT']."/netop/");
define('HOST','http://localhost/netop/');
define('ADMIN_ROOT',DOC_ROOT . 'admin/');
define('ADMIN_URL',HOST . 'admin/');

/*********************************
Setari debug
*********************************/

define('DEBUG',1);	// Salvare query-uri
define('LOG',1);	// Salvare adaugare carti, utilizatori, comenzi
define('LOG_FOLDER',DOC_ROOT . 'logs');

/**********************************
Incarcare clase - autoload
**********************************/

function __autoload($class){
	include_once DOC_ROOT . "classes/{$class}.php";
}

require_once "password.php";

session_start();
?>