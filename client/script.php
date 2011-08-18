<?php 

session_start();

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
    ob_start("ob_gzhandler");
else
    ob_start();

if($_GET['f']=='js'){
	header ("content-type: text/javascript; charset: UTF-8");
}
if($_GET['f']=='css'){
	header ("content-type: text/css; charset: UTF-8");
}
header ("cache-control: must-revalidate");
header ("expires: " . gmdate ("D, d M Y H:i:s", time() + 60 * 60 * 24) . " GMT");

$file = dirname(__FILE__)."/".$_GET['load'].".".$_GET['f'];

echo file_get_contents($file);

?>