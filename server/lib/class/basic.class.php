<?php

class basic {

	static private $_singleton;
	
	private $objs = array();
	
	/*
	 * Basic class constructor 
	 * */
    private function __construct() {
    	
    	include_once(DOCROOT."settings.php");
    	$this->config = $config;
    	$this->db = db::getInstance($this->config['db_hostname'], $this->config['db_user'], $this->config['db_password'], $this->config['db_name']);
    	
    }
    
	static function getInstance(){
		if (is_null (self::$_singleton)) {
			self::$_singleton = new basic();
		}
		return self::$_singleton;
	}
	
	function __get($name){
		if(!isset($this->objs[$name])){
			$this->objs[$name] = new $name();
		}
		return $this->objs[$name];
	}
    
	static function sendEmail($mailto, $subject, $content, $fromEmail='', $fromName='', $content_type='text/plain', $attachements=array()){
		
		$mailer = new PHPMailer();
		
		$mailer->CharSet = "UTF-8";
		$mailer->Subject = $subject;
		
		$mailer->ContentType = $content_type;

		$mailer->Body = $content;
		
		foreach($attachements as $file){
			if(file_exists($file)) $mailer->AddAttachment($file, basename($file));
		}
		
		$mailer->AddAddress($mailto);
		$mailer->From = $fromEmail;
		$mailer->FromName = $fromName;
		$mailer->Send();
		
	}    

}
?>