<?php

class visitorInfo {
 
  var $debug;
  var $serverVars;
  var $db;

  var $IP;
  var $RequestPage;
  var $URL;
  var $RequestMethod;
  var $UA;
  var $Broswer;
  var $BroswerVersion;
  var $OS;
  var $Referer;
  var $RefererDomain;
  var $Keyword;
  var $C2Code;
  var $C3Code;
  var $CountryName;
  var $ProxyUsed;
  var $ProxyIP;
  var $ProxyCountry;
  var $ProxySignature;
  var $AcceptedLanguage;
  var $AcceptedEncoding;
  var $AcceptedCharset;

  var $search_engines = array(array('domain'=>".google.", 'key'=>'q'),array('domain'=>".yahoo.", 'key'=>'p'),array('domain'=>".live.", 'key'=>'q'),array('domain'=>"search.delfi.lt", 'key'=>'q'));
  
  
/**
*  Prints the debug message
*  @param $msg the debug message to print
*
*/
  function debug($msg) {
    echo ($this->debug==1) ? "\n<br>Debug: $msg": "";
  }

/**
*  Prints the array contents
*  @param $array  the array to print
*
*/
  function debug_r($array) {
    if($this->debug==1) {
      echo "<pre>";
      print_r($array);
      echo "</pre>";
    }
  }

/**
*  Constructor
*  @param $totalItems total number of items to show, (it is not the total no of pages!)
*  @param $itemsPerPage no of items shown per page
*/
  function __construct($server) {
    $this->SetVInfoServerVars($server);
  }

  function SetVInfoServerVars($serverVars) {
    $this->serverVars=$serverVars;
  }
  function GetVInfoIP() {
    //return the IP
     return $this->IP;
  }

  function GetVInfoKeyword() {
    //return the IP
     return $this->Keyword;
  }

  function GetVInfoRequestPage () {
    //return the REQUEST_URI
    return $this->RequestPage;
  }

  function GetVInfoURL() {
    //return the actual url(SCRIPT_NAME,QUERY_STRING)
    return $this->URL;
  }

  function GetVInfoRequestMethod() {
    //return the REQUEST_METHOD
    return $this->RequestMethod;
  }

  function GetVInfoUA() {
    //return the user agent sign
    return $this->UA;
  }

  function GetVInfoBrowser() {
    //return the Browser Name
    return $this->Broswer;
  }

  function GetVInfoBrowserVersion() {
    //return the Browser Name
    return $this->BroswerVersion;
  }

  function GetVInfoOS() {
    //return the Operating system
    return $this->OS;
  }


  function GetVInfoReferer() {
    //return the HTTP_REFERER
    return $this->Referer;
  }

  function GetVInfoCountry() {
    //return the country code
    return $this->CountryName;
  }

  function GetVInfoProxyUsed() {
    //return (true,false) whether proxy used or not
    return $this->ProxyUsed;
  }

  function GetVInfoProxyIP() {
    //return the IP's of proxies found
    return $this->ProxyIP;
  }

  function GetVInfoProxyCountry() {
    //return the Country detailsof proxies found
    return $this->ProxyCountry;
  }

  function GetVInfoProxySignature() {
    //return the Signatures of Proxy servers.
    return $this->ProxySignature;
  }

  function GetVInfoAcceptedLanguage() {
    //return the User Accepted Languages.
    return $this->AcceptedLanguage;
  }

  function GetVInfoAcceptedEncoding() {
    //return the User Accepted Encoding.
    return $this->AcceptedEncoding;
  }

  function GetVInfoAcceptedCharset() {
    //return the User Accepted Charset.
    return $this->AcceptedCharset;
  }
  
  function GetVInfoRefererDomain(){
  	return $this->RefererDomain;
  }
  
  function GetVInfoLocation(){
  	return $this->Location;
  }

  function GetVInfo () {
    //return all the info we have.

                    $ret_arr['ipaddress']                = $this->GetVInfoIP();
                    $ret_arr['request_page']       = $this->GetVInfoRequestPage ();
                    $ret_arr['url']               = $this->GetVInfoURL();
                    $ret_arr['RequestMethod']     = $this->GetVInfoRequestMethod();
                    $ret_arr['user_agent']                = $this->GetVInfoUA();
                    $ret_arr['browser']           = $this->GetVInfoBrowser();
                    $ret_arr['browser_version']    = $this->GetVInfoBrowserVersion();
                    $ret_arr['os']                = $this->GetVInfoOS();
                    $ret_arr['referer']           = $this->GetVInfoReferer();
                    $ret_arr['referer_domain']     = $this->GetVInfoRefererDomain();
                    $ret_arr['keyword']           = $this->GetVInfoKeyword();
                    $ret_arr['AcceptedLanguage']  = $this->GetVInfoAcceptedLanguage();
                    $ret_arr['AcceptedEncoding']  = $this->GetVInfoAcceptedEncoding();
                    $ret_arr['AcceptedCharset']   = $this->GetVInfoAcceptedCharset();
                    //$ret_arr['country']           = $this->GetVInfoCountry();
                    
                    $this->ExtractVInfoLocation();
					$location = $this->GetVInfoLocation();
                    
					foreach($location as $key=>$val){
						$ret_arr[$key] = $val;
					}
                    
                    if($this->GetVInfoProxyUsed()==1) {
                       $ProxyIP            = $this->GetVInfoProxyIP();
                       $ProxyCountry       = $this->GetVInfoProxyCountry();
                       $ProxySignature     = $this->GetVInfoProxySignature();
                       foreach($ProxyIP as $k=>$pIP) {
                          $ret_arr['ProxyDetail'][]=array('IP'=>$pIP,'Signature'=>$ProxySignature[$k],'Country'=>$ProxyCountry[$k]);
                       }
                    }
             return $ret_arr;

  }

  ///// now the functions for Extracting visitorInfo.

  function ExtractVInfoIP() {
    //return the IP
    $this->IP=$this->serverVars['REMOTE_ADDR'];
    $this->debug('Extracted IP:'.$this->IP);
  }

  function ExtractVInfoRequestPage () {
    //return the REQUEST_URI
    $this->RequestPage=$this->serverVars['REQUEST_URI'];
    $this->debug('Extracted RequestPage:'.$this->RequestPage);
  }

  function ExtractVInfoURL() {
    //return the actual url(SCRIPT_NAME,QUERY_STRING)
    $this->URL=$this->serverVars['SCRIPT_NAME'].'?'.$this->serverVars['QUERY_STRING'];
    $this->debug('Extracted URL:'.$this->URL);
  }

  function ExtractVInfoRequestMethod() {
    //return the request method (get,post ...)
    $this->RequestMethod=$this->serverVars['REQUEST_METHOD'];
    $this->debug('Extracted RequestMethod:'.$this->RequestMethod);
  }

  function ExtractVInfoUA() {
    //return the user agent
    $this->UA=$this->serverVars['HTTP_USER_AGENT'];
    $this->debug('Extracted UA:'.$this->UA);
  }

	function getBrowser() 
	{ 
	    $u_agent = $this->serverVars['HTTP_USER_AGENT']; 
	    $bname = 'Unknown';
	    $platform = 'Unknown';
	    $version= "";
	
	    //First get the platform?
	    if (preg_match('/linux/i', $u_agent)) {
	        $platform = 'linux';
	    }
	    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
	        $platform = 'mac';
	    }
	    elseif (preg_match('/windows|win32/i', $u_agent)) {
	        $platform = 'windows';
	    }
	    
	    // Next get the name of the useragent yes seperately and for good reason
	    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
	    { 
	        $bname = 'Internet Explorer'; 
	        $ub = "MSIE"; 
	    } 
	    elseif(preg_match('/Firefox/i',$u_agent)) 
	    { 
	        $bname = 'Mozilla Firefox'; 
	        $ub = "Firefox"; 
	    } 
	    elseif(preg_match('/Chrome/i',$u_agent)) 
	    { 
	        $bname = 'Google Chrome'; 
	        $ub = "Chrome"; 
	    } 
	    elseif(preg_match('/Safari/i',$u_agent)) 
	    { 
	        $bname = 'Apple Safari'; 
	        $ub = "Safari"; 
	    } 
	    elseif(preg_match('/Opera/i',$u_agent)) 
	    { 
	        $bname = 'Opera'; 
	        $ub = "Opera"; 
	    } 
	    elseif(preg_match('/Netscape/i',$u_agent)) 
	    { 
	        $bname = 'Netscape'; 
	        $ub = "Netscape"; 
	    } 
	    
	    // finally get the correct version number
	    $known = array('Version', $ub, 'other');
	    $pattern = '#(?<browser>' . join('|', $known) .
	    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	    if (!preg_match_all($pattern, $u_agent, $matches)) {
	        // we have no matching number just continue
	    }
	    
	    // see how many we have
	    $i = count($matches['browser']);
	    if ($i != 1) {
	        //we will have two since we are not using 'other' argument yet
	        //see if version is before or after the name
	        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
	            $version= $matches['version'][0];
	        }
	        else {
	            $version= $matches['version'][1];
	        }
	    }
	    else {
	        $version= $matches['version'][0];
	    }
	    
	    // check if we have a number
	    if ($version==null || $version=="") {$version="?";}
	    
	    return array(
	        'userAgent' => $u_agent,
	        'name'      => $bname,
	        'version'   => $version,
	        'platform'  => $platform,
	        'pattern'    => $pattern
	    );
	} 
  
  function ExtractVInfoBrowser() {
    //return the user agent
    $this->Broswer='Not Detected';
    $this->BroswerVersion='';
    $this->ExtractVInfoUA();
    $userAgentStr=$this->UA;
    
    $arr = $this->getBrowser();

    $this->Broswer=$arr['name'];
    $this->BroswerVersion=$arr['version'];
    
    /*
    if(($pos = strpos($userAgentStr,"MSIE")) !== false) {
        $this->Broswer='Internet Explorer';
        $pos+=strlen("MSIE")+1;
        $endPos=strpos($userAgentStr,";",$pos);
        $endPos = ($endPos===false) ? strlen($userAgentStr) : $endPos-$pos;
        $this->BroswerVersion = substr($userAgentStr,$pos,$endPos);
    } else if(($pos = strpos($userAgentStr,"Mozilla Firebird")) !== false) {
        $this->Broswer='Mozilla Firebird';
        $pos+=strlen("Mozilla Firebird")+1;
        $endPos=strpos($userAgentStr,";",$pos);
        $endPos = ($endPos===false) ? strlen($userAgentStr) : $endPos-$pos;
        $this->BroswerVersion = substr($userAgentStr,$pos,$endPos);
    } else if(($pos = strpos($userAgentStr,"Netscape")) !== false) {
        $this->Broswer='Netscape Navigator';
        $pos+=strlen("Netscape")+1;
        $endPos=strpos($userAgentStr," ",$pos);
        $endPos = ($endPos===false) ? strlen($userAgentStr) : $endPos-$pos;
        $this->BroswerVersion = substr($userAgentStr,$pos,$endPos);
    } else if(($pos = strpos($userAgentStr,"Mozilla")) !== false) {
        $this->Broswer='Mozilla';
        $pos+=strlen("Mozilla")+1;
        $endPos=strpos($userAgentStr," ",$pos);
        $endPos = ($endPos===false) ? strlen($userAgentStr) : $endPos-$pos;
        $this->BroswerVersion = substr($userAgentStr,$pos,$endPos);
    } else if(($pos = strpos($userAgentStr,"Opera")) !== false) {
        $this->Broswer='Opera';
        $pos+=strlen("Opera")+1;
        $endPos=strpos($userAgentStr," ",$pos);
        $endPos = ($endPos===false) ? strlen($userAgentStr) : $endPos-$pos;
        $this->BroswerVersion = substr($userAgentStr,$pos,$endPos);
    }
	*/
    
    $this->debug('Extracted Browser:'.$this->Broswer . ' - '.$this->BroswerVersion);
  }

  function ExtractVInfoOS() {
    //return the user agent
    $this->ExtractVInfoUA();
    $this->OS='Not Detected';
    $userAgentStr=$this->UA;

    if(eregi("Windows",$userAgentStr)) {
/*        $pos=strpos($userAgentStr,"Windows");
        $endPos=strpos($userAgentStr,";",$pos);
        $endPos=($endPos>0) ? $endPos: strpos($userAgentStr,")",$pos);
        echo '--------'.substr($userAgentStr,$pos,($endPos-$pos));
*/
        $this->OS='Windows';
    } else if(eregi("Linux",$userAgentStr)) {
        $this->OS='Linux';
    } else if(eregi("Unix",$userAgentStr)) {
        $this->OS='Unix';
    } else if(eregi("Mac",$userAgentStr)) {
        $this->OS='Macintosh';
    } else if(eregi("SunOS",$userAgentStr)) {
        $this->OS='SunOS';
    } else if(eregi("HP-UX",$userAgentStr)) {
        $this->OS='Unix';
    } else if(eregi("IRIX",$userAgentStr)) {
        $this->OS='Unix';

    }


    $this->debug('Extracted OS:'.$this->OS);
  }

  function ExtractVInfoReferer() {
    //return the HTTP_REFERER
    $this->Referer=$this->serverVars['HTTP_REFERER'];
    $this->debug('Extracted Referer:'.$this->Referer);
  }


  function ExtractVInfoLocation() {
    $serverDetail=$this->ServerDetails($this->serverVars['REMOTE_ADDR']);
	
	$this->Location = $serverDetail;

    $this->debug('Extracted Country:'.$this->C2Code.':'.$this->C3Code.':'.$this->CountryName);

  }

  function ExtractVInfoCountry() {
    $serverDetail=$this->ServerDetails($this->serverVars['REMOTE_ADDR']);

    $this->C2Code        =$serverDetail['c2code'];
    $this->C3Code        =$serverDetail['c3code'];
    $this->CountryName   =$serverDetail['country'];
    $this->debug('Extracted Country:'.$this->C2Code.':'.$this->C3Code.':'.$this->CountryName);

  }

  function ExtractVInfoProxyUsed() {
     //return (true,false) whether proxy used or not
     $this->ProxyUsed= ($this->serverVars['HTTP_VIA'] || $this->serverVars['HTTP_X_FORWARDED_FOR'] );
     $this->debug('Extracted ProxyUsed:'.$this->ProxyUsed);
  }

  function ExtractVInfoProxyIP() {
     //return the IP's of proxies found
     if($this->ProxyUsed) {
        $proxyArr=explode(",",$this->serverVars['HTTP_X_FORWARDED_FOR']);
        if(count($proxyArr)>1) {
           $this->ProxyIPArr=1; //// don't know why this is required, will be utilised/removed by next release
           $this->ProxyIP=$proxyArr;
           $this->debug('Extracted ProxyIP:'.$this->ProxyIPArr.':');
           $this->debug_r($this->ProxyIP);

           foreach($proxyArr as $k=>$server) {
             $serverDetail=$this->ServerDetails($server);
             
             $this->Location = $serverDetail;
				
             $this->ProxyCountry[$k]['C2Code']        =$serverDetail['c2code'];
             $this->ProxyCountry[$k]['C3Code']        =$serverDetail['c3code'];
             $this->ProxyCountry[$k]['CountryName']   =$serverDetail['country'];
           }
        } else {
           $this->ProxyIPArr=0; //// don't know why this is required, will be utilised/removed by next release
           $this->ProxyIP=array($proxyArr[0]);
           $this->debug('Extracted ProxyIP:'.$this->ProxyIPArr.':'.$this->ProxyIP);

           $serverDetail=$this->ServerDetails($proxyArr[0]);
           
           $this->Location = $serverDetail;

           $this->ProxyCountry[0]['C2Code']        =$serverDetail['c2code'];
           $this->ProxyCountry[0]['C3Code']        =$serverDetail['c3code'];
           $this->ProxyCountry[0]['CountryName']   =$serverDetail['country'];

        }
           $this->debug('Extracted Proxy Country:');
           $this->debug_r($this->ProxyCountry);
     }
  }

  function ExtractVInfoProxySignature() {
    //return the Signatures of Proxy servers.
     if($this->ProxyUsed) {
        $proxyArr=explode(",",$this->serverVars['HTTP_VIA']);
        if(count($proxyArr)>1) {
           $this->ProxySignatureArr=1; //// don't know why this is required, will be utilised/removed by next release
           $this->ProxySignature=$proxyArr;
           $this->debug('Extracted ProxySignature:'.$this->ProxySignatureArr.':');
           $this->debug_r($this->ProxySignature);
        } else {
           $this->ProxySignatureArr=0; //// don't know why this is required, will be utilised/removed by next release
           $this->ProxySignature=array($proxyArr[0]);
           $this->debug('Extracted ProxySignature:'.$this->ProxySignatureArr.':'.$this->ProxySignature);
        }

     }
  }

  function ExtractVInfoAcceptedLanguage() {
    //return the Accepted Languages
    if(isset($this->serverVars['HTTP_ACCEPT_LANGUAGE']) && !empty($this->serverVars['HTTP_ACCEPT_LANGUAGE'])) {
      $lArr=explode(',',$this->serverVars['HTTP_ACCEPT_LANGUAGE']);

      foreach($lArr as $k => $v) {
        $vArr=explode(";",$v);
        $vArr[1]=(!empty($vArr[1])) ? str_replace("q=","",$vArr[1]) : 1.0;
        $tmplCArr[$vArr[0]]=$vArr[1];

      }
      //$lDetailsArr=$this->LangDetails(array_keys($tmplCArr));
      foreach($tmplCArr as $lC=>$qV) {
        $langArr[]=array("langCode"=>$lC,"langName"=>$lDetailsArr[$lC],"langQValue"=>$qV);
      }
    } else {
      $langArr[]=array("langCode"=>'',"langName"=>"all languages are equally acceptable","langQValue"=>1.0);

    }

    $this->AcceptedLanguage=$langArr;
    $this->debug('Extracted Accepted Language:');
    $this->debug_r($this->AcceptedLanguage);
  }

  function ExtractVInfoAcceptedEncoding() {
    //return the Accepted Encoding
    if(isset($this->serverVars['HTTP_ACCEPT_ENCODING']) && !empty($this->serverVars['HTTP_ACCEPT_ENCODING'])) {
      $eArr=explode(',',$this->serverVars['HTTP_ACCEPT_ENCODING']);

      foreach($eArr as $k => $v) {
        $vArr=explode(";",$v);
        $vArr[1]=(!empty($vArr[1])) ? str_replace("q=","",$vArr[1]) : 1.0;
        $encArr[]=array("encoding"=>$vArr[0],"encodingQValue"=>$vArr[1]);
      }
    } else {
      $encArr[]=array("encoding"=>'',"encodingQValue"=>1.0);

    }

    $this->AcceptedEncoding=$encArr;
    $this->debug('Extracted Accepted Encoding:');
    $this->debug_r($this->AcceptedEncoding);
  }

  function ExtractVInfoAcceptedCharset() {
    //return the Accepted Charset
    if(isset($this->serverVars['HTTP_ACCEPT_CHARSET']) && !empty($this->serverVars['HTTP_ACCEPT_CHARSET'])) {
      $cArr=explode(',',$this->serverVars['HTTP_ACCEPT_CHARSET']);

      foreach($cArr as $k => $v) {
        $vArr=explode(";",$v);
        $vArr[1]=(!empty($vArr[1])) ? str_replace("q=","",$vArr[1]) : 1.0;
        $charsetArr[]=array("charset"=>$vArr[0],"charsetQValue"=>$vArr[1]);
      }
    } else {
      $charsetArr[]=array("charset"=>'',"charsetQValue"=>1.0);

    }

    $this->AcceptedCharset=$charsetArr;
    $this->debug('Extracted Accepted Charset:');
    $this->debug_r($this->AcceptedCharset);
  }
  
  function ExtractVInfoKeyword(){
  	foreach($this->search_engines as $key=>$val){
  		if(ereg($val['domain'], $this->RefererDomain)){
  			$arr = explode("?", $this->Referer);
  			$_arr = explode("&", $arr[1]);
  			foreach($_arr as $k=>$v){
  				$__arr = explode("=", $v);
  				if($__arr[0]==$val['key']){
  					$this->Keyword = urldecode($__arr[1]);
  					return;
  				}
  			}
  		}
  	}
  }
  
  function ExtractVInfoRefererDomain(){
	$arr = explode("/", $this->Referer);
	$this->RefererDomain = $arr[2];
  
  }

  function ExtractVInfo () {
    //return all the info we have.
      $this->ExtractVInfoIP();
      $this->ExtractVInfoRequestPage ();
      $this->ExtractVInfoURL();
      $this->ExtractVInfoRequestMethod();
      $this->ExtractVInfoUA();
      $this->ExtractVInfoBrowser();
      $this->ExtractVInfoOS();
      $this->ExtractVInfoReferer();
      $this->ExtractVInfoRefererDomain();
      $this->ExtractVInfoKeyword();

      $this->ExtractVInfoCountry();
      
      //$this->ExtractVInfoLocation();
      
      $this->ExtractVInfoProxyUsed();
      $this->ExtractVInfoProxyIP();
      $this->ExtractVInfoProxySignature();
      $this->ExtractVInfoAcceptedLanguage();
      $this->ExtractVInfoAcceptedEncoding();
      $this->ExtractVInfoAcceptedCharset();
      $this->needExtract=0;
      
  }

  function ServerDetails($serverName) {
     //return the country code
     
     if(function_exists("geoip_record_by_name") && $row = geoip_record_by_name($serverName)){
     	
	     $serverDetail['country_code']   = strtolower($row['country_code']);
	     $serverDetail['country']   = $row['country_name'];
	     $serverDetail['region']   = $row['region'];
	     $serverDetail['city']   = $row['city'];
	     $serverDetail['latitude']   = $row['latitude'];
	     $serverDetail['longitude']   = $row['longitude'];

     }else{
	     
	     $url = "http://freegeoip.appspot.com/xml/";
		$ch = curl_init($url.$serverName);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);       
        curl_close($ch);
        
		$xml = simplexml_load_string($output);
		$json = json_encode($xml);
		$row = json_decode($json,TRUE);
	    
        //$row = File::xmlStringToArray($output);
     	
	     $serverDetail['country_code']   = strtolower($row['CountryCode']);
	     $serverDetail['country']   = $row['CountryName'];
	     $serverDetail['region']   = $row['RegionCode'];
	     $serverDetail['city']   = $row['City'];
	     $serverDetail['latitude']   = $row['Latitude'];
	     $serverDetail['longitude']   = $row['Longitude'];
	     
     }
     
     return $serverDetail;
  }

}


?>