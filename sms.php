<?php
include 'smsapi/smsapi.php';
$sms = new SMSApi($_REQUEST['uid'],$_REQUEST['pwd'],$_REQUEST['phone'],$_REQUEST['msg']);
$sms->setGateway($_REQUEST['gateway']);
$sms->sendSms();
?>
