<?php
/**
 * Config file for easyPay
 * User: adm
 * Date: 02.12.2014
 * Time: 14:48
 */
date_default_timezone_set('Europe/Kiev');

include_once "lang/ua.php"; //Подключаем языковой файл

define("DB_HOST", "localhost");
define("DB_NAME", "dbname");
define("DB_USER", "dbuser");
define("DB_PASS", "dbpass");
/*Secret code for hash */
define("SECRET", "your secret code");

/*Ip addr which allow to send request*/
$allow_ip = array('192.168.0.30', '193.10.5.10');

$allow_request = array('Check', 'Payment', 'Confirm', 'Cancel');

/*Configure email addr*/
$mail_addr = "admin@domain.com";

/**
 * @method __autoload() Загрузка файла класса при инициализации обьекта
 * @param $class_name Название класса
 */
function __autoload($cName) {
    $fName = "classes/" . $cName . ".php";
    require_once $fName;
}

function loadXml($fXml){
    $fName = file_get_contents("xml/".$fXml.".xml");
    return  $fName;
}

function clear($data){
    $data = trim(htmlspecialchars($data, ENT_QUOTES));
    return $data;
}

/* Codes of mobile operator for send message */
$codeAllowed = array('050', '099', '095', '066', '067','068', '096', '097', '098');
