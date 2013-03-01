<?php
class MysqlDB {
	/**
	 *@resource connection
	*/
	private $connection;
	
	/**
	 *@array actions
	*/
	private $actions = array(
		'updated' => 0,
		'deleted' => 0,
		'inserted' => 0
	);
	
	/**
	 *@string needed
	*/
	private $needed;
	
	/**
	 *@array errors
	*/
	public $errors = false;
	
	/**
	 *@string cnam
	*/
	public $cnam = 'MysqlDB - ';
	
	
	/**
	 * public constructor
	 * Initializes the helper and makes connection
	 *
	 *@string host
	 *@string username
	 *@string password
	 *@string database
	 *
	*/
	public function __construct($host = null, $username = null, $password = null, $database = null) {
		$err = $this->cnam.'Constructor - ';
		
		if ($host == null || $username == null /*|| $password == null*/) {
			$connection = false;
			$this->errors[] = $err.'Please give all the needed values (host, username, password)';
			return false;
		}
		
		$con = mysql_connect($host, $username, $password) /*or die ('MysqlDB - Constructor - Unable to connect to MySQL')*/;
		$this->connection = $con;
		
		if($database == null) {
			$this->needed = 'dbname';
		}else{
			mysql_select_db($database, $this->connection) /*or die ('MysqlDB - Constructor - Unable to select DB')*/;
		}
		return true;
	}
	
	
	/**
	 * private init
	 * Checks if dbname is needed
	*/
	public function init() {
		if($this->needed == 'dbname') {
			$this->errors[] = 'You need to select db with self::sel_db';
			return false;
		}
		return true;
	}
	 
	
	/**
	 * public sel_db
	 * Selects new (or replaces the selected) database name
	 *
	 *@string database
	 *
	*/
	public function sel_db($database = null) {
		$err = $this->cnam.'sel_db - ';
		if ($database == null) {
			$this->errors[] = $err.'Database (first param) cannot be null';
			return false;
		}
		
		mysql_select_db($database, $this->connection) or die ('MysqlDB - sel_db - Unable to select DB');
		$this->needed = false;
		return true;
	}
	
	
	/**
	 * public query
	 * Makes $query query
	 *
	 *@string query
	 *
	*/
	public function query($query = null) {
		$err = $this->cnam.'query - ';
		if(!self::init()) {
			return false;
		}
		if($query == null) {
			$this->errors[] = $err.'Query (first param) cannot be null';
			return false;
		}
		
		$qry = mysql_query($query, $this->connection) or die ('MysqlDB - query - Unable to make query');
		return $qry;
	}
	
	/**
	 * public multi_query
	 * Makes a multi query
	 *
	 *@string query
	 *
	*/
	public function multi_query($queries = null) {
		$err = $this->cnam.'multi_query - ';
		if(!self::init()) {
			return false;
		}
		if(!is_array($queries)) {
			$this->errors[] = $err.'Queries (first param) needs to be array';
			return false;
		}
		
		$counter = 0;
		$result = null;
		foreach($queries as $query) {
			if(preg_match('/^INSERT|insert/', $query)) {
				$qry = mysql_query($query, $this->connection) or die ('MysqlDB - multi_query - Unable to make query');
				$this->actions['inserted'] += 1;
			}elseif(preg_match('/^DELETE|delete/', $query)){
				$qry = mysql_query($query, $this->connection) or die ('MysqlDB - multi_query - Unable to make query');
				$this->actions['deleted'] += 1;
			}elseif(preg_match('/^UPDATE|update/', $query)) {
				$qry = mysql_query($query, $this->connection) or die ('MysqlDB - multi_query - Unable to make query');
				$this->actions['updated'] += 1;
			}elseif(preg_match('/^SELECT|select/', $query)){
			
				$qry = mysql_query($query, $this->connection) or die ('MysqlDB - multi_query - Unable to make query');
				if (mysql_num_rows($qry) == 0) {
				}else{
					while($assoc = mysql_fetch_assoc($qry)) {
						$result[$counter][] = (object)$assoc;
					}
				}
				$counter++;
				
			}else{
				$qry = mysql_query($query, $this->connection) /*or die ('MysqlDB - multi_query - Unable to make query')*/;
			}
		}
		
		if($result != null)
			return $result;
	}
	
	
	/**
	 * public pagin_query
	 * Makes paginated $query query
	 *
	 *@string query
	 *@string from
	 *@string number
	 *
	*/
	public function pagin_query($query = null, $from = null, $number = null) {
		$err = $this->cnam.'pagin_query - ';
		if(!self::init()) {
			return false;
		}
		if ($query == null) {
			$this->errors[] = $err.'Query (first param) cannot be null';
			return false;
		}elseif($from == null) {
			$this->errors[] = $err.'From (second param) cannot be null';
			return false;
		}elseif($number == null) {
			$this->errors[] = $err.'Number (third param) cannot be null';
			return false;
		}
		
		// All its ok
		
		if(preg_match('/LIMIT\s*\d,\d/', $query)) {
			// The query has already limit? Replace it
			$qry = mysql_query(preg_replace('/LIMIT\s*\d,\d/', "LIMIT $from,$number", $query), $this->connection) or die
			('MysqlDB - pagin_query - Unable to make query');
			return $qry;
		}else{
			if (substr($query, -1) == ' ')
				$delim = '';
			else
				$delim = ' ';
			
			$qry = mysql_query($query.$delim."LIMIT $from,$number", $this->connection) or die ('MysqlDB - pagin_query - Unable
			to make query');
			return $qry;
		}
	}
	
	
	/**
	 * public get_row
	 * Gets a row from a SQL query
	 *
	 *@string query
	 *
	*/
	public function get_row($query = null) {
		$err = $this->cnam.'get_row - ';
		if(!self::init()) {
			return false;
		}
		if ($query == null) {
			$this->errors[] = $err.'Query (first param) cannot be null';
			return false;
		}
		
		if(preg_match('/LIMIT\s*\d*,\d*/', $query)) {
			// The query has already limit? Replace it
			$qry = mysql_query(preg_replace('/LIMIT\s(.*),\d*/', "LIMIT $1,1", $query), $this->connection) or die
			('MysqlDB - get_row - Unable to make query');
		}else{
			if (substr($query, -1) == ' ')
				$delim = '';
			else
				$delim = ' ';
			
			$qry = mysql_query($query.$delim."LIMIT 0,1", $this->connection) or die ('MysqlDB - get_row - Unable to make query');
		}
		
		$assoc = mysql_fetch_assoc($qry);
		return (object)$assoc;
	}
	
	
	/*
	 * public get_var
	 * Gets one var (data) from query
	 *
	 *@string query
	 *
	*/
	public function get_var($query = null) {
		$err = $this->cnam.'get_var - ';
		if(!self::init()) {
			return false;
		}
		if($query == null) {
			$this->errors[] = $err.'Query (first param) cannot be null';
			return false;
		}
		
		$qry = mysql_query($query, $this->connection) or die ('MysqlDB - get_var - Unable to make query');
		$assoc = mysql_fetch_assoc($qry);
		
		if(count($assoc) == 1)
			foreach($assoc as $key => $val)
				return $val;
		
		return $assoc;
	}
	
	
	/**
	 * public count
	 * Counts rows from a query
	 *
	 *@string query
	 *
	*/
	public function count($query = null) {
		$err = $this->cnam.'count - ';
		if(!self::init())
			return false;
			
		if($query == null) {
			$this->errors[] = $err.'Query (first param) cannot be null';
			return false;
		}
		
		$qry = mysql_query($query, $this->connection) or die ('MysqlDB - count - Unable to make query');
		return mysql_num_rows($qry);
	}
	
	
	/**
	 * public get_rows
	 * Gets array from query and converts to object
	 *
	 *@string query
	 *
	*/
	public function get_rows($query = null) {
		$err = $this->cnam.'get_rows - ';
		if(!self::init()) {
			return false;
		}
		if ($query == null) {
			$this->errors[] = $err.'Query (first param) cannot be null';
			return false;
		}
		
		$qry = mysql_query($query, $this->connection) or die ('MysqlDB - get_rows - Unable to make query');
		if (mysql_num_rows($qry) == 0)
			return false;
		
		$result = array();
		while($assoc = mysql_fetch_assoc($qry)) {
			$result[] = (object)$assoc;
		}
		
		return $result;
	}
	
	
	/**
	 * public show_tables
	 * Returns the tables in the selected db
	 *
	*/
	public function show_tables() {
		if(!self::init()) {
			return false;
		}
		$qry = mysql_query("SHOW TABLES", $this->connection) or die ('MysqlDB - show_tables - Unable to get results');
		
		$tmpRes = array();
		while($res = mysql_fetch_assoc($qry)) {
			foreach($res as $r => $val)
				$tmpRes[] = $val;
		}
		return $tmpRes;
	}
	
	/**
	 * public descrip
	 * Returns the full description of X table
	 *
	 *@string table
	 *
	*/
	public function descrip($table = null) {
		if(!self::init()) {
			return false;
		}
		$result = array();
		
		$err = $this->cnam.'descrip - ';
		if ($table == null) {
			$this->errors[] = $err.'Table (first param) cannot be null';
			return false;
		}
		
		$qry = mysql_query("DESC $table", $this->connection) /*or die ('MysqlDB - descrip - Unable to get description')*/;
		
		while($res = mysql_fetch_assoc($qry)) {
			$result['data'][] = $res;
		}
		return $result;
	}
	
	
	/**
	 * public insert
	 * Inserts data into MySQL
	 *
	 *@string table
	 *@array values
	 
	 VALUES NEEDS TO BE:
	 array (
		'colum' => 'value',
		'colum2' => 'value2', [.....]
	 )
	*/
	public function insert($table = null, $values = null, $values_opt = null) {
		$err = $this->cnam.'insert - ';
		if(!self::init()) {
			return false;
		}
		if($table == null) {
			$this->errors[] = $err.'Table (first param) cannot be null';
			return false;
		}
		if (!is_array($values)) {
			$this->errors[] = $err.'Values (second param) needs to be array';
			return false;
		}
		
		if($values_opt == null)
			$sql = "INSERT INTO $table".self::build_insert($values);
		elseif($values_opt != null && !is_array($values_opt)) {
			$this->errors[] = $err.'Values (third param) needs to be array. If you prefer, use first more. Read documentation';
			return false;
		}else
			$sql = "INSERT INTO $table".self::build_insert_valopt($values, $values_opt);
			
		mysql_query($sql, $this->connection) or die ('MysqlDB - insert - Unable to insert data');
		$this->actions['inserted'] += 1;
		return true;
	}
	
	
	/**
	 * public delete
	 * Deletes row in MySQL
	 *
	 *@string table
	 *@array where
	 *@int limit
	 *
	*/
	public function delete($table = null, $where = null, $limit = 0) {
		$err = $this->cnam.'delete - ';
		if(!self::init()) {
			return false;
		}
		if($table == null) {
			$this->errors[] = $err.'Table (first param) cannot be null';
			return false;
		}
		
		$sql = "DELETE FROM $table ";
		
		if($where != null) {
			if(!is_array($where))
				$sql .= "WHERE $where ";
			else
				$sql .= 'WHERE '.self::build_where($where).' ';
		}
		
		if ($limit > 0)
			$sql .= "LIMIT $limit";
		
		mysql_query($sql, $this->connection) or die ('MysqlDB - delete - Unable to delete data');
		$this->actions['deleted'] += 1;
		return true;
	}
	
	
	/**
	 * public update
	 * Updates row in MysQL
	 *
	 *@string table
	 *@array data
	 *@array where
	 *@int limit
	 *
	*/
	public function update($table = null, $data = null, $where = null) {
		$err = $this->cnam.'update - ';
		if(!self::init()) {
			return false;
		}
		if($table == null) {
			$this->errors[] = $err.'Table (first param) cannot be null';
			return false;
		}
		if($data != null && !is_array($data)) {
			$this->errors[] = $err.'Data (second param) needs to be array';
			return false;
		}
		if($where == null) {
			$this->errors[] = $err.'Where (third param) cannot be null';
			return false;
		}
		
		$sql = "UPDATE $table SET ".self::build_set($data);
		$sql .= " WHERE ".self::build_where($where);
		
		mysql_query($sql, $this->connection) or die ('MysqlDB - update - Unable to update data');
		$this->actions['updated'] += 1;
		return true;
	}
	
	
	/**
	 * private build_set
	 * Build the set param
	 * Called by self::update
	 *
	*/
	private function build_set($data) {
		if(!self::init()) {
			return false;
		}
		$end = null;
		foreach($data as $column => $value) {
			$end .= "$column = '$value', ";
		}
		return substr($end, 0, -2);
	}
	
	/**
	 * private build_where
	 * Build the where delete condition
	 * Called by self::delete
	 *
	*/
	private function build_where($where) {
		if(!self::init()) {
			return false;
		}
		$counter = 0;
		
		$end = null;
		foreach($where as $operator => $value) {
			if($counter == 0) {
				$end = $value;
				$counter++;
			}else{
				$end .= " $operator $value";
			}
		}
		return $end;
	}
	
	
	/**
	 * private build_insert
	 * Build the insert query (table, values)
	 * Called by self::insert
	 *
	*/
	private function build_insert($values) {
		if(!self::init()) {
			return false;
		}
		$ret = '(';
		foreach($values as $colum => $value) {
			$ret .= "`$colum`, ";
		}
		
		$ret = substr($ret, 0, -2).') VALUES (';
		
		foreach($values as $colum => $value) {
			if(!is_numeric($value))
				$ret .= "'$value', ";
			else
				$ret .= "$value, ";
		}
		
		return substr($ret, 0, -2).')';
	}
	
	/**
	 * private build_insert_valopt
	 * Builds a insert query in second mode
	 * Called by self::insert
	 *
	*/
	private function build_insert_valopt($columns, $values) {
		if(!self::init()) {
			return false;
		}
		
		$ret = '(';
		foreach($columns as $column) {
			$ret .= "`$column`, ";
		}
		$ret = substr($ret, 0, -2).') VALUES (';
		
		foreach($values as $value) {
			if(!is_numeric($value))
				$ret .= "'$value', ";
			else
				$ret .= "$value, ";
		}
		return substr($ret, 0, -2).')';
	}
	
	
	/**
	 * public function escape
	 * Escapes string. Prevents SQLi
	 *
	 *@string string
	 *
	*/
	public function escape($string = null) {
		$err = $this->cnam.'escape - ';
		if(!self::init()) {
			return false;
		}
		if($string == null) {
			$this->errors[] = $err.'String (first param) cannot be null';
			return false;
		}
		
		$string = mysql_real_escape_string($string, $this->connection);
		return $string;
	}
	
	/**
	 * public get_actions
	 * Returns array with all the actions did
	 *
	*/
	public function get_actions() {
		$result = $this->actions;
		$biggest = false;
		foreach($this->actions as $type => $value) {
			//Make array...
			if($type == 'updated')
				$result[$type] == "$value rows updated";
			elseif($type == 'deleted')
				$result[$type] == "$value rows deleted";
			else
				$result[$type] == "$value rows inserted";
			
			if($value > 0)
				$biggest = true;
		}
		
		if ($biggest == true)
			return $result;
		else
			return 'No actions did';
	}
}