<?php
/**
 * Created by PhpStorm.
 * User: adm
 * Date: 08.01.2015
 * Time: 11:21
 * @param string $qwr "Шаблон запроса"
 * @param array $data "Массив для получения результата"
 */

abstract class Db{
    private static $db_host = DB_HOST;
    private static $db_name = DB_NAME;
    private static $db_user = DB_USER;
    private static $db_pass = DB_PASS;
    
    private $patternQwr = '/^INSERT|UPDATE|DELETE/';
    private $typeQwr;

    protected $qwr;
    protected $data = array();

    protected function __construct(){
        $dbc = new mysqli(self::$db_host, self::$db_user, self::$db_pass, self::$db_name);

        if($dbc->connect_errno)
            die($dbc->connect_error);
        else{
	    $dbc->query("SET NAMES utf8");
	    $stmt = $dbc->query($this->qwr);
	    
	    preg_match($this->patternQwr, strtoupper($this->qwr), $this->typeQwr);
	    if(empty($this->typeQwr[0])){
		$this->data = $stmt->fetch_assoc();
		$stmt->close();
	    }
            $dbc->close();
        }
    }

    abstract function setQuery();

}