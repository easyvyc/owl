<?php

class TPL {
	
	// Compiled template files directory
	static private $compileDir = "tpl/c/";
	static private $compileFile = "";
	static private $tplFile = "";
	
	static private $vars = array();

	// If true, ignore compiled template file modification time and parse template every time 
	static public $parseAllTime = true;
	
	public function __construct(){}
	
	static public function getVar($name){
		
		eval("\$return_val = isset(self::\$vars['".implode("']['", explode(".", $name))."'])?self::\$vars['".implode("']['", explode(".", $name))."']:'';");
		return $return_val;
		 
	}

	static public function getLoop($name){
		if(isset(self::$vars[$name])) return self::$vars[$name];
		else return array();
	}
	
	// Set tepmlate variable type var
	static public function setVar($name, $value){
	    /*if(is_array($value)){
	        // cia biski neoptimalu, reiktu kazkaip kitaip 
	        foreach($value as $k=>$v){
 	        	self::setVar($name.".".$k, $v);
	        }
	    }
	    else
	        self::$vars[$name] = $value;
	    */
	    
	    self::$vars[$name] = $value;
	    
	}

	static private function loops($source){
	    $pos=0; $endPos=0; $n = 0;
	    while(($pos=strpos($source, "{loop ", $pos))!==false){
	        $endPos=strpos($source, "}", $pos);
	        // if loop mark '}' not found
	        if($endPos===false) {
	        	throw new exceptionErrorGeneric(E_USER_ERROR, "Loop does not have end mark '}'", self::$tplFile, ($pos==0?0:substr_count($source, PHP_EOL, 0, $pos)+1), null);
	        }
	        $loopname = substr($source, $pos+6, $endPos-$pos-6);
	        $loopPath = explode(".", $loopname);
	        $endPosLoopPos = strpos($source, "{-loop ".$loopname."}", $endPos);
	        // if end loop mark not found
	        if($endPosLoopPos===false) {
	        	throw new exceptionErrorGeneric(E_USER_ERROR, "Loop - '".$loopname."' does not have end tag", self::$tplFile, ($pos==0?0:substr_count($source, PHP_EOL, 0, $pos)+1), null);
	        }
	        $length = strlen("{loop ".$loopname."}");
	        $endPosLoop = $endPosLoopPos + $length + 1;
	        $tempCode = substr($source, $pos, $endPosLoop-$pos);
	        $name = str_replace(".", "_", $loopname);
	        $length = strlen("{loop ".$loopname."}");

			// when sub loop
			if(count($loopPath)>1){
				$loopPath1 = $loopPath;
				$loopPath_last = array_pop($loopPath1);
				$subloopname = '$'.implode("_", $loopPath1).'_val';
				$tempCode = str_replace('{loop '.$loopname.'}', '<?php $'.$name.'_iterator=1; if(isset('.$subloopname.'["'.$loopPath_last.'"])){ foreach('.$subloopname.'["'.$loopPath_last.'"] as $'.$name.'_key => $'.$name.'_val){ $'.$name.'_val[\'_FIRST\']=0; if($'.$name.'_iterator==1) $'.$name.'_val[\'_FIRST\']=1; if($'.$name.'_iterator%2==1) $'.$name.'_val[\'_EVEN\']=0; else $'.$name.'_val[\'_EVEN\']=1; $'.$name.'_val[\'_INDEX\']=$'.$name.'_iterator++; ?>', $tempCode);
				$tempCode = str_replace('{-loop '.$loopname.'}', '<?php }} ?>', $tempCode);
			}else{
				$tempCode = str_replace('{loop '.$loopname.'}', '<?php $'.$name.'_iterator=1; foreach(TPL::getLoop("'.$loopname.'") as $'.$name.'_key => $'.$name.'_val){ $'.$name.'_val[\'_FIRST\']=0; if($'.$name.'_iterator==1) $'.$name.'_val[\'_FIRST\']=1; if($'.$name.'_iterator%2==1) $'.$name.'_val[\'_EVEN\']=0; else $'.$name.'_val[\'_EVEN\']=1; $'.$name.'_val[\'_INDEX\']=$'.$name.'_iterator++; ?>', $tempCode);
				$tempCode = str_replace('{-loop '.$loopname.'}', '<?php } ?>', $tempCode);
			}
		    
		    /* TODO: padaryti komplexini loop'o ir vars'o parsinima
		     * $tempCode = ereg_replace("\{".$loopname."\.(\{[a-zA-Z0-9\._]{1,}\})\}", "<?php echo if(isset(\$".$name."_val[{\\1}])) \$".$name."_val[{\\1}]; ?>", $tempCode);*/

		    // TODO: kad pracekintu ar uzdaryti blokai ar uzsetinti kintamieji
		    // Replace loop negative blocks
		    $tempCode = ereg_replace("\{block ".$loopname."\.([a-zA-Z0-9_]{1,}) no\}", "<?php if(!isset(\$".$name."_val[\"\\1\"]) || !\$".$name."_val[\"\\1\"]){ ?>", $tempCode);
		    $tempCode = ereg_replace("\{-block ".$loopname."\.([a-zA-Z0-9_]{1,}) no\}", "<?php } ?>", $tempCode);
		    
		    // TODO: kad pracekintu ar uzdaryti blokai ar uzsetinti kintamieji
			// Replace loop blocks
		    $tempCode = ereg_replace("\{block ".$loopname."\.([a-zA-Z0-9_]{1,})\}", "<?php if(isset(\$".$name."_val[\"\\1\"]) && \$".$name."_val[\"\\1\"]){ ?>", $tempCode);
		    $tempCode = ereg_replace("\{-block ".$loopname."\.([a-zA-Z0-9_]{1,})\}", "<?php } ?>", $tempCode);
		    
		    // Double start '{{' and end '}}' use when no need to php code start and end tags '<?php', '?\>' 
		    $tempCode = ereg_replace("\{\{".$loopname."\.([a-zA-Z0-9_]{1,})\}\}", "\$".$name."_val[\"\\1\"]", $tempCode);

			// Replace loop variables
		    $tempCode = ereg_replace("\{".$loopname."\.([a-zA-Z0-9_]{1,})\}", "<?php if(isset(\$".$name."_val[\"\\1\"])) echo \$".$name."_val[\"\\1\"]; ?>", $tempCode);

	        $tempCode = self::loops($tempCode);

	        $source = substr_replace($source, $tempCode, $pos, $endPosLoop-$pos);
	    }
	    return $source;
	}
	
	// parse code blocks
	static private function codes($source){
		
	    $pos=0; $endPos=0; $n = 0;
	    while(($pos=strpos($source, "{code ", $pos))!==false){
	        $endPos=strpos($source, "}", $pos);
	        // if end loop mark not found
	        if($endPos===false) {
	        	throw new exceptionErrorGeneric(E_USER_ERROR, "Code does not have end mark '}' ", self::$tplFile, ($pos==0?0:substr_count($source, PHP_EOL, 0, $pos)+1), null);
	        }
	        $codename = substr($source, $pos+6, $endPos-$pos-6);
	        $source = substr_replace($source, "<?php ".TPL::getVar($codename)." ?>", $pos, $endPos-$pos+1);
	    }
		return $source;
		
		/*foreach(self::$vars as $key => $val){
			$source = str_replace("{code ".$key."}", "<?php $val ?>", $source);
		}
		return $source;*/
	}
	
	// TODO: padaryt kad pereistu per source ir rastu visus {block ..} patikrintu ar uzsetinti ar uzdaryti ir t.t.	
	static private function vars($source){

		$source = ereg_replace("\{block ([a-zA-Z0-9\._]{1,}\.)\{([a-zA-Z0-9\._]{1,})\}\}", "<?php if(TPL::getVar(\"\\1\".TPL::getVar(\"\\2\"))){ ?>", $source);
		$source = ereg_replace("\{block ([a-zA-Z0-9\._]{1,}\.)\{([a-zA-Z0-9\._]{1,})\}(\.[a-zA-Z0-9\._]{1,})\}", "<?php if(TPL::getVar(\"\\1\".TPL::getVar(\"\\2\").\"\\3\")){ ?>", $source);
		$source = ereg_replace("\{block \{([a-zA-Z0-9\._]{1,})\}(\.[a-zA-Z0-9\._]{1,})\">", "<?php if(TPL::getVar(TPL::getVar(\"\\1\").\"\\2\")){ ?>", $source);
		$source = ereg_replace("\{block ([a-zA-Z0-9\._]{1,})\}", "<?php if(TPL::getVar(\"\\1\")){ ?>", $source);
		$source = ereg_replace("\{-block ([a-zA-Z0-9\._]{1,})\}", "<?php } ?>", $source);

		$source = ereg_replace("\{block ([a-zA-Z0-9\._]{1,}\.)\{([a-zA-Z0-9\._]{1,})\}\}", "<?php if(!TPL::getVar(\"\\1\".TPL::getVar(\"\\2\"))){ ?>", $source);
		$source = ereg_replace("\{block ([a-zA-Z0-9\._]{1,}\.)\{([a-zA-Z0-9\._]{1,})\}(\.[a-zA-Z0-9\._]{1,})\}", "<?php if(!TPL::getVar(\"\\1\".TPL::getVar(\"\\2\").\"\\3\")){ ?>", $source);
		$source = ereg_replace("\{block \{([a-zA-Z0-9\._]{1,})\}(\.[a-zA-Z0-9\._]{1,})\}", "<?php if(!TPL::getVar(TPL::getVar(\"\\1\").\"\\2\")){ ?>", $source);
		$source = ereg_replace("\{block ([a-zA-Z0-9\._]{1,}) no\}", "<?php if(!TPL::getVar(\"\\1\")){ ?>", $source);
		$source = ereg_replace("\{-block ([a-zA-Z0-9\._]{1,}) no\}", "<?php } ?>", $source);
	    
	    $source = ereg_replace("\{\{([a-zA-Z0-9\._]{1,})\}\}", "TPL::getVar(\"\\1\")", $source);
	    /*
	    $source = ereg_replace("\{([a-zA-Z0-9\._]{1,}\.)\{([a-zA-Z0-9\._]{1,})\}\}", "<?php echo TPL::getVar(\"\\1\".TPL::getVar(\"\\2\")); ?>", $source);
	    $source = ereg_replace("\{([a-zA-Z0-9\._]{1,}\.)\{([a-zA-Z0-9\._]{1,})\}(\.[a-zA-Z0-9\._]{1,})\}", "<?php echo TPL::getVar(\"\\1\".TPL::getVar(\"\\2\").\"\\3\"); ?>", $source);
	    $source = ereg_replace("\{\{([a-zA-Z0-9\._]{1,})\}(\.[a-zA-Z0-9\._]{1,})\}", "<?php echo TPL::getVar(TPL::getVar(\"\\1\").\"\\2\"); ?>", $source);
	    */
	    $source = ereg_replace("\{([a-zA-Z0-9\._]{1,})\}", "<?php echo TPL::getVar(\"\\1\"); ?>", $source);
	    
	    return $source;
	    
	}
	
	// Parse template file
	static public function parse($tpl_file){
		
		self::$tplFile = $tpl_file;
		
		// Compile file name
		self::$compileFile = self::$compileDir.md5(self::$tplFile).".php";
		
		// If template file is not exist then error
		if(!file_exists(self::$tplFile) && !file_exists(self::$compileFile)){
		    throw new exceptionErrorGeneric(E_USER_ERROR, "There is no template file - '".self::$tplFile."'.", __FILE__, __LINE__, null);
		}
		
		// 
		clearstatcache();
		// Check whether parse template 
		if(!file_exists(self::$compileFile) || (filemtime(self::$tplFile) > filemtime(self::$compileFile)) || self::$parseAllTime){
		    
		    // Read template file
		    $file = fopen(self::$tplFile, "r");
		    $source = fread($file, filesize(self::$tplFile));
		    fclose($file);
		    
			// 
			$source = self::codes($source);
			$source = self::loops($source);
			$source = self::vars($source);
			
			// Create compiled temlpate file
			$file = fopen(self::$compileFile, "w");
			@fwrite($file, $source);
			@chmod(self::$compileFile, 0777);
			@fclose($file);

		}
		
		if(!file_exists(self::$compileFile)){
			throw new exceptionErrorGeneric(E_USER_ERROR, "Error compiling template file - '".self::$tplFile."' in a directory '".self::$compileDir."'.", __FILE__, __LINE__, null);
		}

		return self::$compileFile;
		
	}
	
}

?>
