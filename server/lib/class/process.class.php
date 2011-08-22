<?php 

class process extends basic 
{
	
	function __construct(){
		
		basic::getInstance();
		
		$this->websites = new websites();
		$this->visitors = new visitors();
		$this->messages = new messages();
		
	}
	
	function start(){
		
		header("Content-Type: text/html; charset=utf-8");
		ob_flush();
		$_SESSION['talker_admin'] = true;
		return file_get_contents(VIEWSDIR."index.tpl");
		
	}
	
	function loadSites(){
		
		return json_encode($this->websites->listWebsites());
		
	}
	
	function registerVisitor($p){
		return $this->visitors->registerVisitor($p['site_id'], $p['visit_id'], $p['referer'], $p['url']);
	}
	
	function lastActions($p){

		$arr = $this->visitors->lastActions($p['site_id'], $p['time']);
		//pa($arr);
		if(!empty($arr)) $arr[0]['time'] = $this->getTime();
		return json_encode($arr);

	}

	function loadMessage($id){
		
		return $this->messages->load($id);

	}

	function sendMessage($p){
		
		$id = $this->messages->send($p['site_id'], $p['visit_id'], $p['msg'], ($_SESSION['talker_admin']==1?0:1), ($_SESSION['talker_admin']==1?1:0));
		return json_encode($this->messages->load($id));
		
	}
	
	function setReaded($id){
		
		$this->messages->setReaded($id, true);

	}
	
	// for admin
	function checkMessage($p){
		
		$arr = $this->messages->checkAdmin($p['site_id']);
		foreach($arr as $i=>$val){
			$this->messages->setReaded($val['id']);
		}
		if(!empty($arr)) $arr[0]['time'] = $this->getTime();
		return json_encode($arr);
		
	}
	
	// for client
	function getNewMessages($p){
		
		$this->visitors->setOnline($p['site_id'], $p['visit_id']);
		
		$arr = $this->messages->checkUser($p['site_id'], $p['visit_id'], $p['time']);
		foreach($arr as $i=>$val){
			$this->messages->setReaded($val['id']);
		}
		$info['time'] = $this->getTime();
		$info['online'] = $this->getOnline();
		
		array_unshift($arr, $info);
		
		return json_encode($arr);
				
	}
	
	function getOldMessages($p){
		$arr = $this->messages->listHistory($p['site_id'], $p['visit_id']);
		if(!empty($arr)) $arr[0]['time'] = $this->getTime();
		return json_encode($arr);
		
	}
	
	function getTime(){
		return date("Y-m-d H:i:s");
	}	
	
	function getOnline(){
		// TODO: padaryti kad butu galima kazkaip nustatyti ar adminas online
		return 1;
	}
	
}

?>
