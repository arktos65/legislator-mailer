Legislator Mailer
=================

This PHP application adds the ability for your web site to generate email campaigns to
legislators based on a voter's street address.

REQUIREMENTS
------------

* PHP v5.x
* NuSOAP 0.9.5
* VoterMart Geoserver 1.0

INSTALLATION
------------

* Download and install the NuSOAP libraries in a subdirectory of your website.  Note the location where the libraries are installed.	
* Upload the VoterMart Legislative Mailer PHP application to a subdirector of your web site.  If you're planning more than one email campaign, install the application for each campaign into separate directories.
* Edit the config.inc.php file and make whatever changes are necessary. Check all the variables to make sure they are configured the way you want them.
* The sample_form.html has been provided to show you the minimum requirements for your email campaign.
* Set the $testmode variable to true (in the config.inc.php) to test your new campaign form.  In test mode, the application will not send emails, only display the results.  Once you are ready to begin sending emails, set $testmode to false (also make sure $debugmode is set to false too).

EMAIL CAMPAIGN FORM
-------------------

The application is called using a simple HTML form.  The form must use the method POST and the action
must point to mail_legislator.php.  The following fields are required on the form:

	1. contact (textbox) - the name of the person sending the email.
	2. email (textbox) - email address of the person sending the message.
	3. address (textbox) - home street address of the sender.
	3. city (textbox) - home address city.
	4. state (textbox) - home address state.
	5. zipcode (textbox) - home address postal code.
	6. comments (textarea) - additional comments from the sender.
	7. message (hidden) - the message to be sent.

If you wish to use CAPTCHA on the email form, you will need the following lines in your form:

	<p><img src="captcha.php" alt="captcha" /><br />
	<input type="text" name="userpass" value="" /></p>

Also set the variable $usecaptcha to true in the config.inc.php.

DIAGNOSING PROBLEMS
-------------------

In the even that you're having problems with the application, edit the config.inc.php file and set
both $testmode and $debugmode to TRUE.  Debug mode will generate extensive messages, including the
detailed transactions between the PHP application and the VoterMart Geoserver.

In normal operations, be sure to set both of these variables to FALSE.
