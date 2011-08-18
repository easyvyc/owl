<?php 

class websites extends table
{
	
	function __construct(){

		table::__construct(__CLASS__);
		
	}
	
	function listWebsites(){
		
		$sql = "SELECT * FROM $this->table";
		return $this->db->arr($sql);
		
	}
	
	
}


?>
