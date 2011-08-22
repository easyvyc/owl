<?php
/*
 * Created on 2010.09.01
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

error_reporting(E_ERROR | E_USER_ERROR | E_WARNING);
ini_set("display_errors", true);

session_start();

include("config.php");

$process = new process();

if(!$_GET['a']){
	$method = "start";
}else{
	$method = $_GET['a'];
}

if(method_exists($process, $method)){
	header('Access-Control-Allow-Origin: *');
	echo $process->{$method}($_GET['p']);
}


?>
