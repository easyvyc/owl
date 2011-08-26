<?php 

class admins extends table 
{
	
	private $online_period = 5; // minutes
	
	function __construct(){
		
		table::__construct(__CLASS__);
		
	}
	
	function is_online(){
		
		$sql = "SELECT * FROM $this->table WHERE DATE_ADD(online_time, INTERVAL $this->online_period MINUTE)>NOW()";
		$arr = $this->db->arr($sql);
		return (empty($arr)?false:true);
		
	}
	
	function registerOnline($admin_id){
		$sql = "UPDATE $this->table SET online_time=NOW() WHERE id=$admin_id";
		$this->db->exec($sql);
	}
	
}
	
?>