<?php
class Model {
	private $host;
	private $password;
	private $user;
	private $db;
	public $sql;
	public $result;

	function setConnection($host,$user,$password) {
		$this->host = $host;
		$this->user = $user;
		$this->password = $password;
		return mysql_connect("$this->host","$this->user","$this->password");
	}
	
	function selectDB($xdb) {
		$this->db = $xdb;
		return mysql_select_db($this->db);
	}
	
	function cekDB($xdb){
		$this->db = $xdb;
		$this->sql = "CREATE DATABASE IF NOT EXISTS $xdb";
		$this->result = mysql_query($this->sql);
		return $this->result;
	}
	
	function generateId($tabel,$field){
		$qry	= mysql_query("SELECT MAX(".$field.") AS maxid FROM ".$tabel);
		$data	= mysql_fetch_array($qry); 
		$n = $data['maxid'];
		return ++$n;
	}
	
	function searchKey($table,$column,$key,$other=false){
		//(tabel, kolom, katakunci, other)
		$this->sql = "select * from $table where $column like '%$key%' $other";
		$this->result = mysql_query($this->sql);
	}
	
	function selectTable($table,$other=false){
		//(tabel, other)
		$this->sql = "select * from $table $other";
		$this->result = mysql_query($this->sql);
	}
	
	
/* //sementara belum dimanfaatkan 
	function insertTable($table,$values){
	//insert into table values(null,now(),'hasil post','hasil post');
		$this->sql = "insert into $table values ($values)";
		$this->result = mysql_query($this->sql);
	}
	function insertColumn($table,$columns,$values){
		//insert into table(id,nama) values (null,'hasil post');
		$this->sql = "insert into $table ($columns) values ($values)";
		$this->result = mysql_query($this->sql);
	}
	function updateTableId($table,$columnvalue,$klausa){
		$this->sql = "update $table set $columnvalue where $klausa";
		$this->result = mysql_query($this->sql);
	}
*/
}
?>