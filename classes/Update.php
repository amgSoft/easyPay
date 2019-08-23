<?php
/**
 * User: adm
 * Time: 13:44
 */
class Update extends Db{
    private $column;
    private $payment_id;

    function __construct($column,$payment_id){
        $this->column = $column;
        $this->order_id = $payment_id;

        $this->setQuery();
        parent::__construct();
    }

    function setQuery(){
        //  SYSDATE()
        $this->qwr = "UPDATE easyPays SET $this->column = NOW() WHERE PaymentId = $this->porder_id";
    }
}