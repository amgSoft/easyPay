<?php
/**
 * User: adm
 * Time: 13:55
 */
class GetPayment extends Db{
    private $payment_id;

    function __construct($payment_id){
        $this->payment_id = $payment_id;
        $this->setQuery();

        parent::__construct();

    }

    function setQuery(){
        $this->qwr = "SELECT DATE_FORMAT(p.OrderDate, '%Y-%m-%d\T%T') AS OrderDate, DATE_FORMAT(p.CancelDate, '%Y-%m-%d\T%T') AS CancelDate, p.Account AS Account, p.Amount AS Amount, p.Made AS Made FROM easyPays p WHERE p.PaymentId = $this->payment_id LIMIT 1";
    }

    /**
     * @param string $param "Name of property which need to return"
     * @return array
     */
    function __get($param){
        return $this->$param;
    }

}