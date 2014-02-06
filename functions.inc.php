<?php
//	Create a raw address string
function formataddress($streetaddr, $addrcity, $addrstate, $addrzip) 
{
	global $debugmode;
	
	if ($debugmode == TRUE) echo('function formataddress: ' . $streetaddress . '|' . $addrcity . '|' .
			$addrstate . '|' . $addrzip . '<br />');

	$addr = $streetaddr . " " . $addrcity . ", " . $addrstate . " " . $addrzip;
	return $addr;
}

//	Get the legislator's email address based on residence address
function getlegislator($homeaddr, $state) 
{
	global $debugmode, $proxyhost, $proxyport, $proxyuser, $proxypass, $legislature, $conntimeout, $restimeout;
	
	if ($debugmode == TRUE) echo('...executing getlegislator function<br />');
	
	//	Initialize SOAP client
	if ($debugmode == TRUE) echo('.....attempting SOAP connection to http://geoserver.votermart.com/DistrictCodeService.asmx<br />');
	$client = new nusoap_client('http://geoserver.votermart.com/DistrictCodeService.asmx?WSDL', 'wsdl',
		$proxyhost, $proxyport, $proxyuser, $proxypasspass, $conntimeout, $restimeout);
	$err = $client->getError();
	if ($err) 
	{
		$msg = "Unable to connect to VoterMart Geoserver.  Please contact the web site administrator.<br />" .
			"Error message: " . $err;
		die($msg);
		return 1;
	}
	if ($debugmode == TRUE) echo ('.....web service connection successful.<br />');
	
	//	Now call the web service method to retrieve the legislative contact information
	$params = array(
		'address' => $homeaddr,
		'fips' => $state
		);
		
	//	Attempt to get the delegate record from the web service
	switch ($legislature) 
	{
		case "LOWER":
			// Get lower house delegate information
			if ($debugmode == TRUE) echo('.....executing GetLowerHouseDelegate.<br />');
			$result = $client->call('GetLowerHouseDelegate', array('parameters' => $params), '', '', false, true);
			break;
		case "UPPER":
			// Get upper house delegate information
			if ($debugmode == TRUE) echo('.....executing GetUpperHouseDelegate.<br />');
			$result = $client->call('GetUpperHouseDelegate', array('parameters' => $params), '', '', false, true);
			break;
		case "CONGRESS":
			// Get congress delegate information
			if ($debugmode == TRUE) echo('.....executing GetCongressDelegate.<br />');
			$result = $client->call('GetCongressDelegate', array('parameters' => $params), '', '', false, true);
			break;
	}
	
	//	If debugmode is true, display full SOAP transaction messages
	if ($debugmode == TRUE)
	{
		echo ('<hr>');
		echo ('<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>');
		echo ('<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>');
		echo ('<h2>Debug</h2><pre>' . htmlspecialchars($client->debug_str, ENT_QUOTES) . '</pre>');
		echo ('<hr>');		
		echo ('.....$result = ');
		print_r ($result);
		echo ('<br />');
	}

	//	Check to see if a fault occurred
	if ($debugmode == TRUE) echo ('.....checking for SOAP errors.<br />');
	if ($client->fault)
	{	
		if ($debugmode == TRUE) echo ('.....a fault occurred in SOAP transaction<br />');
		$msg = "An error occurred looking up the delegate.  Please contact the web site administrator.<br />";
		die($msg);
		return 1;
	}
	//	Check if transaction was successful
	$err = $client->getError();
	if ($err) 
	{
		if ($debugmode == TRUE) echo ('.....an error occurred in SOAP transaction<br />');
		$msg = "Unable to get delegate information.  Please contact the web site administrator.<br />" .
			"Error message: " . $err;
		die($msg);
		return 1;
	}
	
	//	Parse the results into the array legislator
	if ($debugmode == TRUE) echo ('.....parsing the results.<br />');
		switch ($legislature) 
	{
		case "LOWER":
			parseresults($result['GetLowerHouseDelegateResult']);
			break;
		case "UPPER":
			parseresults($result['GetUpperHouseDelegateResult']);
			break;
		case "CONGRESS":
			parseresults($result['GetCongressDelegateResult']);
			break;
	}
	return 0;
}

//	Verify that the CAPTCHA code is correct
function checkcaptcha() 
{
	global $debugmode;
	
	if ($debugmode == TRUE) echo('...executing checkcaptcha function<br />');

	session_start();
	if ($_SESSION["pass"] != $_POST["userpass"]) 
	{
		die("Sorry, you failed the CAPTCHA. Note that the CAPTCHA is case-sensitive. Please hit your browser back button and try again.");
		return 1;
	}
	else
	{
		return 0;
	}
}

//	Check the formatting of the email address	
function checkemail($field) 
{
	global $debugmode;
	
	if ($debugmode == TRUE) echo('...executing checkemail function<br />');

	// checks proper syntax
	if( !preg_match( "/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/", $field))
	{
		die("Improper email address detected. Please hit your browser back button and try again."); 
		return 1;
	}
	else
	{
		return 0;
	}
}

//	Attempt to determine if this is spam
function spamcheck($field) 
{
	global $debugmode;
	
	if ($debugmode == TRUE) echo('...executing spamcheck function<br />');

	if(eregi("to:",$field) || eregi("cc:",$field) || eregi("\r",$field) || eregi("\n",$field) || eregi("%0A",$field))
	{ 
		$possiblespam = TRUE;
	}
	else 
	{
		$possiblespam = FALSE;
	}
	if ($possiblespam) 
	{
		die("Possible spam attempt detected. If this is not the case, please edit the content of the contact form and try again.");
		return 1;
	}
	else
	{
		return 0;
	}
}

//	Check that the minimum length has been met
function strlencheck($field,$minlength,$whichfieldresponse) 
{
	global $debugmode;
	
	if ($debugmode == TRUE) echo('...executing strlencheck function<br />');

	if (strlen($field) < $minlength)
	{
		die($whichfieldresponse); 
		return 1;
	}
	else
	{
		return 0;
	}
}

//	Split the web service results into an array
function parseresults($field) 
{
	global $debugmode, $legislator;
	
	if ($debugmode == TRUE) 
	{
		echo('...executing parseresults function<br />');
		echo('......$field = ' . $field . '<br />');
	}

	$parts = explode("|", $field);
	$legislator['status'] = $parts[0];
	$legislator['distid'] = $parts[1];
	$legislator['title'] = $parts[2];
	$legislator['fname'] = $parts[3];
	$legislator['mname'] = $parts[4];
	$legislator['lname'] = $parts[5];
	$legislator['email'] = $parts[19];
	
	if ($debugmode == TRUE)
	{
		echo('.....$parts = ');
		print_r ($parts);
		echo('<br />');
		echo('.....$legislator = ');
		print_r ($legislator);
		echo('<br />');
	}
}
?>
