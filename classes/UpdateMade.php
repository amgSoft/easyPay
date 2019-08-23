<?php
/**
 * User: adm
 * Time: 15:30
 */
class UpdateMade extends Db{
    private $payment_id;

    function __construct($payment_id){

        $this->payment_id = $payment_id;

        $this->setQuery();
        parent::__construct();
    }

    function setQuery(){
        //  SYSDATE()
        $this->qwr = "UPDATE easyPays SET Made = '1' WHERE PaymentId = $this->payment_id";
    }
}