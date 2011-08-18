<?php 

define('DOCROOT', dirname(__FILE__)."/");
define('VIEWSDIR', DOCROOT."view/");
define('LIBDIR', DOCROOT."lib/");
define('CLASSDIR', LIBDIR."class/");
define('LANGDIR', LIBDIR."lang/");


function __autoload($class_name){
	
	if(file_exists(CLASSDIR.$class_name.".class.php")){
		include_once(CLASSDIR.$class_name.".class.php");
		return true;
	}
	
}

function pa($arr){
	echo "<pre>";
	print_r($arr);
	echo "</pre>";
}

?>
