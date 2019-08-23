#!/usr/bin/php

<?php
//ini_set('display_errors', 1);
//ini_set('error_reporting', E_ALL);

// Including config file
require_once "conf/conf.php";

// Incoming data
$in = file_get_contents('php://stdin');

// Symbolic for replace
$pattern = array("\n", "\r");

// Replacing
$inData = str_replace($pattern, '', $in);

//Get remote addr, which posted request
$remote_ip = $_SERVER['REMOTE_ADDR'];

//Check if ipaddr is allow go next or exit
    if(!in_array($remote_ip, $allow_ip)) {
        $time_stamp = date("Y.m.d H:i:s");

        $xmlIn = new SimpleXMLElement($inData);
        $xmlOut = new SimpleXMLElement(loadXml("Response"));

        if(isset($xmlIn)) {
            $clientInfo = "";

           foreach($xmlIn->children() as $child):
               $type_request = $child->getName();

               if(!in_array($type_request, $allow_request)) continue;

               switch($type_request){
//It's checking opportunity to make payment and show info by client
                   case 'Check':
                       $contract = clear($xmlIn->Check->Account);
                       $obj_GetInfo = new GetInfo($contract);

                       $clientInfo = $obj_GetInfo->data;
                       if(!empty($clientInfo)){
                               $xmlOut->addChild('StatusCode', 0);
                               $xmlOut->addChild('StatusDetail', 'OK');
                               $xmlOut->addChild('time_stamp', $time_stamp);
                               $xmlOut->addChild('Sign');

                               $AccountInfo = $xmlOut->addChild('AccountInfo');
                               $AccountInfo->addChild('Name', $clientInfo['fio']);
                               $AccountInfo->addChild('Address', $clientInfo['addr']);
                               $AccountInfo->addChild('Address', $clientInfo['balance']);
                       }else{
                           $xmlOut->addChild('status_code', '-1');
                           $xmlOut->addChild('StatusDetail', 'Client did not find');
                           $xmlOut->addChild('time_stamp', $time_stamp);
                           $xmlOut->addChild('Sign');
                       }
                       break;
//Request to make payment
                   case 'Payment':
                       $order_id = clear($xmlIn->Payment->OrderId);
                       $obj_payID = new GetPayID($order_id);

                       if(empty($obj_payID->data)){
                           $contract = clear($xmlIn->Payment->Account);
                           $obj_GetInfo = new GetInfo($contract);
                           $clientInfo = $obj_GetInfo->data;

                           if(!empty($clientInfo)) {

                               $service_id = clear($xmlIn->Payment->ServiceId);
                               $amount = clear($xmlIn->Payment->Amount);

                               //to Db log
                               new Insert($order_id,$service_id, $contract, $amount);

                               // повторно вызиваем обьект для получения paymentId (проверка создания заказа)
                               $obj_payID = new GetPayID($order_id);
                               $paymentId = $obj_payID->data;

                               if(isset($paymentId['PaymentId'])){
                                   $xmlOut->addChild('StatusCode', 0);
                                   $xmlOut->addChild('StatusDetail', 'Order Created');
                                   $xmlOut->addChild('DateTime', $time_stamp);
                                   $xmlOut->addChild('Sign');
                                   $xmlOut->addChild('PaymentId', $paymentId['PaymentId']);

                               } else {
                                   $xmlOut->addChild('StatusCode', -2);
                                   $xmlOut->addChild('StatusDetail', 'Order did not  Create');
                                   $xmlOut->addChild('DateTime', $time_stamp);
                                   $xmlOut->addChild('Sign');
                               }

                           }else{
                               $xmlOut->addChild('status_code', '-1');
                               $xmlOut->addChild('StatusDetail', 'Client did not find');
                               $xmlOut->addChild('time_stamp', $time_stamp);
                               $xmlOut->addChild('Sign');
                           }
                       }else{
                           $xmlOut->addChild('status_code', '-3');
                           $xmlOut->addChild('StatusDetail', 'Payment already exist');
                           $xmlOut->addChild('time_stamp', $time_stamp);
                           $xmlOut->addChild('Sign');
                       }

                       break;
//It's checking the status of the payment
                   case 'Confirm':
                       $payment_id = clear($xmlIn->Confirm->PaymentId);
                       new Update('OrderDate',$payment_id);

                       $obj_get_payment_data = new GetPayment($payment_id);
                       $payment_data = $obj_get_payment_data->data;
                       if(!empty($payment_data)){
                           $obj_GetInfo = new GetInfo($payment_data['Account']);
                           $clientInfo = $obj_GetInfo->data;

                           if($payment_data['Made'] === 0 ){
                               $obj_main = new Main();
                               //Set payment
                               $obj_main->payment($clientInfo['aid'], $payment_data['Account'], $payment_data['Amount']);

                               //Setnd sms
                               $patternForNumber = array('(', ')', '-', '+',' ');

                               if(!empty($clientInfo['mob_phone'])){
                                   $phone = str_replace($patternForNumber, '', $clientInfo['mob_phone']);
                                   $phone = substr(trim($phone), '-10');
                                   if(strlen($phone) == '9')
                                       $phone = str_pad($phone, 10, '0', STR_PAD_LEFT);

                                   $obj_main->sendSms($phone, $payment_data['Amount'], $codeAllowed);
                               }
                           }



                           $xmlOut->addChild('StatusCode', 0);
                           $xmlOut->addChild('StatusDetail', 'Payment Confirmed');
                           $xmlOut->addChild('DateTime', $time_stamp);
                           $xmlOut->addChild('Sign');
                           $xmlOut->addChild('OrderDate', $payment_data['OrderDate']);

                       }else{
                           $xmlOut->addChild('status_code', '-4');
                           $xmlOut->addChild('StatusDetail', 'Payment did not find');
                           $xmlOut->addChild('DateTime', $time_stamp);
                           $xmlOut->addChild('Sign');
                       }

                       break;
                   case 'Cancel':
                       $payment_id = clear($xmlIn->Cancel->PaymentId);
                       new Update('CancelDate',$payment_id);
                       mail($mail_addr, "Canceled payment ", "EasyPay for payment_id = $payment_id");

                       $obj_get_payment_data = new GetPayment($payment_id);
                       $payment_data = $obj_get_payment_data->data;

                       $xmlOut->addChild('StatusCode', 0);
                       $xmlOut->addChild('StatusDetail', 'Payment Canceled');
                       $xmlOut->addChild('DateTime', $time_stamp);
                       $xmlOut->addChild('Sign');
                       $xmlOut->addChild('CancelDate', $payment_data['CancelDate']);

                       break;
//Default condition
                   default:
                       $xmlOut->addChild('status_code', '-5');
                       $xmlOut->addChild('StatusDetail', 'Incorrect Request');
                       $xmlOut->addChild('DateTime', $time_stamp);
                       $xmlOut->addChild('Sign');
               }

           endforeach;
        } else{
            $xmlOut->addChild('status_code', '-5');
            $xmlOut->addChild('StatusDetail', 'Incorrect Request');
            $xmlOut->addChild('time_stamp', $time_stamp);
            $xmlOut->addChild('Sign');
        }
        echo $xmlOut->asXML();
    }else
	    exit;

