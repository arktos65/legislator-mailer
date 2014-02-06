<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>VoterMart Legislative Mailer - Test Mode</title>
</head>

<body>
<?php
/*
	This file is included if the $testmode variable in config.php is set to true.  The results
	are displayed as an HTML page instead of sending an email.
*/
echo "<p>Contact Name: " . $contactname . "<br />";
echo "Email Address: " . $contactemail . "</p>";
echo "<p>Legislator: " . $delegatename . "<br />";
echo "Email Address: " . $emailaddress . "</p>";
echo "<hr>";
print_r ($emailmsg);
echo "<hr>";
print_r ($legislator);
?>

</body>

</html>
