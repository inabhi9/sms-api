# SMS API
Unofficial various websites SMS api. Currently fullonsms.com, way2sms.com, site2sms.com and smsze.com are built.  
*This is  solely for educational purpose.*

#Usage
**sms.php** is an entry point script and accepts parameter in GET/POST methods
Accepted parameters:  

* gateway = SITE_NAME (eg. fullonsms, way2sms)  
* uid = USER_ID of repective gateway  
* pwd = PASSWORD  
* phone = PHONE_NO, receiver  
* msg = your message  

##Example
    /sms.php?gateway=fullonsms&uid=xxxxxxxxx&pwd=xxxxxxx&phone=xxxxxx&msg=hi