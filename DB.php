<?php

class MDB3Statement extends PDOStatement {
	public $dbh;
    protected function __construct($dbh) {
        $this->dbh = $dbh;
    }
	function fetchRow() {
		return $this->fetch();
	}
	function numRows() {
		return $this->rowCount();
	}
}

class MDB3 extends PDO {
	function __construct($dsn, $username="", $password="", $driver_options=array()) {
        parent::__construct($dsn,$username,$password, $driver_options);
        $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('MDB3Statement', array($this)));
    }
	function query($sql, $params = NULL) {
		global $ALOCAL;

		if ($ALOCAL['debug']) error_log($sql, 3, '/tmp/php.log');
		$stmt = parent::prepare($sql);
		$stmt->execute($params);
		return $stmt;
	}
	function getAll($query) {
		$stmt = $this->query($query);
		return $stmt->fetchAll();
	}
	function setFetchMode($mode) {
        if (DB_FETCHMODE_ASSOC === $mode) 
			$this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	}
	function setOption($option,$val) {
        if ('persistent' === $option) 
			$this->setAttribute(PDO::ATTR_PERSISTENT, $val);
	}
	function autoCommit($autocommit) {
		if (! $autocommit && ! $this->inTransaction()) $this->beginTransaction();
	}
	function slaveOK()
	{
		$resultat = $this->query('show slave status');
		$etat = $resultat->fetch();
		if ($etat && $etat['Slave_IO_Running'] == 'Yes' && $etat['Slave_SQL_Running'] == 'Yes') return TRUE;
		return FALSE;
	}
}

class PEAR
{
	static function isError($obj)
	{
		return FALSE;
	}
}

class DB extends PEAR
{
    static function &connect($dsn, $options = array())
    {
		if (! is_array($dsn) && strpos($dsn,'//')) {
			$parts = explode('/', $dsn);
			list($dsna['phptype']) = explode(':', $parts[0]);
			list($creds , $dsna['hostspec']) = explode('@', $parts[2]);
			list($dsna['username'], $dsna['password']) = explode(':', $creds);
			$dsna['database'] = $parts[3];
			$dsn = $dsna;
		}
		if (is_array($dsn)) $obj = new MDB3($dsn['phptype'].':host='.$dsn['hostspec'].';dbname='.$dsn['database'], $dsn['username'], $dsn['password']);
		else $obj = new MDB3($dsn);
		$obj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$obj->dsn = $dsn;
        return $obj;
    }
}

if (! function_exists('mysql_connect')) {

function mysql_connect($server, $login, $password) {
	global $DSN, $DB;
	if ($DB instanceof MDB3) return TRUE;

	$DSN = array('phptype' => 'mysql',
			'username' => $login,
			'password' => $password,
			'hostspec' => $server);
	try {
		$DB = new PDO($DSN['phptype'].':host='.$DSN['hostspec'], $DSN['username'], $DSN['password']);
		$DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (Exception $e) {
		return FALSE;
	}
	return true;
}
function mysql_select_db($db) {
	global $DSN, $DB;
	if ($DB instanceof MDB3) return TRUE;

	$DSN['database'] = $db;
	try {
		$DB = new PDO($DSN['phptype'].':host='.$DSN['hostspec'].';dbname='.$DSN['database'], $DSN['username'], $DSN['password']);
		$DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (Exception $e) {
		return FALSE;
	}
	return true;
}
function mysql_query($query) {
	global $DB;

	return $DB->query($query);
}
function mysql_num_rows($statement) {
	return $statement->rowCount();
}
function mysql_fetch_array($statement, $params = NULL) {
	return $statement->fetch();
}
function mysql_fetch_assoc($statement, $params = NULL) {
	return $statement->fetch();
}
function mysql_error($statement = NULL) {
	return 'No Error Today';
}

}
