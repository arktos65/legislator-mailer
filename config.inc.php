<?php
/*	
	VoterMart Legislative Mailer
	Written by Sean M. Sullivan
	
	Use this file to adjust the settings for your particular legislative email campaign. Do
	not edit any of the other PHP files, unless you know what you're doing!
*/

//	Set this value to true if you only want to test without sending email, otherwise set to false
$testmode = true;

//	Debug mode is useful for troubleshooting SOAP issues
$debugmode = false;

//	Location of nusoap.php library, change it for your particular installation
$soaplib = "../nusoap/lib/nusoap.php";

//	Timeout value in seconds for SOAP client calls
$conntimeout = 30;							//	Client connect timeout
$restimeout = 90;							//	Transaction response timeout

//	Set the URL of the web page you want to land on after successfully sending the email
$senturl = "../email_sent.php";

//	SOAP proxy access credentials (leave blank if no authentication is required)
$proxyhost = "";
$proxyport = "";
$proxyuser = "";
$proxypass = "";

//	Set this value to true if you want to use CAPTCHA on your email form
$usecaptcha = true;

//	Email subject line
$subject = "Legislative mailer test";

//	Fallback email address in case legislater cannot be located in database
$fallbackemail = "sean@icspot.com";

//	Uncomment appropriate line to specify which legislative body this campaign is for.
$legislature = "LOWER";
//$legislature = "UPPER";
//$legislature = "CONGRESS";

//	Specify the two-digit state FIPS code.  Values less than 10 must have a preceeding zero.
$statefips = "06";
?>