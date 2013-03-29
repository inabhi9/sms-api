<?php
include 'common.php';

interface SmsAction{
	function doLogin();
	function sendSms();
	function varEncoding();
}

abstract class SmsSkel{
	protected $uid, $pwd, $msg, $phone, $total_phone, $curl;
	
	protected function validation(){
		$this->_validMsg();
		$this->_validPhone();
		$this->_validPwd();
		$this->_validUID();
		$this->total_phone = count(explode(",",$this->phone));
	}
	
	private function _validUID(){
		$res = Common::basicValidation($this->uid);
		if ($res == false) Common::throwMsg(101, StatusCode::_101);
	}
	
	private function _validPwd(){
		$res = Common::basicValidation($this->pwd);
		if ($res == false) Common::throwMsg(101, StatusCode::_101);
	}
	
	private function _validMsg(){
		$res = Common::basicValidation($this->msg);
		if ($res == false) Common::throwMsg(102, StatusCode::_102);
	}
	
	private function _validPhone(){
		$this->phone = str_replace(";",",",$this->phone);
		$res = Common::basicValidation($this->phone);
		if ($res == false) Common::throwMsg(103, StatusCode::_103);
		
		$phone = explode(",", $this->phone);
		$nph = array();
		foreach($phone as $ph){
			if (is_numeric($ph) && strlen($ph)==10)
				array_push($nph, $ph);
		}
		$this->phone = implode(",", $nph);
	}
}



?>