<?php
/*
	VoterMart Legislative Mailer - Version 1.0

	This application provides your web site the ability to generate emails to legislators from
	constituents, by simply entering their name and address.  The address is used to determine
	the legislative district and legislator's email address.  A message is constructed and sent.
*/

//	Include user controlled configuration variables
include_once('config.inc.php');

//	Include the nusoap library
include_once($soaplib);
include_once('functions.inc.php');

//	Initialize legislator contact array
$legislator = array (
	'status' => "",
	'distid' => "",
	'title' => "",
	'fname' => "",
	'mname' => "",
	'lname' => "",
	'email' => ""
	);

//	REQUEST the data from the HTTP POST buffer
$contactname = $_REQUEST['contact'];
$contactemail = $_REQUEST['email'];
$address = $_REQUEST['address'];
$city = $_REQUEST['city'];
$state = $_REQUEST['state'];
$zipcode = $_REQUEST['zipcode'];
$message = $_REQUEST['message'];
$comments = $_REQUEST['comments'];

//	Initialize variables
$dontsendemail = 0;
$possiblespam = FALSE;
$strlenmessage = "";
$emailaddress = "";							//	Recipient email address
$delegatename = "";							//	Recipient's name
$rawaddress = "";

//	Display variables in testmode
if ($debugmode == true)
{
	echo('$contactname: ' . $contactname . '<br />');
	echo('$contactemail: ' . $contactemail . '<br />');
	echo('$address: ' . $address . '<br />');
	echo('$city: ' . $city . '<br />');
	echo('$state: ' . $state . '<br />');
	echo('$zipcode: ' . $zipcode. '<br />');
	echo('$message: ' . $message . '<br />');
	echo('$comments: ' . $comments . '<br />');
}

//	Step 1 - Check the CAPTCHA code for validity
if ($debugmode == true) echo('...executing step 1 - $dontsendemail = ' . $dontsendemail . '<br />');
if ($usecaptcha == true) $dontsendemail = checkcaptcha($contactemail);

//	Step 2 - Geocode the address and return the legislator's contact info
if ($debugmode == true) echo('...executing step 2 - $dontsendemail = ' . $dontsendemail . '<br />');
$rawaddress = formataddress($address, $city, $state, $zipcode);
if ($debugmode == true) echo('...$rawaddress = ' . $rawaddress . '<br />');
if ($dontsendemail == 0) $dontsendemail = strlencheck($rawaddress, 20, "Your home address is too short.  Please hit your browser back button and check your address.<br />");
//	Consume the XML web service
if ($dontsendemail == 0)
{ 
	$dontsendemail = getlegislator($rawaddress, $statefips, $proxyhost, $proxyport, $proxyuser, $proxypass, $debugmode);
	if ($legislator['status'] == "OK") 
	{
		$delegatename = $legislator['title'] . " " . $legislator['lname'];
		$emailaddress = $legislator['email'];
	} 
	else 
	{
		$delegatename = "[" . $legislator['distid'] . "]";
		$emailaddress = $fallbackemail;
	}
	if ($debugmode == TRUE)
	{
		echo ('...$delegatename = ' . $delegatename . '<br />');
		echo ('...$emailaddress = ' . $emailaddress . '<br />');
	}
}

//	Step 3 - Check for valid email address formats
if ($debugmode == true) echo('...executing step 3 - $dontsendemail = ' . $dontsendemail . '<br />');
if ($dontsendemail == 0) $dontsendemail = checkemail($contactemail);
if ($dontsendemail == 0) $dontsendemail = checkemail($emailaddress);

//	Step 4 - Check for possible spam abuse
if ($debugmode == true) echo('...executing step 4 - $dontsendemail = ' . $dontsendemail . '<br />');
if ($dontsendemail == 0) $dontsendemail = spamcheck($message);
if ($dontsendemail == 0) $dontsendemail = spamcheck($comments);
if ($dontsendemail == 0) $dontsendemail = strlencheck($contactname,2, "Your need to provide your name.  Please hit your browser back button and check your name.<br />");
if ($dontsendemail == 0) $dontsendemail = strlencheck($contactemail,10,"Your email address is too short. Please hit your browser back button and check your entry.<br />");

//	Step 5 - Format the email message
if ($debugmode == true) echo('...executing step 5 - $dontsendemail = ' . $dontsendemail . '<br />');
$emailmsg = "Dear " . $delegatename . ",\n\n" . $message . "\n\n" .
	"Sincerely,\n" . $contactname . "\n" . $city . ", " . $state . "\n";
if (strlen($comments) > 0) 
{
	$emailmsg = $emailmsg . "\nPS: " . $comments . "\n";
}

//	Step 6 - Send the email
if ($debugmode == true) echo('...executing step 6 - $dontsendemail = ' . $dontsendemail . '<br />');
if ($testmode == FALSE) 
{
	if ($dontsendemail == 0) 
	{
		$headers = 'From: ' . $contactemail . "\r\n" .
			'Reply-To: ' . $contactemail . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
		mail($emailaddress, $subject ,$emailmsg, $headers);
		include $senturl;
	} 
}
else
{
	if ($dontsendemail == 0) 
	{
		include('test_results.php');
	}
}
?>