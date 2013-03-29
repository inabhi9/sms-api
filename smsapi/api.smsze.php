<?php
include 'smsbase.php';

class SmsGateway extends SmsSkel implements SmsAction{
	const www = "www.smsze.com";

	function __construct($uid, $pwd, $phone, $msg) {
		$this->uid = $uid;
		$this->pwd = $pwd;
		$this->msg = $msg;
		$this->phone = $phone;
		$this->validation(); //Executing validation process
		$this->varEncoding(); //Executing variable encoding process
	}
	
	public function varEncoding(){
		$this->msg = urlencode($this->msg);
		$this->phone = explode(",", $this->phone);
	}
	
	public function doLogin(){
		$this->curl = curl_init();
		curl_setopt ($this->curl, CURLOPT_URL, "http://".SmsGateway::www."/login");
		curl_setopt ($this->curl, CURLOPT_COOKIEFILE, "cookie_smsze");
		curl_setopt ($this->curl, CURLOPT_COOKIESESSION, 1);
		curl_setopt ($this->curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt ($this->curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($this->curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5");
		$text = curl_exec($this->curl);
		
		$pos = strpos($text,'token=');
		$token = substr($text, $pos);
		
		$pos = strpos($token,'"');
		$token = substr($token,0,$pos);
		
		curl_setopt ($this->curl, CURLOPT_URL, "http://".SmsGateway::www."/doValidateLogin?$token");
		curl_setopt ($this->curl, CURLOPT_POST, 1);
		curl_setopt ($this->curl, CURLOPT_POSTFIELDS, "mobile=" . $this->uid . "&password=" . $this->pwd);
		$text = curl_exec($this->curl);
		
		if (strpos($text,"token is invalid")>0)		
			Common::throwMsg(201, StatusCode::_201);
			
		else if(strpos($text,"correct Login")>0)
			Common::throwMsg(101, StatusCode::_101);
	}
	
	public function sendSms(){
		$this->doLogin();
		
		$phone = $this->phone; $msg = $this->msg;

		foreach ($phone as $ph){
			$data = "mobile=$ph&sms=$msg";
			curl_setopt ($this->curl, CURLOPT_URL, "http://".SmsGateway::www."/sendsms"); 
			curl_setopt ($this->curl, CURLOPT_POST, 1);
			curl_setopt ($this->curl, CURLOPT_POSTFIELDS, $data);
			$text = curl_exec($this->curl);
			
			if (stripos($text,"reached your SMS limit")>0){
				Common::throwMsg(105, StatusCode::_105);
				break;
			}
		}
		curl_close($this->curl);
		
		if (strpos($text,"smssuccess")>0){
			$detail = array("total_mobile"=>$this->total_phone, "numbers"=>$this->phone, "sms_length"=>strlen(urldecode($this->msg)), "sms_text"=>urldecode($this->msg));
			Common::throwMsg(100, $detail);
		}
		else{
			Common::throwMsg(200, StatusCode::_200);
		}

		
	}
	
	
}
?>