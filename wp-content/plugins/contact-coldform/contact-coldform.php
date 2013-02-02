<?php 
/*
Plugin Name: Contact Coldform
Plugin URI: http://perishablepress.com/contact-coldform/
Description: Delivers a lightweight, clean-markup contact-form that doesn't require JavaScript.
Tags: contact, form, contact form, email
Author: Jeff Starr
Author URI: http://monzilla.biz/
Donate link: http://digwp.com/book/
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 20130103
Version: 20130103
License: GPL v2
*/

// NO EDITING REQUIRED - PLEASE SET PREFERENCES IN THE WP ADMIN!

$contact_coldform_plugin  = __('Contact Coldform');
$contact_coldform_options = get_option('contact_coldform_options');
$contact_coldform_path    = plugin_basename(__FILE__); // 'contact-coldform/contact-coldform.php';
$contact_coldform_homeurl = 'http://perishablepress.com/contact-coldform/';
$contact_coldform_version = '20130103';

// require minimum version of WordPress
add_action('admin_init', 'contact_coldform_require_wp_version');
function contact_coldform_require_wp_version() {
	global $wp_version, $contact_coldform_path, $contact_coldform_plugin;
	if (version_compare($wp_version, '3.0', '<')) {
		if (is_plugin_active($contact_coldform_path)) {
			deactivate_plugins($contact_coldform_path);
			$msg =  '<strong>' . $contact_coldform_plugin . '</strong> ' . __('requires WordPress 3.0 or higher, and has been deactivated!') . '<br />';
			$msg .= __('Please return to the ') . '<a href="' . admin_url() . '">' . __('WordPress Admin area') . '</a> ' . __('to upgrade WordPress and try again.');
			wp_die($msg);
		}
	}
}

// create inputs
$contact_coldform_strings = array(
	'name'     => '<input name="coldform_name" id="coldform_name" type="text" size="33" maxlength="99" value="' . htmlentities($_POST['coldform_name']) . '" placeholder="Your name" />', 
	'email'    => '<input name="coldform_email" id="coldform_email" type="text" size="33" maxlength="99" value="' . htmlentities($_POST['coldform_email']) . '" placeholder="Your email" />', 
	'response' => '<input name="coldform_response" id="coldform_response" type="text" size="33" maxlength="99" value="' . htmlentities($_POST['coldform_response']) . '" placeholder="' . $contact_coldform_options['coldform_question'] . '" />', 
	'message'  => '<textarea name="coldform_message" id="coldform_message" cols="33" rows="7" placeholder="Your message">' . htmlentities($_POST['coldform_message']) . '</textarea>', 
	'verify'   => '<input name="coldform_verify" type="text" size="33" maxlength="99" value="" />', 
	'error'    => '',
);

// spam filter
function contact_coldform_filter_input($input) {
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

// challenge question
function contact_coldform_spam_question($input) {
	global $contact_coldform_options;
	$response = $contact_coldform_options['coldform_response'];
	$response = stripslashes(trim($response));
	if ($contact_coldform_options['coldform_casing'] == true) {
		return (strtoupper($input) == strtoupper($response));
	} else {
		return ($input == $response);
	}
}

// get ip address
function contact_coldform_get_ip_address() {
	if (isset($_SERVER)) {
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

// filter input
function contact_coldform_input_filter() {
	global $contact_coldform_options, $contact_coldform_strings;
	$coldform_style = $contact_coldform_options['coldform_style'];
	$pass = true;

	$_POST['coldform_name']     = stripslashes(trim($_POST['coldform_name']));
	$_POST['coldform_email']    = stripslashes(trim($_POST['coldform_email']));
	$_POST['coldform_topic']    = stripslashes(trim($_POST['coldform_topic']));
	$_POST['coldform_website']  = stripslashes(trim($_POST['coldform_website']));
	$_POST['coldform_message']  = stripslashes(trim($_POST['coldform_message']));
	$_POST['coldform_response'] = stripslashes(trim($_POST['coldform_response']));

	if (!isset($_POST['coldform_key'])) { 
		return false; 
	}
	if (empty($_POST['coldform_name'])) {
		$pass = false;
		$fail = 'empty';
		$contact_coldform_strings['name'] = '<input name="coldform_name" id="coldform_name" type="text" size="33" maxlength="99" value="' . htmlentities($_POST['coldform_name']) . '" class="coldform-error-input" ' . $coldform_style . ' placeholder="Your name" />';
	}
	if (!is_email($_POST['coldform_email'])) {
		$pass = false;
		$fail = 'empty';
		$contact_coldform_strings['email'] = '<input name="coldform_email" id="coldform_email" type="text" size="33" maxlength="99" value="' . htmlentities($_POST['coldform_email']) . '" class="coldform-error-input" ' . $coldform_style . ' placeholder="Your email" />';
	}
	if (!empty($_POST['coldform_verify'])) { 
		$pass = false; 
		$fail = 'verify';
		$contact_coldform_strings['verify'] = '<input name="coldform_verify" type="text" size="33" maxlength="99" class="coldform-error-input" value="" ' . $coldform_style . ' />';
	}
	if (empty($_POST['coldform_message'])) {
		$pass = false; 
		$fail = 'empty';
		$contact_coldform_strings['message'] = '<textarea name="coldform_message" id="coldform_message" cols="33" rows="11" class="coldform-error-input" ' . $coldform_style . ' placeholder="Your message">' . $_POST['coldform_message'] . '</textarea>';
	}
	if (contact_coldform_filter_input($_POST['coldform_name']) || contact_coldform_filter_input($_POST['coldform_email'])) {
		$pass = false; 
		$fail = 'malicious';
	}
	if ($contact_coldform_options['coldform_trust'] == false) {
		if (empty($_POST['coldform_response'])) {
			$pass = false; 
			$fail = 'empty';
			$contact_coldform_strings['response'] = '<input name="coldform_response" id="coldform_response" type="text" size="33" maxlength="99" value="' . htmlentities($_POST['coldform_response']) . '" class="coldform-error-input" ' . $coldform_style . ' placeholder="' . $contact_coldform_options['coldform_question'] . '" />';
		}
		if (!contact_coldform_spam_question($_POST['coldform_response'])) {
			$pass = false;
			$fail = 'wrong';
			$contact_coldform_strings['response'] = '<input name="coldform_response" id="coldform_response" type="text" size="33" maxlength="99" value="' . htmlentities($_POST['coldform_response']) . '" class="coldform-error-input" ' . $coldform_style . ' placeholder="' . $contact_coldform_options['coldform_question'] . '" />';
		}	
	}
	if ($pass == true) {
		return true;
	} else {
		if ($fail == 'malicious') {
			$contact_coldform_strings['error'] = "<p class='coldform-error'>Please do not include any of the following in the Name or Email fields: linebreaks, or the phrases 'mime-version', 'content-type', 'cc:' or 'to:'.</p>";
		} elseif ($fail == 'empty') {
			$contact_coldform_strings['error'] = $contact_coldform_options['coldform_error'];
		} elseif ($fail == 'wrong') {
			$contact_coldform_strings['error'] = $contact_coldform_options['coldform_spam'];
		} elseif ($fail == 'verify') {
			$contact_coldform_strings['error'] = "<p class='coldform-error'>Please leave the human-verification field empty and try again.</p>";
		}
		return false;
	}
}

// enqueue styles
add_action('init', 'contact_coldform_register_style');
function contact_coldform_register_style() {
	global $contact_coldform_options, $contact_coldform_version;
	$coldform_coldskin = $contact_coldform_options['coldform_coldskin'];
	if ($coldform_coldskin == 'coldskin_default') {
		$coldskin = 'default.css';
	} elseif ($coldform_coldskin == 'coldskin_classic') {
		$coldskin = 'classic.css';
	} elseif ($coldform_coldskin == 'coldskin_dark') {
		$coldskin = 'dark.css';
	}
	$enable_styles = $contact_coldform_options['coldform_styles'];
	if ($enable_styles == true) {
		$coldform_url = $contact_coldform_options['coldform_url'];
		$current_url = trailingslashit('http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
		if ($coldform_url !== '') {
			if ($coldform_url == $current_url) {
				wp_register_style('coldform', plugins_url() . '/contact-coldform/coldskins/coldskin-' . $coldskin, array(), $contact_coldform_version, 'all');
				wp_enqueue_style('coldform');
			}
		} else {
			wp_register_style('coldform', plugins_url() . '/contact-coldform/coldskins/coldskin-' . $coldskin, array(), $contact_coldform_version, 'all');
			wp_enqueue_style('coldform');
		}
	}
}

// shortcodes to display coldform
add_shortcode('coldform','contact_coldform_shortcode');
add_shortcode('contact_coldform','contact_coldform_shortcode');
function contact_coldform_shortcode() {
	if (contact_coldform_input_filter()) {
		return contact_coldform();
	} else {
		return contact_coldform_display_form();
	}
}

// template tag to display coldform
function contact_coldform_public() {
	if(contact_coldform_input_filter()) {
		echo contact_coldform();
	} else {
		echo contact_coldform_display_form();
	}
}

// display coldform
function contact_coldform_display_form() {
	global $contact_coldform_options, $contact_coldform_strings;

	$question = $contact_coldform_options['coldform_question'];
	$nametext = $contact_coldform_options['coldform_nametext'];
	$mailtext = $contact_coldform_options['coldform_mailtext'];
	$sitetext = $contact_coldform_options['coldform_sitetext'];
	$subjtext = $contact_coldform_options['coldform_subjtext'];
	$messtext = $contact_coldform_options['coldform_messtext'];
	$copytext = $contact_coldform_options['coldform_copytext'];
	$lgndtext = $contact_coldform_options['coldform_welcome'];

	if ($contact_coldform_options['coldform_custom'] !== '') {
		$coldform_custom = '<style type="text/css">' . $contact_coldform_options['coldform_custom'] . '</style>';
	} else { $coldform_custom = ''; }

	if ($contact_coldform_options['coldform_trust'] == false) {
		$coldform_captcha = '<fieldset class="coldform-response">
					<label for="coldform_response">' . $question . '</label>
					' . $contact_coldform_strings['response'] . '
				</fieldset>';
	} else { $coldform_captcha = ''; }

	if ($contact_coldform_options['coldform_carbon'] == true) {
		$coldform_carbon = '<fieldset class="coldform-carbon">
					<input id="coldform_carbon" name="coldform_carbon" type="checkbox" value="1" checked="checked" /> 
					<label for="coldform_carbon">' . $copytext . '</label>
				</fieldset>';
	} else { $coldform_carbon = ''; }

	$coldform = (
		$contact_coldform_strings['error'] . '
		<!-- Contact Coldform @ http://perishablepress.com/contact-coldform/ -->
		<div id="coldform">
			<form action="' . get_permalink() . '" method="post">
				<legend title="Note: text only, no markup.">' . $lgndtext . '</legend>
				<fieldset class="coldform-name">
					<label for="coldform_name">' . $nametext . '</label>
					' . $contact_coldform_strings['name'] . '
				</fieldset>
				<fieldset class="coldform-email">
					<label for="coldform_email">' . $mailtext . '</label>
					' . $contact_coldform_strings['email'] . '
				</fieldset>
				<fieldset class="coldform-website">
					<label for="coldform_website">' . $sitetext . '</label>
					<input name="coldform_website" id="coldform_website" type="text" size="33" maxlength="177" value="' . htmlentities($_POST['coldform_website']) . '" placeholder="Your website" />
				</fieldset>
				<fieldset class="coldform_topic">
					<label for="coldform_topic">' . $subjtext . '</label>
					<input name="coldform_topic" id="coldform_topic" type="text" size="33" maxlength="177" value="' . htmlentities($_POST['coldform_topic']) . '" placeholder="Subject of email" />
				</fieldset>
				' . $coldform_captcha . '
				<fieldset class="coldform-message">
					<label for="coldform_message">' . $messtext . '</label>
					' . $contact_coldform_strings['message'] . '
				</fieldset>
				<fieldset id="coldform_verify" style="display:none;">
					<label for="coldform_verify">Human verification: leave this field empty.</label>
					' . $contact_coldform_strings['verify'] . '
				</fieldset>
				' . $coldform_carbon . '
				<div class="coldform-submit">
					<input name="coldform_submit" type="submit" value="Send it!" id="coldform_submit" />
					<input name="coldform_key" type="hidden" value="process" />
				</div>
			</form>
		</div>
		' . $coldform_custom . '
		<script type="text/javascript">(function(){var e = document.getElementById("coldform_verify");e.parentNode.removeChild(e);})();</script>
		<div class="clear">&nbsp;</div>
		');
	return $coldform;
}

// contact coldform
function contact_coldform($content='') {
	global $contact_coldform_options, $contact_coldform_strings;

	$prefix_topic = $contact_coldform_options['coldform_prefix'] . $_POST['coldform_topic'];
	$user_topic = $_POST['coldform_topic'];

	if (empty($_POST['coldform_topic'])) {
		$topic = $contact_coldform_options['coldform_subject'];
	} elseif (!empty($_POST['coldform_topic'])) {
		$topic = $prefix_topic;
	}
	if (empty($_POST['coldform_carbon'])) {
		$copy  = "No carbon copy sent.";
	} elseif (!empty($_POST['coldform_carbon'])) {
		$copy  = "Copy sent to sender.";
	}
	if (empty($_POST['coldform_website'])) {
		$website = "No website specified.";
	} elseif (!empty($_POST['coldform_website'])) {
		$website = $_POST['coldform_website'];
	}
	$recipient = $contact_coldform_options['coldform_email'];
	$recipname = $contact_coldform_options['coldform_name'];
	$recipsite = $contact_coldform_options['coldform_website'];
	$success   = $contact_coldform_options['coldform_success'];
	$thanks    = $contact_coldform_options['coldform_thanks'];
	$name      = $_POST['coldform_name'];
	$email     = $_POST['coldform_email'];

	$senderip  = contact_coldform_get_ip_address();
	$offset    = $contact_coldform_options['gmt_offset'];
	$agent     = $_SERVER['HTTP_USER_AGENT'];
	$form      = getenv("HTTP_REFERER");
	$host      = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	$offset    = $contact_coldform_options['coldform_offset'];
	$date      = date("l, F jS, Y @ g:i a", time()+$offset*60*60);

	$headers   = "MIME-Version: 1.0\n";
	$headers  .= "From: $name <$email>\n";
	$headers  .= "Content-Type: text/plain; charset=\"" . get_settings('blog_charset') . "\"\n";

	$message   = $_POST['coldform_message'];
	$message   = wordwrap($message, 77, "\n");
	$fullmsg   = "Hello $recipname,

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

";
	$fullmsg = stripslashes(strip_tags(trim($fullmsg)));
	wp_mail($recipient, $topic, $fullmsg, $headers);
	if ($_POST['coldform_carbon'] == '1') {
		wp_mail($email, $topic, $fullmsg, $headers);
	}

	if ($contact_coldform_options['coldform_custom'] !== '') {
		$coldform_custom = '<style type="text/css">' . $contact_coldform_options['coldform_custom'] . '</style>';
	} else { $coldform_custom = ''; }

	$results = '<div id="coldform_thanks">' . $success . $thanks . 
'<pre><code>Date:       ' . $date . '
Name:       ' . $name    . '
Email:      ' . $email   . '
Carbon:     ' . $copy    . '
Website:    ' . $website . '
Subject:    ' . $user_topic . '
Message:    ' . $message . '</code></pre>
<p class="coldform-reset">[ <a href="'.$form.'" title="Click here to reset the form.">Click here to reset the form</a> ]</p>
</div>' . $coldform_custom;

	return $results;
}

// display settings link on plugin page
add_filter ('plugin_action_links', 'contact_coldform_plugin_action_links', 10, 2);
function contact_coldform_plugin_action_links($links, $file) {
	global $contact_coldform_path;
	if ($file == $contact_coldform_path) {
		$contact_coldform_links = '<a href="' . get_admin_url() . 'options-general.php?page=' . $contact_coldform_path . '">' . __('Settings') .'</a>';
		array_unshift($links, $contact_coldform_links);
	}
	return $links;
}

// delete plugin settings
function contact_coldform_delete_plugin_options() {
	delete_option('contact_coldform_options');
}
if ($contact_coldform_options['default_options'] == 1) {
	register_uninstall_hook (__FILE__, 'contact_coldform_delete_plugin_options');
}

// define default settings
register_activation_hook (__FILE__, 'contact_coldform_add_defaults');
function contact_coldform_add_defaults() {
	$user_info = get_userdata(1);
	if ($user_info == true) {
		$admin_name = $user_info->user_login;
	} else {
		$admin_name = 'Neo Smith';
	}
	$site_title = get_bloginfo('name');
	$admin_mail = get_bloginfo('admin_email');
	$tmp = get_option('contact_coldform_options');
	if(($tmp['default_options'] == '1') || (!is_array($tmp))) {
		$arr = array(
			'default_options'   => 0,
			'coldform_name'     => $admin_name,
			'coldform_website'  => $site_title,
			'coldform_email'    => $admin_mail,
			'coldform_offset'   => 'For example, +1 or -1',
			'coldform_subject'  => 'Message sent from your contact form',
			'coldform_success'  => '<p id=\'coldform_success\'>Success! Your message has been sent.</p>',
			'coldform_error'    => '<p id=\'coldform_error\' class=\'coldform-error\'>Please complete the required fields.</p>',
			'coldform_spam'     => '<p id=\'coldform_spam\' class=\'coldform-error\'>Incorrect response for challenge question. Please try again.</p>',
			'coldform_style'    => 'style=\'border: 1px solid #CC0000;\'',
			'coldform_question' => '1 + 1 =',
			'coldform_response' => '2',
			'coldform_casing'   => false,
			'coldform_carbon'   => false,
			'coldform_nametext' => 'Name (Required)',
			'coldform_mailtext' => 'Email (Required)',
			'coldform_sitetext' => 'Website (Optional)',
			'coldform_subjtext' => 'Subject (Optional)',
			'coldform_messtext' => 'Message (Required)',
			'coldform_copytext' => 'Carbon Copy?',
			'coldform_prefix'   => 'Contact Coldform: ',
			'coldform_trust'    => false,
			'coldform_styles'   => true,
			'coldform_coldskin' => 'coldskin_default',
			'coldform_custom'   => '',
			'coldform_url'      => '',
			'coldform_thanks'   => '<p class=\'coldform-thanks\'><span>Thanks for contacting me.</span> The following information has been sent via email:</p>',
			'coldform_welcome'  => '<strong>Hello!</strong> Please use this contact form to send us an email.',
		);
		update_option('contact_coldform_options', $arr);
	}
}

// sanitize and validate input
function contact_coldform_validate_options($input) {
	global $coldform_coldskins;

	if (!isset($input['default_options'])) $input['default_options'] = null;
	$input['default_options'] = ($input['default_options'] == 1 ? 1 : 0);
	
	$input['coldform_name'] = wp_filter_nohtml_kses($input['coldform_name']);
	$input['coldform_website'] = wp_filter_nohtml_kses($input['coldform_website']);
	$input['coldform_email'] = wp_filter_nohtml_kses($input['coldform_email']);
	$input['coldform_offset'] = wp_filter_nohtml_kses($input['coldform_offset']);
	$input['coldform_subject'] = wp_filter_nohtml_kses($input['coldform_subject']);
	
	// dealing with kses
	global $allowedposttags;
	$allowed_atts = array('align'=>array(), 'class'=>array(), 'id'=>array(), 'dir'=>array(), 'lang'=>array(), 'style'=>array(), 'xml:lang'=>array(), 'src'=>array(), 'alt'=>array());

	$allowedposttags['strong'] = $allowed_atts;
	$allowedposttags['small'] = $allowed_atts;
	$allowedposttags['span'] = $allowed_atts;
	$allowedposttags['abbr'] = $allowed_atts;
	$allowedposttags['code'] = $allowed_atts;
	$allowedposttags['div'] = $allowed_atts;
	$allowedposttags['img'] = $allowed_atts;
	$allowedposttags['h1'] = $allowed_atts;
	$allowedposttags['h2'] = $allowed_atts;
	$allowedposttags['h3'] = $allowed_atts;
	$allowedposttags['h4'] = $allowed_atts;
	$allowedposttags['h5'] = $allowed_atts;
	$allowedposttags['ol'] = $allowed_atts;
	$allowedposttags['ul'] = $allowed_atts;
	$allowedposttags['li'] = $allowed_atts;
	$allowedposttags['em'] = $allowed_atts;
	$allowedposttags['p'] = $allowed_atts;
	$allowedposttags['a'] = $allowed_atts;

	$input['coldform_success'] = wp_kses_post($input['coldform_success'], $allowedposttags);
	$input['coldform_error'] = wp_kses_post($input['coldform_error'], $allowedposttags);
	$input['coldform_spam'] = wp_kses_post($input['coldform_spam'], $allowedposttags);
	$input['coldform_style'] = wp_kses_post($input['coldform_style'], $allowedposttags);

	$input['coldform_question'] = wp_filter_nohtml_kses($input['coldform_question']);
	$input['coldform_response'] = wp_filter_nohtml_kses($input['coldform_response']);
	
	if (!isset($input['coldform_casing'])) $input['coldform_casing'] = null;
	$input['coldform_casing'] = ($input['coldform_casing'] == 1 ? 1 : 0);
	
	if (!isset($input['coldform_carbon'])) $input['coldform_carbon'] = null;
	$input['coldform_carbon'] = ($input['coldform_carbon'] == 1 ? 1 : 0);
	
	$input['coldform_nametext'] = wp_filter_nohtml_kses($input['coldform_nametext']);
	$input['coldform_mailtext'] = wp_filter_nohtml_kses($input['coldform_mailtext']);
	$input['coldform_sitetext'] = wp_filter_nohtml_kses($input['coldform_sitetext']);
	$input['coldform_subjtext'] = wp_filter_nohtml_kses($input['coldform_subjtext']);
	$input['coldform_messtext'] = wp_filter_nohtml_kses($input['coldform_messtext']);
	$input['coldform_copytext'] = wp_filter_nohtml_kses($input['coldform_copytext']);

	$input['coldform_prefix'] = wp_filter_nohtml_kses($input['coldform_prefix']);

	if (!isset($input['coldform_trust'])) $input['coldform_trust'] = null;
	$input['coldform_trust'] = ($input['coldform_trust'] == 1 ? 1 : 0);

	if (!isset($input['coldform_styles'])) $input['coldform_styles'] = null;
	$input['coldform_styles'] = ($input['coldform_styles'] == 1 ? 1 : 0);

	if (!isset($input['coldform_coldskin'])) $input['coldform_coldskin'] = null;
	if (!array_key_exists($input['coldform_coldskin'], $coldform_coldskins)) $input['coldform_coldskin'] = null;

	$input['coldform_custom'] = wp_filter_nohtml_kses($input['coldform_custom']);
	$input['coldform_url'] = wp_filter_nohtml_kses($input['coldform_url']);

	$input['coldform_thanks'] = wp_kses_post($input['coldform_thanks'], $allowedposttags);
	$input['coldform_welcome'] = wp_kses_post($input['coldform_welcome'], $allowedposttags);

	return $input;
}

// define style options
$coldform_coldskins = array(
	'coldskin_default' => array(
		'value' => 'coldskin_default',
		'label' => __('Default styles')
	),
	'coldskin_classic' => array(
		'value' => 'coldskin_classic',
		'label' => __('Classic styles')
	),
	'coldskin_dark' => array(
		'value' => 'coldskin_dark',
		'label' => __('Dark styles')
	),
);

// whitelist settings
add_action ('admin_init', 'contact_coldform_init');
function contact_coldform_init() {
	register_setting('contact_coldform_plugin_options', 'contact_coldform_options', 'contact_coldform_validate_options');
}

// add the options page
add_action ('admin_menu', 'contact_coldform_add_options_page');
function contact_coldform_add_options_page() {
	global $contact_coldform_plugin;
	add_options_page($contact_coldform_plugin, $contact_coldform_plugin, 'manage_options', __FILE__, 'contact_coldform_render_form');
}

// create the options page
function contact_coldform_render_form() {
	global $contact_coldform_plugin, $contact_coldform_options, $contact_coldform_path, $contact_coldform_homeurl, $contact_coldform_version, $coldform_coldskins; 
	$offset = $contact_coldform_options['coldform_offset'];?>

	<style type="text/css">
		.mm-panel-overview { padding-left: 115px; background: url(<?php echo plugins_url(); ?>/contact-coldform/contact-coldform.png) no-repeat 15px 0; }

		#mm-plugin-options h2 small { font-size: 60%; }
		#mm-plugin-options h3 { cursor: pointer; }
		#mm-plugin-options h4, 
		#mm-plugin-options p { margin: 15px; line-height: 18px; }
		#mm-plugin-options ul { margin: 15px 15px 25px 40px; line-height: 16px; }
		#mm-plugin-options li { margin: 8px 0; list-style-type: disc; }
		#mm-plugin-options abbr { cursor: help; border-bottom: 1px dotted #dfdfdf; }
		
		.mm-table-wrap { margin: 15px; }
		.mm-table-wrap td { padding: 5px 10px; vertical-align: middle; }
		.mm-table-wrap .mm-table {}
		.mm-table-wrap .widefat th { padding: 10px 15px; vertical-align: middle; }
		.mm-table-wrap .widefat td { padding: 10px; vertical-align: middle; }

		.mm-item-caption { margin: 3px 0 0 3px; font-size: 11px; color: #777; line-height: 17px; }
		.mm-radio-inputs { margin: 5px 0; }
		.mm-code { background-color: #fafae0; color: #333; font-size: 14px; }

		#setting-error-settings_updated { margin: 10px 0; }
		#setting-error-settings_updated p { margin: 5px; }
		#mm-plugin-options .button-primary { margin: 0 0 15px 15px; }

		#mm-panel-toggle { margin: 5px 0; }
		#mm-credit-info { margin-top: -5px; }
		#mm-iframe-wrap { width: 100%; height: 250px; overflow: hidden; }
		#mm-iframe-wrap iframe { width: 100%; height: 100%; overflow: hidden; margin: 0; padding: 0; }
	</style>

	<div id="mm-plugin-options" class="wrap">
		<?php screen_icon(); ?>

		<h2><?php echo $contact_coldform_plugin; ?> <small><?php echo 'v' . $contact_coldform_version; ?></small></h2>
		<div id="mm-panel-toggle"><a href="<?php get_admin_url() . 'options-general.php?page=' . $contact_coldform_path; ?>"><?php _e('Toggle all panels'); ?></a></div>

		<form method="post" action="options.php">
			<?php $contact_coldform_options = get_option('contact_coldform_options'); settings_fields('contact_coldform_plugin_options'); ?>

			<div class="metabox-holder">
				<div class="meta-box-sortables ui-sortable">
					<div id="mm-panel-overview" class="postbox">
						<h3><?php _e('Overview'); ?></h3>
						<div class="toggle default-hidden">
							<div class="mm-panel-overview">
								<p>
									<strong><?php echo $contact_coldform_plugin; ?></strong> <?php _e(' delivers a lightweight, clean-markup contact-form that doesn&rsquo;t require JavaScript.'); ?>
									<?php _e('Use the shortcode to display the Coldform on a post or page. Use the template tag to display the Coldform anywhere in your theme template.'); ?>
								</p>
								<ul>
									<li><?php _e('To configure the Coldform, visit the'); ?> <a id="mm-panel-primary-link" href="#mm-panel-primary"><?php _e('Coldform Options'); ?></a>.</li>
									<li><?php _e('For the shortcode and template tag, visit'); ?> <a id="mm-panel-secondary-link" href="#mm-panel-secondary"><?php _e('Shortcodes &amp; Template Tags'); ?></a>.</li>
									<li><?php _e('By default, some basic CSS styles are applied to the Coldform. To choose different styles and to customize further, visit'); ?> <a id="mm-panel-tertiary-link" href="#mm-panel-tertiary"><?php _e('Appearance &amp; Styles'); ?></a>.</li>
									<li><?php _e('For more information check the <code>readme.txt</code> and'); ?> <a href="<?php echo $contact_coldform_homeurl; ?>"><?php _e('Coldform Homepage'); ?></a>.</li>
								</ul>
							</div>
						</div>
					</div>
					<div id="mm-panel-primary" class="postbox">
						<h3><?php _e('Coldform Options'); ?></h3>
						<div class="toggle<?php if (!isset($_GET["settings-updated"])) { echo ' default-hidden'; } ?>">
							<p><?php _e('Use these settings to configure and customize Contact Coldform.'); ?></p>
							<h4><?php _e('General options'); ?></h4>
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_email]"><?php _e('Your Email'); ?></label></th>
										<td><input type="text" size="50" maxlength="200" name="contact_coldform_options[coldform_email]" value="<?php echo $contact_coldform_options['coldform_email']; ?>" />
										<div class="mm-item-caption"><?php _e('Where shall Coldform send your messages?'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_name]"><?php _e('Your Name'); ?></label></th>
										<td><input type="text" size="50" maxlength="200" name="contact_coldform_options[coldform_name]" value="<?php echo $contact_coldform_options['coldform_name']; ?>" />
										<div class="mm-item-caption"><?php _e('To whom shall Coldform address your messages?'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_website]"><?php _e('Your Website'); ?></label></th>
										<td><input type="text" size="50" maxlength="200" name="contact_coldform_options[coldform_website]" value="<?php echo $contact_coldform_options['coldform_website']; ?>" />
										<div class="mm-item-caption"><?php _e('What is the name of your blog or website?'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_subject]"><?php _e('Default Subject'); ?></label></th>
										<td><input type="text" size="50" maxlength="200" name="contact_coldform_options[coldform_subject]" value="<?php echo $contact_coldform_options['coldform_subject']; ?>" />
										<div class="mm-item-caption"><?php _e('This will be the subject of the email if none is specified.'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_prefix]"><?php _e('Subject Prefix'); ?></label></th>
										<td><input type="text" size="50" maxlength="200" name="contact_coldform_options[coldform_prefix]" value="<?php echo $contact_coldform_options['coldform_prefix']; ?>" />
										<div class="mm-item-caption"><?php _e('This will be prepended to any subject specified by the sender.'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_question]"><?php _e('Challenge Question'); ?></label></th>
										<td><input type="text" size="50" maxlength="200" name="contact_coldform_options[coldform_question]" value="<?php echo $contact_coldform_options['coldform_question']; ?>" />
										<div class="mm-item-caption"><?php _e('This question must be answered correctly before mail is sent.'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_response]"><?php _e('Challenge Response'); ?></label></th>
										<td><input type="text" size="50" maxlength="200" name="contact_coldform_options[coldform_response]" value="<?php echo $contact_coldform_options['coldform_response']; ?>" />
										<div class="mm-item-caption"><?php _e('This is the only correct answer to the challenge question.'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_casing]"><?php _e('Case Sensitivity'); ?></label></th>
										<td><input type="checkbox" name="contact_coldform_options[coldform_casing]" value="1" <?php if (isset($contact_coldform_options['coldform_casing'])) { checked('1', $contact_coldform_options['coldform_casing']); } ?> /> 
										<?php _e('Check this box if the challenge response should be case-insensitive.'); ?></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_trust]"><?php _e('Trust Registered Users'); ?></label></th>
										<td><input type="checkbox" name="contact_coldform_options[coldform_trust]" value="1" <?php if (isset($contact_coldform_options['coldform_trust'])) { checked('1', $contact_coldform_options['coldform_trust']); } ?> /> 
										<?php _e('Check this box to disable the challenge question for registered users.'); ?></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_carbon]"><?php _e('Carbon Copies'); ?></label></th>
										<td><input type="checkbox" name="contact_coldform_options[coldform_carbon]" value="1" <?php if (isset($contact_coldform_options['coldform_carbon'])) { checked('1', $contact_coldform_options['coldform_carbon']); } ?> /> 
										<?php _e('Check this box if you want to enable users to receive carbon copies.'); ?></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_offset]"><?php _e('Time Offset'); ?></label></th>
										<td>
											<input type="text" size="50" maxlength="200" name="contact_coldform_options[coldform_offset]" value="<?php echo $contact_coldform_options['coldform_offset']; ?>" />
											<div class="mm-item-caption">
												<?php _e('Please specify any time offset here. If no offset, enter "0" (zero).'); ?><br />
												<?php _e('Current Coldform time:'); ?> <?php echo date("l, F jS, Y @ g:i a", time()+$offset*60*60); ?>
											</div>
										</td>
									</tr>
								</table>
							</div>
							<h4><?php _e('Coldform captions'); ?></h4>
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_nametext]"><?php _e('Caption for Name Field'); ?></label></th>
										<td><input type="text" size="50" maxlength="200" name="contact_coldform_options[coldform_nametext]" value="<?php echo $contact_coldform_options['coldform_nametext']; ?>" />
										<div class="mm-item-caption"><?php _e('This is the caption that corresponds with the Name field.'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_mailtext]"><?php _e('Caption for Email Field'); ?></label></th>
										<td><input type="text" size="50" maxlength="200" name="contact_coldform_options[coldform_mailtext]" value="<?php echo $contact_coldform_options['coldform_mailtext']; ?>" />
										<div class="mm-item-caption"><?php _e('This is the caption that corresponds with the Email field.'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_sitetext]"><?php _e('Caption for Website Field'); ?></label></th>
										<td><input type="text" size="50" maxlength="200" name="contact_coldform_options[coldform_sitetext]" value="<?php echo $contact_coldform_options['coldform_sitetext']; ?>" />
										<div class="mm-item-caption"><?php _e('This is the caption that corresponds with the Website field.'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_subjtext]"><?php _e('Caption for Subject Field'); ?></label></th>
										<td><input type="text" size="50" maxlength="200" name="contact_coldform_options[coldform_subjtext]" value="<?php echo $contact_coldform_options['coldform_subjtext']; ?>" />
										<div class="mm-item-caption"><?php _e('This is the caption that corresponds with the Subject field.'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_messtext]"><?php _e('Caption for Message Field'); ?></label></th>
										<td><input type="text" size="50" maxlength="200" name="contact_coldform_options[coldform_messtext]" value="<?php echo $contact_coldform_options['coldform_messtext']; ?>" />
										<div class="mm-item-caption"><?php _e('This is the caption that corresponds with the Message field.'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_copytext]"><?php _e('Caption for Carbon Copy'); ?></label></th>
										<td><input type="text" size="50" maxlength="200" name="contact_coldform_options[coldform_copytext]" value="<?php echo $contact_coldform_options['coldform_copytext']; ?>" />
										<div class="mm-item-caption"><?php _e('This caption corresponds with the Carbon Copy checkbox.'); ?></div></td>
									</tr>
								</table>
							</div>
							<h4><?php _e('Success &amp; error messages'); ?></h4>
							<p><?php _e('Note: use single quotes for attributes, for example: <code>style=\'margin:10px;color:red;\'</code>'); ?></p>
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_welcome]"><?php _e('Welcome Message'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="contact_coldform_options[coldform_welcome]"><?php echo esc_textarea($contact_coldform_options['coldform_welcome']); ?></textarea>
										<div class="mm-item-caption"><?php _e('This text/markup will appear before the Coldform, in the <code>&lt;legend&gt;</code> tag.'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_success]"><?php _e('Success Message'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="contact_coldform_options[coldform_success]"><?php echo esc_textarea($contact_coldform_options['coldform_success']); ?></textarea>
										<div class="mm-item-caption"><?php _e('When the form is sucessfully submitted, this success message will be displayed to the sender.'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_thanks]"><?php _e('Thank You Message'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="contact_coldform_options[coldform_thanks]"><?php echo esc_textarea($contact_coldform_options['coldform_thanks']); ?></textarea>
										<div class="mm-item-caption"><?php _e('When the form is sucessfully submitted, this thank-you message will be displayed to the sender.'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_spam]"><?php _e('Incorrect Response'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="contact_coldform_options[coldform_spam]"><?php echo esc_textarea($contact_coldform_options['coldform_spam']); ?></textarea>
										<div class="mm-item-caption"><?php _e('When the challenge question is answered incorrectly, this message will be displayed.'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_error]"><?php _e('Error Message'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="contact_coldform_options[coldform_error]"><?php echo esc_textarea($contact_coldform_options['coldform_error']); ?></textarea>
										<div class="mm-item-caption"><?php _e('If the user skips a required field, this message will be displayed.'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_style]"><?php _e('Error Fields'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="contact_coldform_options[coldform_style]"><?php echo esc_textarea($contact_coldform_options['coldform_style']); ?></textarea>
										<div class="mm-item-caption"><?php _e('Here you may specify the default CSS for error fields, or add other attributes.'); ?></div></td>
									</tr>
								</table>
							</div>
							<input type="submit" class="button-primary" value="<?php _e('Save Settings'); ?>" />
						</div>
					</div>
					<div id="mm-panel-tertiary" class="postbox">
						<h3><?php _e('Appearance &amp; Styles'); ?></h3>
						<div class="toggle<?php if (!isset($_GET["settings-updated"])) { echo ' default-hidden'; } ?>">
							<h4><?php _e('Coldskin'); ?></h4>
							<p><?php _e('Default Coldskin styles are enabled by default. Here you may choose different Coldskin and/or add your own custom CSS styles. Note: for a complete list of CSS hooks for the Coldform, visit:'); ?> 
								<a href="http://m0n.co/b" target="_blank">http://m0n.co/b</a></p>
							<div class="mm-table-wrap">
								<table class="widefat mm-table">
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_coldskin]"><?php _e('Choose a Coldskin'); ?></label></th>
										<td>
											<?php if (!isset($checked)) $checked = '';
												foreach ($coldform_coldskins as $coldform_coldskin) {
													$radio_setting = $contact_coldform_options['coldform_coldskin'];
													if ('' != $radio_setting) {
														if ($contact_coldform_options['coldform_coldskin'] == $coldform_coldskin['value']) {
															$checked = "checked=\"checked\"";
														} else {
															$checked = '';
														}
													} ?>
													<div class="mm-radio-inputs">
														<input type="radio" name="contact_coldform_options[coldform_coldskin]" value="<?php esc_attr_e($coldform_coldskin['value']); ?>" <?php echo $checked; ?> /> 
														<?php echo $coldform_coldskin['label']; ?>
													</div>
											<?php } ?>
										</td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_styles]"><?php _e('Enable Coldskin?'); ?></label></th>
										<td><input name="contact_coldform_options[coldform_styles]" type="checkbox" value="1" <?php if (isset($contact_coldform_options['coldform_styles'])) { checked('1', $contact_coldform_options['coldform_styles']); } ?> /> 
										<?php _e('Here you may enable/disable the Coldskin selected above.'); ?></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_custom]"><?php _e('Custom Styles'); ?></label></th>
										<td><textarea class="textarea" rows="3" cols="50" name="contact_coldform_options[coldform_custom]"><?php echo esc_textarea($contact_coldform_options['coldform_custom']); ?></textarea>
										<div class="mm-item-caption"><?php _e('Here you may use any additional CSS to style the Coldform. For example:'); ?>
										<code>#coldform { margin: 10px; }</code> <?php _e('(do not include'); ?> <code>&lt;style&gt;</code> <?php _e('tags). Leave blank to disable.'); ?></div></td>
									</tr>
									<tr>
										<th scope="row"><label class="description" for="contact_coldform_options[coldform_url]"><?php _e('Coldform URL'); ?></label></th>
										<td><input type="text" size="50" maxlength="200" name="contact_coldform_options[coldform_url]" value="<?php echo $contact_coldform_options['coldform_url']; ?>" />
										<div class="mm-item-caption"><?php _e('By default, Coldform displays enabled styles on <em>every</em> page. To prevent this, and to display CSS styles only for the Coldform, enter the URL where it&rsquo;s displayed.'); ?></div></td>
									</tr>
								</table>
							</div>
							<input type="submit" class="button-primary" value="<?php _e('Save Settings'); ?>" />
						</div>
					</div>
					<div id="mm-panel-secondary" class="postbox">
						<h3><?php _e('Shortcodes &amp; Template Tags'); ?></h3>
						<div class="toggle<?php if (!isset($_GET["settings-updated"])) { echo ' default-hidden'; } ?>">
							<h4><?php _e('Shortcode'); ?></h4>
							<p><?php _e('Use this shortcode to display the Coldform on a post or page:'); ?></p>
							<p><code class="mm-code">[coldform]</code></p>
							<h4><?php _e('Template tag'); ?></h4>
							<p><?php _e('Use this template tag to display the Coldform anywhere in your theme template:'); ?></p>
							<p><code class="mm-code">&lt;?php if (function_exists('contact_coldform_public')) contact_coldform_public(); ?&gt;</code></p>
						</div>
					</div>
					<div id="mm-restore-settings" class="postbox">
						<h3><?php _e('Restore Default Options'); ?></h3>
						<div class="toggle<?php if (!isset($_GET["settings-updated"])) { echo ' default-hidden'; } ?>">
							<p>
								<input name="contact_coldform_options[default_options]" type="checkbox" value="1" id="mm_restore_defaults" <?php if (isset($contact_coldform_options['default_options'])) { checked('1', $contact_coldform_options['default_options']); } ?> /> 
								<label class="description" for="contact_coldform_options[default_options]"><?php _e('Restore default options upon plugin deactivation/reactivation.'); ?></label>
							</p>
							<p>
								<small>
									<?php _e('<strong>Tip:</strong> leave this option unchecked to remember your settings. Or, to go ahead and restore all default options, check the box, save your settings, and then deactivate/reactivate the plugin.'); ?>
								</small>
							</p>
							<input type="submit" class="button-primary" value="<?php _e('Save Settings'); ?>" />
						</div>
					</div>
					<div id="mm-panel-current" class="postbox">
						<h3><?php _e('Updates &amp; Info'); ?></h3>
						<div class="toggle default-hidden">
							<div id="mm-iframe-wrap">
								<iframe src="http://perishablepress.com/current/index-cc.html"></iframe>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="mm-credit-info">
				<a target="_blank" href="<?php echo $contact_coldform_homeurl; ?>" title="<?php echo $contact_coldform_plugin; ?> Homepage"><?php echo $contact_coldform_plugin; ?></a> by 
				<a target="_blank" href="http://twitter.com/perishable" title="Jeff Starr on Twitter">Jeff Starr</a> @ 
				<a target="_blank" href="http://monzilla.biz/" title="Obsessive Web Design &amp; Development">Monzilla Media</a>
			</div>
		</form>
	</div>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			// toggle panels
			jQuery('.default-hidden').hide();
			jQuery('#mm-panel-toggle a').click(function(){
				jQuery('.toggle').slideToggle(300);
				return false;
			});
			jQuery('h3').click(function(){
				jQuery(this).next().slideToggle(300);
			});
			jQuery('#mm-panel-primary-link').click(function(){
				jQuery('.toggle').hide();
				jQuery('#mm-panel-primary .toggle').slideToggle(300);
				return true;
			});
			jQuery('#mm-panel-secondary-link').click(function(){
				jQuery('.toggle').hide();
				jQuery('#mm-panel-secondary .toggle').slideToggle(300);
				return true;
			});
			jQuery('#mm-panel-tertiary-link').click(function(){
				jQuery('.toggle').hide();
				jQuery('#mm-panel-tertiary .toggle').slideToggle(300);
				return true;
			});
			// prevent accidents
			if(!jQuery("#mm_restore_defaults").is(":checked")){
				jQuery('#mm_restore_defaults').click(function(event){
					var r = confirm("<?php _e('Are you sure you want to restore all default options? (this action cannot be undone)'); ?>");
					if (r == true){  
						jQuery("#mm_restore_defaults").attr('checked', true);
					} else {
						jQuery("#mm_restore_defaults").attr('checked', false);
					}
				});
			}
		});
	</script>

<?php } ?>