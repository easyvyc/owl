<?php 

class visitors extends table 
{
	
	function __construct(){
		
		table::__construct(__CLASS__);
		
		$this->visitor_path = new table("visitor_path");
		
	}
	
	function lastActions($site_id, $time){
		if(!is_numeric($site_id)) return false;
		$this->db->escape($time);
		$sql = "SELECT V.*, V.visit_time AS start_time ". 
    			" FROM $this->table V " .
    			" WHERE V.website_id=$site_id AND V.online_time>DATE_SUB(NOW(), INTERVAL 2 MINUTE) " .
				" ORDER BY V.online_time DESC ";
   		return $this->db->arr($sql);
   	}
	
	function setOnline($site_id, $visit_id){
		if(!is_numeric($site_id)) return false;
		if(!is_numeric($visit_id)) return false;
		$sql = "UPDATE $this->table SET online_time=NOW() WHERE website_id=$site_id AND id=$visit_id";
		$this->db->exec($sql);
	}
	
	function registerVisitor($site_id, $visit_id, $referer, $url){
		if(!is_numeric($site_id)) return false;
		$_SERVER['HTTP_REFERER'] = urldecode($referer);
		$this->visitorInfo = new visitorInfo($_SERVER);
		if(is_numeric($visit_id)){
			$this->visitorInfo->ExtractVInfo();
			$visitor_info = $this->visitorInfo->GetVInfo();
			$path['visitor_id'] = $visit_id;
			$path['visit_time'] = date("Y-m-d H:i:s");
			$path['url'] = $url;
			$this->visitor_path->insert($path);
			$visitor = $this->load($visit_id);
			$this->update($visit_id, array('page_count'=>$visitor['page_count']+1));
		}else{
			$this->visitorInfo->ExtractVInfo();
			$visitor_info = $this->visitorInfo->GetVInfo();
			$visitor_info['website_id'] = $site_id;
			$visitor_info['visit_time'] = date("Y-m-d H:i:s");
			$visitor_info['session_id'] = session_id();
			$visitor_info['page_count'] = 1;
			$visit_id = $this->insert($visitor_info);
			$path['visitor_id'] = $visit_id;
			$path['visit_time'] = date("Y-m-d H:i:s");
			$path['url'] = $url;
			$this->visitor_path->insert($path);
		}
		$this->setOnline($site_id, $visit_id);
		return $visit_id;
	}
	
}

?>