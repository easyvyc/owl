<?php 

class table extends basic 
{
	
	protected $table;
	
	function __construct($table){
		
		$basic = basic::getInstance();
		
		$this->db = $basic->db;
		$this->config = $basic->config;
		
		$this->table = $this->config['db_prefix'].$table;
		
		//$this->fields = $this->db->getFields($this->table);
		
	}
	
	function load($id){
		return $this->db->arr("SELECT * FROM $this->table WHERE id=$id");
	}
	
	function insert($data){
		$this->db->insert($this->table, $data);
		return $this->db->get_last_insert_id();
	}
	
	function update($id, $data){
		$this->db->update($this->table, $data, " WHERE id=$id ");
		return $id;
	}
	
	function delete($id){
		$this->db->delete($this->table, " WHERE id=$id ");
	}
	
}

?>