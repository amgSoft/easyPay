<?php
/**
 * User: adm
 * Date: 12.01.2015
 * Time: 14:42
 */

class GetPayID extends Db{
    private $payID;

    function __construct($pay_id){
        $this->payID = $pay_id;
        $this->setQuery();

        parent::__construct();

    }

    function setQuery(){
        $this->qwr = "SELECT PaymentId, OrderId from easyPays WHERE OrderId = '$this->payID' LIMIT 1";
    }

    /**
     * @param string $param "Name of property which need to return"
     * @return array
     */
    function __get($param){
        return $this->$param;
    }

}