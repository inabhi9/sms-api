<?php
include 'smsbase.php';

class SmsGateway extends SmsSkel implements SmsAction{
	const www = "site5.way2sms.com";
	private $action;
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
		curl_setopt ($this->curl, CURLOPT_URL, "http://".SmsGateway::www."/Login1.action");
        curl_setopt ($this->curl, CURLOPT_POST, 1);
        curl_setopt ($this->curl, CURLOPT_POSTFIELDS, "username=" . $this->uid . "&password=" . $this->pwd . "&button=Login");
        curl_setopt ($this->curl, CURLOPT_COOKIESESSION, 1);
        curl_setopt ($this->curl, CURLOPT_COOKIEFILE, "cookie_way2sms");
        curl_setopt ($this->curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt ($this->curl, CURLOPT_MAXREDIRS, 20);
        curl_setopt ($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($this->curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5");
        curl_setopt ($this->curl, CURLOPT_REFERER, "http://".SmsGateway::www."/");
		$text = curl_exec($this->curl);
		$pos = stripos(curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL), "Main.action");

		if ($pos === "FALSE" || $pos == 0 || $pos == "")
			Common::throwMsg(101, StatusCode::_101);
		
		$refurl = curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL);
        curl_setopt ($this->curl, CURLOPT_REFERER, $refurl);
        curl_setopt ($this->curl, CURLOPT_URL, "http://".SmsGateway::www."/jsp/InstantSMS.jsp");
        $text = curl_exec($this->curl);
		
		preg_match_all('/<input[\s]*type="hidden"[\s]*name="Action"[\s]*id="Action"[\s]*value="?([^>]*)?"/is', $text, $match);
 
        $this->action=$match[1][0]; // get custid from the form fro the Action field in the post form
	}
	
	public function sendSms(){
		$this->doLogin();
		
		$phone = $this->phone; $msg = $this->msg;

		foreach ($phone as $ph){
			$data = "HiddenAction=instantsms&login=&pass=&custid=undefined&Action=".$this->action."&MobNo=".$ph."&textArea=".$msg;
			curl_setopt ($this->curl, CURLOPT_URL, "http://".SmsGateway::www."/quicksms.action");
            curl_setopt ($this->curl, CURLOPT_REFERER, curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL));
            curl_setopt ($this->curl, CURLOPT_POST, 1);
            curl_setopt ($this->curl, CURLOPT_POSTFIELDS, $data);
            $contents= curl_exec($this->curl);
			if (stripos($contents,"your day quota")>0){
				Common::throwMsg(105, StatusCode::_105);
				break;
			}
		}
		curl_close($this->curl);
		
		if (stripos($contents,"submitted successfully")>0){
			$detail = array("total_mobile"=>$this->total_phone, "numbers"=>$this->phone, "sms_length"=>strlen(urldecode($this->msg)), "sms_text"=>urldecode($this->msg));
			Common::throwMsg(100, $detail);
		}
		else if (stripos($contents,"DND")>0){
			Common::throwMsg(104, StatusCode::_104);
		}
		else{
			Common::throwMsg(200, StatusCode::_200);
		}

		
	}
	
	
}
?>