<?php
/*
Plugin Name: Contact Coldform
Plugin URI: http://perishablepress.com/press/2008/01/08/contact-coldform/
Description: Contact Coldform is a remixed plug-n-play contact form for WordPress.
Tags: contact, form, contact-form
Author: Jeff Starr
Author URI: http://perishablepress.com/
Donate link: http://digwp.com/book/
Requires at least: 2.3
Tested up to: 3.0.5
Stable tag: 1.0
Version: 1.0

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA
*/

// NO EDITING REQUIRED - PLEASE SET PREFERENCES IN THE WORDPRESS COLDFORM ADMIN!

$coldform_version = '0.88.1';
load_plugin_textdomain('coldform', $path = 'wp-content/plugins/contact-coldform');

$coldform_strings = array(
	'name' 	   => '<input class="input" type="text" name="coldform_name" id="coldform_name" size="33" maxlength="99" value="' . htmlentities($_POST['coldform_name']) . '" />', 
	'email'    => '<input class="input" type="text" name="coldform_email" id="coldform_email" size="33" maxlength="99" value="' . htmlentities($_POST['coldform_email']) . '" />', 
	'response' => '<input class="input" type="text" name="coldform_response" id="coldform_response" size="33" maxlength="99" value="' . htmlentities($_POST['coldform_response']) . '" />',	
	'message'  => '<textarea class="input" name="coldform_message" id="coldform_message" cols="33" rows="11" >' . htmlentities($_POST['coldform_message']) . '</textarea>', 
	'error'    => ''
	);

function malicious_input($input) {
	$maliciousness = false;
	$denied_inputs = array("\r", "\n", "mime-version", "content-type", "cc:", "to:");
	foreach($denied_inputs as $denied_input) {
		if(strpos(strtolower($input), strtolower($denied_input)) !== false) {
			$maliciousness = true;
			break;
		}
	}
	return $maliciousness;
}

function spam_question($input) {
	$response = get_option('coldform_response');
	$response = stripslashes(trim($response));
	if (get_option('coldform_casing') == 'TRUE') {
		return (strtoupper($input) == strtoupper($response));
	} else {
		return ($input == $response);
	}
}

function coldform_input_filter() {

	if(!(isset($_POST['coldform_key']))) { 
		return false;
	}

	$_POST['coldform_name']     = stripslashes(trim($_POST['coldform_name']));
	$_POST['coldform_email']    = stripslashes(trim($_POST['coldform_email']));
	$_POST['coldform_topic']    = stripslashes(trim($_POST['coldform_topic']));
	$_POST['coldform_website']  = stripslashes(trim($_POST['coldform_website']));
	$_POST['coldform_message']  = stripslashes(trim($_POST['coldform_message']));
	$_POST['coldform_response'] = stripslashes(trim($_POST['coldform_response']));

	global $coldform_strings;
	$style = stripslashes(get_option('coldform_style'));
	$pass  = true;

	if(empty($_POST['coldform_name'])) {
		$pass = FALSE;
		$fail = 'empty';
		$coldform_strings['name'] = '<input class="input" type="text" name="coldform_name" id="coldform_name" size="33" maxlength="99" value="' . htmlentities($_POST['coldform_name']) . '" ' . $style . ' />';
	}
	if(!is_email($_POST['coldform_email'])) {
		$pass = FALSE; 
		$fail = 'empty';
		$coldform_strings['email'] = '<input class="input" type="text" name="coldform_email" id="coldform_email" size="33" maxlength="99" value="' . htmlentities($_POST['coldform_email']) . '" ' . $style . ' />';
	}
	if (empty($_POST['coldform_response'])) {
		$pass = FALSE; 
		$fail = 'empty';
		$coldform_strings['response'] = '<input class="input" type="text" name="coldform_response" id="coldform_response" size="33" maxlength="99" value="' . htmlentities($_POST['coldform_response']) . '" ' . $style . ' />';
	}
	if (!spam_question($_POST['coldform_response'])) {
		$pass = FALSE;
		$fail = 'wrong';
		$coldform_strings['response'] = '<input class="input" type="text" name="coldform_response" id="coldform_response" size="33" maxlength="99" value="' . htmlentities($_POST['coldform_response']) . '" ' . $style . ' />';
	}
	if(empty($_POST['coldform_message'])) {
		$pass = FALSE; 
		$fail = 'empty';
		$coldform_strings['message'] = '<textarea class="input" name="coldform_message" id="coldform_message" cols="33" rows="11" ' . $style . '>' . $_POST['coldform_message'] . '</textarea>';
	}
	if(malicious_input($_POST['coldform_name']) || malicious_input($_POST['coldform_email'])) {
		$pass = false; 
		$fail = 'malicious';
	}
	if($pass == true) {
		return true;
	} else {
		if($fail == 'malicious') {
			$coldform_strings['error'] = "<p>Please do not include any of the following in the Name or Email fields: linebreaks, or the phrases 'mime-version', 'content-type', 'cc:' or 'to:'.</p>";
		} elseif($fail == 'empty') {
			$coldform_strings['error'] = stripslashes(get_option('coldform_error'));
		} elseif($fail == 'wrong') {
			$coldform_strings['error'] = stripslashes(get_option('coldform_spam'));
		}
		return false;
	}
}

function contact_coldform($content='') {
	global $coldform_strings;

	if(!preg_match('|<!--coldform-->|', $content)) {
		return $content;
	}

	if(coldform_input_filter()) {

		if(empty($_POST['coldform_topic'])) {
			$topic = get_option('coldform_subject');
		} elseif(!empty($_POST['coldform_topic'])) {
			$topic = $_POST['coldform_topic'];
		}
		if(empty($_POST['coldform_carbon'])) {
			$copy  = "No carbon copy sent.";
		} elseif(!empty($_POST['coldform_carbon'])) {
			$copy  = "Copy sent to sender.";
		}
		if(empty($_POST['coldform_website'])) {
			$website = "No website specified.";
		} elseif(!empty($_POST['coldform_website'])) {
			$website = $_POST['coldform_website'];
		}

		$recipient = get_option('coldform_email');
		$recipname = get_option('coldform_name');
		$recipsite = get_option('coldform_website');
		$success   = get_option('coldform_success');
		$success   = stripslashes($success);
		$name      = $_POST['coldform_name'];
		$email     = $_POST['coldform_email'];

		$senderip  = get_ip_address();
		$offset    = get_option('gmt_offset');
		$agent     = $_SERVER['HTTP_USER_AGENT'];
		$form      = getenv("HTTP_REFERER");
		$host      = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		$date      = date("l, F jS, Y @ g:i a", time()+get_option('coldform_offset')*60*60);

		$headers   = "MIME-Version: 1.0\n";
		$headers  .= "From: $name <$email>\n";
		$headers  .= "Content-Type: text/plain; charset=\"" . get_settings('blog_charset') . "\"\n";

		$message   = $_POST['coldform_message'];
		$message   = wordwrap($message, 77, "\n");
		$fullmsg   = ("Hello $recipname,

You are being contacted via $recipsite:

Name:     $name
Email:    $email
Carbon:   $copy
Website:  $website
Subject:  $topic
Message:

$message

-----------------------

Additional Information:

IP:     $senderip
Site:   $recipsite
URL:    $form
Time:   $date
Host:   $host
Agent:  $agent
Whois:  http://www.arin.net/whois/


");

		$fullmsg = stripslashes(strip_tags(trim($fullmsg)));
		mail($recipient, $topic, $fullmsg, $headers);
		if($_POST['coldform_carbon'] == 'TRUE') {
			mail($email, $topic, $fullmsg, $headers);
		}

		$results = ($success . '
<p id="contact_message">Thanks for contacting me, ' . $name . '. The following information has been sent via email:</p> 
<pre><code>Date:       ' . $date . '
Name:       ' . $name    . '
Email:      ' . $email   . '
Carbon:     ' . $copy    . '
Website:    ' . $website . '
Subject:    ' . $topic   . '
Message:    ' . $message . '</code></pre>
<p id="contact_reset">[ <a href="'.$form.'" title="Click here to reset the form.">Click here to reset the form</a> ]</p>');
		echo $results;

    } else {
		if(strstr($content, "<!--coldform-->" )) {

			$question = stripslashes(get_option('coldform_question'));
			$nametext = stripslashes(get_option('coldform_nametext'));
			$mailtext = stripslashes(get_option('coldform_mailtext'));
			$sitetext = stripslashes(get_option('coldform_sitetext'));
			$subjtext = stripslashes(get_option('coldform_subjtext'));
			$messtext = stripslashes(get_option('coldform_messtext'));
			$copytext = stripslashes(get_option('coldform_copytext'));

			if (get_option('coldform_carbon') == 'TRUE') {
				$carbon = ('<fieldset>
									<legend class="hide">Send Yourself a Copy</legend>
									<label class="label" for="coldform_carbon">' . $copytext . '</label>
									<input class="check" type="checkbox" id="coldform_carbon" name="coldform_carbon" value="TRUE" />
								</fieldset>');
			} else { $carbon = ''; }
			
			if (get_option('coldform_credit') == 'TRUE') {
				$credit = ('<p><a href="http://perishablepress.com/press/2008/01/08/contact-coldform/" title="Contact Coldform Homepage @ Perishable Press">Coldform by Perishable Press</a></p>');
			} else { $credit = ''; }

			$form = (
   					$coldform_strings['error'].'
					<!-- Contact Coldform @ http://perishablepress.com/press/2008/01/08/contact-coldform/ -->
					<div id="coldform">
						<form action="' . get_permalink() . '" method="post">
							<fieldset>
								<legend class="hide" title="Note: (X)HTML/code not allowed.">Use this form to contact us via email.</legend>
								<fieldset>
									<legend class="hide">Required: Contact Information</legend>
									<label class="label" for="coldform_name">' . $nametext . '</label>
									' . $coldform_strings['name'] . '
									<label class="label" for="coldform_email">' . $mailtext . '</label>
									' . $coldform_strings['email'] . '
								</fieldset>
								<fieldset>
									<legend class="hide">Optional: Website Information</legend>
									<label class="label" for="coldform_website">' . $sitetext . '</label>
									<input class="input" type="text" name="coldform_website" id="coldform_website" size="33" maxlength="177" value="' . htmlentities($_POST['coldform_website']) . '" />
								</fieldset>
								<fieldset>
									<legend class="hide">Optional: Subject of Email</legend>
									<label class="label" for="coldform_topic">' . $subjtext . '</label>
									<input class="input" type="text" name="coldform_topic" id="coldform_topic" size="33" maxlength="177" value="' . htmlentities($_POST['coldform_topic']) . '" />
								</fieldset>
								<fieldset>
									<legend class="hide">Required: Anti-Spam Challenge Question</legend>
									<label class="label" for="coldform_response">' . $question . '</label>
									' . $coldform_strings['response'] . '
								</fieldset>
								<fieldset>
									<legend class="hide">Required: Message of Email</legend>
									<label class="label" for="coldform_message">' . $messtext . '</label>
									' . $coldform_strings['message'] . '
								</fieldset>
								<fieldset>
									<legend class="hide">Send Message via Email</legend>
									<input class="submit" type="submit" name="Submit" value="Submit" id="contact" />
									<input type="hidden" name="coldform_key" value="process" />
								</fieldset>
								' . $carbon . '
							</fieldset>
						</form>
					</div>
					' . $credit . '
					<div class="clear">&nbsp;</div>
			');
		}
		$content = preg_replace('/<p>\s*<!--(.*)-->\s*<\/p>/i', "<!--$1-->", $content);
		$content = str_replace('<!--coldform-->', $form, $content);
		return $content;
    }
}

function get_ip_address() {
	if(isset($_SERVER)) {
		if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} elseif(isset($_SERVER["HTTP_CLIENT_IP"])) {
			$ip_address = $_SERVER["HTTP_CLIENT_IP"];
		} else {
			$ip_address = $_SERVER["REMOTE_ADDR"];
		}
	} else {
		if(getenv('HTTP_X_FORWARDED_FOR')) {
			$ip_address = getenv('HTTP_X_FORWARDED_FOR');
		} elseif(getenv('HTTP_CLIENT_IP')) {
			$ip_address = getenv('HTTP_CLIENT_IP');
		} else {
			$ip_address = getenv('REMOTE_ADDR');
		}
	}
	return $ip_address;
}

function coldform_admin() {
	add_options_page('Coldform Options', 'Coldform', 'manage_options', 'contact-coldform/coldform_options.php');
}

add_action('admin_menu', 'coldform_admin');
add_filter('the_content', 'contact_coldform');

?>