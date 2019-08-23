<?php
/**
 * User: adm
 * Date: 13.01.2015
 * Time: 14:12
 */

class Insert extends Db{
    private $order_id;
    private $service_id;
    private $contract;
    private $amount;

    function __construct($order_id,$service_id, $contract, $amount){
        $this->order_id = $order_id;
        $this->service_id = $service_id;
        $this->contract = $contract;
        $this->amount= $amount;

        $this->setQuery();
        parent::__construct();
    }

    function setQuery(){
        //  SYSDATE()
        $this->qwr = "INSERT INTO easyPays (OrderId, ServiceId, Account, Amount) VALUE ('$this->order_id', '$this->service_id', '$this->contract', '$this->amount')";
    }
}