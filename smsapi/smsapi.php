<?php

class SMSApi{
	private $gateway, $uid, $pwd, $msg, $phone;
	
	
	function __construct($uid, $pwd, $phone, $msg) {
		$this->uid = $uid;
		$this->pwd = $pwd;
		$this->msg = $msg;
		$this->phone = $phone;
	}
	
	public function setGateway($str){
		$file = 'smsapi/api.'.$str . '.php';

		if (file_exists($file)){
			include $file;
			$this->gateway = new SmsGateway($this->uid, $this->pwd, $this->phone, $this->msg);
		}
		else
			die ('This is not built yet!');
	}
	
	public function sendSms(){
		$this->gateway->sendSms();
	}
}
?>