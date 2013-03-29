<?php
include 'smsbase.php';

class SmsGateway extends SmsSkel implements SmsAction{
	const www = "www.site2sms.com";

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
		curl_setopt ($this->curl, CURLOPT_URL, "http://".SmsGateway::www."/auth.asp");
		curl_setopt ($this->curl, CURLOPT_POST, 1);
		curl_setopt ($this->curl, CURLOPT_POSTFIELDS, "userid=" . $this->uid . "&password=" . $this->pwd . "&Submit=Sign in");
		curl_setopt ($this->curl, CURLOPT_COOKIEFILE, "cookie_site2sms");
		curl_setopt ($this->curl, CURLOPT_COOKIESESSION, 1);
		curl_setopt ($this->curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt ($this->curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($this->curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5");
		curl_setopt ($this->curl, CURLOPT_REFERER, "http://".SmsGateway::www."/");
		$text = curl_exec($this->curl);
		
		//die($text);
		if (stripos($text,"Wrong Username")>0 && stripos($text,"and Password")>0)		
			Common::throwMsg(101, StatusCode::_101);			
	}
	
	public function sendSms(){
		$this->doLogin();
		$phone = $this->phone; $msg = $this->msg;

		foreach ($phone as $ph){
			$data = "Action=SendSms&txtMobileNo=$ph&txtMessage=$msg&txtLeft=0&txtUsed=260";
			curl_setopt ($this->curl, CURLOPT_URL, "http://".SmsGateway::www."/user/send_sms_next.asp"); 
			curl_setopt ($this->curl, CURLOPT_POST, 1);
			curl_setopt ($this->curl, CURLOPT_POSTFIELDS, $data);
			$text = curl_exec($this->curl);
			if (stripos($text,"Limit Exceeded")>0){
				Common::throwMsg(105, StatusCode::_105);
				break;
			}
		}
		curl_close($this->curl);
		
		if (stripos($text,"SMS Successfully Sent")>0){
			$detail = array("total_mobile"=>$this->total_phone, "numbers"=>$this->phone, "sms_length"=>strlen(urldecode($this->msg)), "sms_text"=>urldecode($this->msg));
			Common::throwMsg(100, $detail);
		}
		else if (stripos($text,"DND Registered")>0){
			Common::throwMsg(104, StatusCode::_104);
		}
		else{
			Common::throwMsg(200, StatusCode::_200);
		}

		
	}
	
	
}
?>