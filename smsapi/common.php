<?php
interface StatusCode{
	const _100 = "Success";
	const _101 = "Invalid credential";
	const _102 = "Invalid message";
	const _103 = "Invalid phone";
	const _104 = "Number is registered in NDNC registry. Sending failed.";
	const _105 = "Your daily quota from respected service provider has been exceeded.";
	
	const _201 = "Broken API";
	const _200 = "Something went wrong!";
}
class Common implements Statuscode{
	
	public static function basicValidation($text){
		if (trim($text) == "" || strlen($text) == 0) {
			return false;
		}
		else
			return true;
	}
	
	public static function throwMsg($code, $msg){
		$detail = array("description"=>$msg);
		$res = array("code"=>$code, "detail" => $detail);
		$respond = array("respond"=>$res);
		die (json_encode($respond));
	}
}


?>