<?php 

class messages extends table 
{
	
	function __construct(){
		
		table::__construct(__CLASS__);
		
	}
	
	function load($id){
		if(!is_numeric($id)) return false;
		$sql = "SELECT * FROM $this->table WHERE id=$id";
		return $this->db->row($sql);
	}
	
	function send($site_id, $visit_id, $msg){
		if(!is_numeric($visit_id)) return false;
		if(!is_numeric($site_id)) return false;
		$msg = $this->db->escape($msg);
		$sql = "INSERT INTO $this->table SET website_id=$site_id, visitor_id=$visit_id, message='$msg', direction=".(isset($_SESSION['talker_admin'])?0:1).", ".(isset($_SESSION['talker_admin'])?"admin_id={$_SESSION['talker_admin']['id']}, name='{$_SESSION['talker_admin']['name']}', ":"")." readed=0, create_date=NOW()";
		$this->db->exec($sql);
		return $this->db->get_last_insert_id();
	}
	
	function setReaded($id){
		$sql = "UPDATE $this->table SET readed=1 WHERE id=$id";
		$this->db->exec($sql);
	}
	
	function checkAdmin($site_id){
		if(!is_numeric($site_id)) return false;
		$sql = "SELECT * FROM $this->table WHERE website_id=$site_id AND readed!=1 AND direction=1";
		return $this->db->arr($sql);
	}

	function checkUser($site_id, $visit_id){
		if(!is_numeric($site_id)) return false;
		if(!is_numeric($visit_id)) return false;
		$sql = "SELECT * FROM $this->table WHERE website_id=$site_id AND visitor_id=$visit_id AND readed!=1 AND direction=0";
		return $this->db->arr($sql);
	}
	
	function listHistory($site_id, $visit_id){
		if(!is_numeric($site_id)) return false;
		if(!is_numeric($visit_id)) return false;
		$sql = "SELECT * FROM $this->table WHERE website_id=$site_id AND visitor_id=$visit_id";
		return $this->db->arr($sql);
	}
	
}

?>