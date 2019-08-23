<?php
/**
 * User: adm
 * Date: 12.01.2015
 * Time: 13:58
 */

class Main{
    /**
     * @var int $minBalance Minimal balance on account
     * @var int $count Start count
     * @var int $minAmount Minimal amount for payment
     */
    private $minBalance = 5;
    private $minAmount = "1.00";
    private $count = 0;

    function minAmount($amount){
        if($amount >= $this->minBalance)
            return $this->minAmount;
        else{
            for($grn = ($amount); $grn <= $this->minBalance; $grn++)
                $this->count += 1;

            return $this->count;
        }
    }

    function payment($aid, $contract, $pay_amount){
        $cmd = escapeshellcmd("/utm5/bin/utm5_payment_tool -a '$aid' -b '$pay_amount' -c 980 -m 108 -i 1 -L 'Плата за інтернет послуги згідно договору № $contract'");
        exec($cmd);
    }
    
    function sendSms( $number, $amount, $codeAllowed  ){
        if( in_array(substr($number, 0, 3), $codeAllowed) && strlen($number) == '10' ){
    	    mail('sms'.$number.'@gsm.local.domain', 'Payment', 'Vitaiemo. Vash abonentskyi rakhunok popovnenuj na '. $amount .' UAH. Z povahoiu JV Company name');
        }
    }
            

}