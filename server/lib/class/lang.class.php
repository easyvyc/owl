<?php

class LANG {

	private static $lang;
	private static $library = array();
	
    public static function load($lang) {
    	self::$lang = $lang;
    	include_once(LANGDIR.self::$lang.".php");
    }
    
    public static function get($key=''){
    	if($key=='') return self::$library;
    	if(isset(self::$library[$key])) return self::$library[$key];
    	else{
    		throw new Exception(E_USER_ERROR, "Language error: there is no phrase for keyword '$key'", __FILE__, __LINE__, null);
    	}
    }
    
}
?>