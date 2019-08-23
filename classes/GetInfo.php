<?php
/**
 * User: adm
 * Date: 03.12.2014
 * Time: 12:45
 */

class GetInfo extends Db{
    /**
     *@param string $contract  "Contract's number"
     */
    private $contract;

    function __construct($contract){
        $this->contract = $contract;
        $this->setQuery();

        parent::__construct();
    }
    function setQuery(){
        $this->qwr = "SELECT aid, fio, addr, balance, mob_phone, contract, service_id from get_info_easyPay WHERE contract = '$this->contract' LIMIT 1";
    }

    /**
     * @param string $param "Name of property which need to return"
     * @return array
     */
    function __get($param){
        return $this->$param;
    }
}