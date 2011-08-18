<?php

class db 
{

	private static $_singleton;
	private $_connection;
	private $_db;
	private $_result;
	private $_rTable = array();
	
	protected $queriesCount = 0;
	protected $queriesTime = 0;
	protected $trace = array();
	
	public $debug = false;
	
	private function __construct($host, $user, $pass, $db){
		$this->_connection = mysql_connect($host, $user, $pass);
		$this->_db = mysql_select_db($db, $this->_connection);
	}
	
	static function getInstance($host, $user, $pass, $db){
		if (is_null (self::$_singleton)) {
			self::$_singleton = new db($host, $user, $pass, $db);
		}
		return self::$_singleton;
	}

	// 
	function escape($value){
		if(is_array($value)){
			foreach($value as $key=>$val){
				$value[$key] = $this->escape($val);
			} 
		}else{
			$value = mysql_real_escape_string($value, $this->_connection);
		} 
		return $value;
	}
	
	/*
	 * insert query
	 * table
	 * data = array()
	 * */
	function insert($table, $data){
		$data = $this->checkFields($table, $data);
		foreach($data as $column=>$value){
			$c_arr[] = $column;
			$v_arr[] = "'$value'";
		}
		$sql = "INSERT INTO $table (".implode(",", $c_arr).") VALUES (".implode(",", $v_arr).")";
		$this->exec($sql);
	}

	/*
	 * update query
	 * table
	 * data = array()
	 * where
	 * */
	function update($table, $data, $where=''){
		$data = $this->checkFields($table, $data);
		foreach($data as $column=>$value){
			$v_arr[] = "$column='$value'";
		}
		$sql = "UPDATE $table SET ".implode(",", $v_arr)." ".$where;
		$this->exec($sql);
	}
	
	function checkFields($table, $data){
		$new_data = array();
		if(!is_array($this->_rTable[$table])){
			$this->_rTable[$table] = $this->getFields($table);
		}
		foreach($this->_rTable[$table] as $i => $val){
			if(isset($data[$val['Field']])) $new_data[$val['Field']] = $data[$val['Field']];
		}
		return $new_data;
	}

	/*
	 * delete query
	 * table
	 * where
	 * */
	function delete($table, $where=''){
		$sql = "DELETE FROM $table $where";
		$this->exec($sql);
	}
	
	/*
	 * execute sql query
	 */ 	
	function exec($sql){
		if($this->debug == true){
			$startTime = microtime(true);
		}
		$this->_result = mysql_query($sql, $this->_connection) or die(mysql_error());
		if($this->debug == true){
			$this->queriesCount++;
			$endTime = microtime(true);
			$time = round($endTime - $startTime, 6);
			$this->queriesTime += $time;
			$this->trace['queries'][] = array('query'=>$sql, 'execute_time'=>$time);
		}
	}
	
	/*
	 * return mysql query results by array
	 */ 
	function arr($sql=''){
		
		$this->exec($sql);
		
		$arr = array();
		$n = $this->get_selected_rows_count();
		for($i=0; $i<$n; $i++){
			$arr[] = $this->row();
		}
		mysql_free_result($this->_result);
		return $arr;
	}
	
	/*
	 * return mysql query results by one row
	 */ 
	function row($sql=''){
		if($sql!='') $this->exec($sql);
		$row = mysql_fetch_assoc($this->_result);
		return $row;
	}
	
	/*
	 * return last insert id  
	 */
	function get_last_insert_id(){
		return mysql_insert_id();
	}

	/*
	 * return affected rows count  
	 */
	function get_affected_rows_count(){
		return mysql_affected_rows();
	}
	
	/*
	 * return selected rows count  
	 */
	function get_selected_rows_count(){
		return mysql_num_rows($this->_result);
	}
	
	/*
	 * 
	 */
	function getFields($table){
		$sql = "SHOW COLUMNS FROM $table";
		$list = $this->arr($sql);
		return $list;
	}
	
	function getTrace(){
		$this->trace['all'] = array('execute_time'=>$this->queriesTime, 'queriesCount'=>$this->queriesCount);
		return $this->trace;
	}
	
	function __destruct(){
		mysql_close($this->_connection);
	}
	
}

?>