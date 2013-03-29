<?php
include 'smsbase.php';

class SmsGateway extends SmsSkel implements SmsAction{
	const www = "www.fullonsms.com";

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
	}
	
	public function doLogin(){

		$this->curl = curl_init();
		curl_setopt ($this->curl, CURLOPT_URL, "http://".SmsGateway::www."/CheckLogin.php");
		curl_setopt ($this->curl, CURLOPT_POST, 1);
		curl_setopt ($this->curl, CURLOPT_POSTFIELDS, "MobileNoLogin=" . $this->uid . "&LoginPassword=" . $this->pwd . "&x=22&y=17");
		curl_setopt ($this->curl, CURLOPT_COOKIEFILE, "cookie_fullonsms");
		curl_setopt ($this->curl, CURLOPT_COOKIESESSION, 1);
		curl_setopt ($this->curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt ($this->curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($this->curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5");
		curl_setopt ($this->curl, CURLOPT_REFERER, "http:///www.fullonsms.com/");
		$text = curl_exec($this->curl);
		
		if(strpos($text,'content="5;URL= http://'.SmsGateway::www.'/login.php"')>0){
			Common::throwMsg(101, StatusCode::_101);
		}
	}
	
	public function sendSms(){
		$this->doLogin();
		
		$phone = $this->phone; $msg = $this->msg;
		$phone = explode(",", $phone);
		
		foreach($phone as $ph){
			$data = "CancelScript=/home.php&MobileNos=$ph&SelGroup=&Message=$msg&Gender=0&FriendName=Your+Friend+Name&ETemplatesId=&TabValue=contacts&IntSubmit=Ok";
			curl_setopt ($this->curl, CURLOPT_URL, "http://".SmsGateway::www."/home.php"); 
			curl_setopt ($this->curl, CURLOPT_POST, 1);
			curl_setopt ($this->curl, CURLOPT_POSTFIELDS, $data);
			curl_setopt ($this->curl, CURLOPT_REFERER, "http://".SmsGateway::www."/sc.php");
			$text = curl_exec($this->curl);
		}
		curl_close($this->curl);
		
		$detail = array("total_mobile"=>$this->total_phone, "numbers"=>$this->phone, "sms_length"=>strlen(urldecode($this->msg)), "sms_text"=>urldecode($this->msg));
		Common::throwMsg(100, $detail);
	}
	
	
}
?>