<?php // Coldform Admin Settings Page

global $coldform_version;
load_plugin_textdomain('coldform', $path = 'wp-content/plugins/contact-coldform');
$location = get_option('siteurl') . '/wp-admin/admin.php?page=contact-coldform/coldform_options.php';

/* set default contact form options */

	add_option('coldform_name',     __('Your Name', 'coldform'));
	add_option('coldform_website',  __('Your Site', 'coldform'));
	add_option('coldform_email',    __('your-email@domain.com', 'coldform'));
	add_option('coldform_offset',   __('For example, +1 or -1', 'coldform'));
	add_option('coldform_subject',  __('Message sent from your contact form.', 'coldform'));
	add_option('coldform_success',  __('<h3 id="coldform_success">Success! Your message has been sent.</h3>', 'coldform'));
	add_option('coldform_error',    __('<h3 id="coldform_error">Please complete the required fields.</h3>', 'coldform'));
	add_option('coldform_spam',     __('<h3 id="coldform_spam">Incorrect response for challenge question. Please try again.</h3>', 'coldform'));
	add_option('coldform_style',    __('style="border: 1px solid #CC0000;"', 'coldform'));
	add_option('coldform_question', __('1 + 1 =', 'coldform'));
	add_option('coldform_response', __('2', 'coldform'));
	add_option('coldform_casing',   __('FALSE', 'coldform'));
	add_option('coldform_carbon',   __('FALSE', 'coldform'));
	add_option('coldform_credit',   __('FALSE', 'coldform'));
	add_option('coldform_nametext', __('Name (Required)', 'coldform'));
	add_option('coldform_mailtext', __('Email (Required)', 'coldform'));
	add_option('coldform_sitetext', __('Website (Optional)', 'coldform'));
	add_option('coldform_subjtext', __('Subject (Optional)', 'coldform'));
	add_option('coldform_messtext', __('Message (Required)', 'coldform'));
	add_option('coldform_copytext', __('Carbon Copy?', 'coldform'));

/* check form submission and update options */

if ('process' == $_POST['coldform_set']) {

	update_option('coldform_name',     $_POST['coldform_name']);
	update_option('coldform_website',  $_POST['coldform_website']);
	update_option('coldform_email',    $_POST['coldform_email']);
	update_option('coldform_offset',   $_POST['coldform_offset']);
	update_option('coldform_subject',  $_POST['coldform_subject']);
	update_option('coldform_success',  $_POST['coldform_success']);
	update_option('coldform_error',    $_POST['coldform_error']);
	update_option('coldform_spam',     $_POST['coldform_spam']);
	update_option('coldform_style',    $_POST['coldform_style']);
	update_option('coldform_question', $_POST['coldform_question']);
	update_option('coldform_response', $_POST['coldform_response']);
	if ($_POST['coldform_casing'] == 'TRUE') {
		update_option('coldform_casing', 'TRUE');
	} else {
		update_option('coldform_casing', 'FALSE');
	}
	if ($_POST['coldform_carbon'] == 'TRUE') {
		update_option('coldform_carbon', 'TRUE');
	} else {
		update_option('coldform_carbon', 'FALSE');
	}
	update_option('coldform_credit',   $_POST['coldform_credit']);
	update_option('coldform_nametext', $_POST['coldform_nametext']);
	update_option('coldform_mailtext', $_POST['coldform_mailtext']);
	update_option('coldform_sitetext', $_POST['coldform_sitetext']);
	update_option('coldform_subjtext', $_POST['coldform_subjtext']);
	update_option('coldform_messtext', $_POST['coldform_messtext']);
	update_option('coldform_copytext', $_POST['coldform_copytext']);
}

/* specify options for form fields */

	$coldform_name     = stripslashes(get_option('coldform_name'));
	$coldform_website  = stripslashes(get_option('coldform_website'));
	$coldform_email    = stripslashes(get_option('coldform_email'));
	$coldform_offset   = stripslashes(get_option('coldform_offset'));
	$coldform_subject  = stripslashes(get_option('coldform_subject'));
	$coldform_success  = stripslashes(get_option('coldform_success'));
	$coldform_error    = stripslashes(get_option('coldform_error'));
	$coldform_spam     = stripslashes(get_option('coldform_spam'));
	$coldform_style    = stripslashes(get_option('coldform_style'));
	$coldform_question = stripslashes(get_option('coldform_question'));
	$coldform_response = stripslashes(get_option('coldform_response'));
	$coldform_casing   = get_option('coldform_casing');
	$coldform_carbon   = get_option('coldform_carbon');
	$coldform_credit   = stripslashes(get_option('coldform_credit'));
	$coldform_nametext = stripslashes(get_option('coldform_nametext'));
	$coldform_mailtext = stripslashes(get_option('coldform_mailtext'));
	$coldform_sitetext = stripslashes(get_option('coldform_sitetext'));
	$coldform_subjtext = stripslashes(get_option('coldform_subjtext'));
	$coldform_messtext = stripslashes(get_option('coldform_messtext'));
	$coldform_copytext = stripslashes(get_option('coldform_copytext'));

?>

<div class="wrap">
	<h2><?php _e('Welcome to Coldform <small>[v:', 'coldform') ?><?php echo $coldform_version." ]</small>"; ?></h2>
	<table width="100%" cellspacing="2" cellpadding="5" class="editform">
		<tr valign="top">
			<td><?php _e('<strong>To use Coldform, follow these three steps:</strong>', 'coldform') ?><br />
				<ol>
					<li><?php _e('Customize your preferences via this options page.', 'coldform') ?></li>
					<li><?php _e('Insert &lt;<code>!--coldform--</code>&gt; into any post or page.', 'coldform') ?></li>
					<li><?php _e('Visit <a href="http://perishablepress.com/press/2008/01/08/contact-coldform/" title="Contact Coldform @ Perishable Press">Perishable Press</a> for more information, updates, and general chills.', 'coldform') ?></li>
				</ol>
			</td>
		</tr>
	</table>
	<form name="form1" method="post" action="<?php echo $location ?>&amp;updated=true">
		<input type="hidden" name="coldform_set" value="process" />
		<fieldset class="options">
			<legend><?php _e('General Options', 'coldform') ?></legend>
			<table width="100%" cellspacing="2" cellpadding="5" class="editform">
				<tr valign="top">
					<th scope="row"><?php _e('Your Email:', 'coldform') ?></th>
					<td><input type="text" size="55" name="coldform_email" id="coldform_email" value="<?php echo $coldform_email; ?>" />
					<br /><?php _e('Where shall Coldform send your messages?', 'coldform') ?></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Your Name:', 'coldform') ?></th>
					<td><input type="text" size="55" name="coldform_name" id="coldform_name" value="<?php echo $coldform_name; ?>" />
					<br /><?php _e('To whom shall Coldform address your messages?', 'coldform') ?></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Your Website:', 'coldform') ?></th>
					<td><input type="text" size="55" name="coldform_website" id="coldform_website" value="<?php echo $coldform_website; ?>" />
					<br /><?php _e('What is the name of your blog or website?', 'coldform') ?></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Default Subject:', 'coldform') ?></th>
					<td><input type="text" size="55" name="coldform_subject" id="coldform_subject" value="<?php echo $coldform_subject; ?>" />
					<br /><?php _e('This will be the subject of the email if none is specified.', 'coldform') ?></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Challenge Question:', 'coldform') ?></th>
					<td><input type="text" size="55" name="coldform_question" id="coldform_question" value="<?php echo $coldform_question; ?>" />
					<br /><?php _e('This question must be answered correctly before mail is sent.', 'coldform') ?></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Challenge Response:', 'coldform') ?></th>
					<td><input type="text" size="55" name="coldform_response" id="coldform_response" value="<?php echo $coldform_response; ?>" />
					<br /><?php _e('This is the only correct answer to the challenge question.', 'coldform') ?></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Case Sensitivity:', 'coldform') ?></th>
					<td><input type="checkbox" name="coldform_casing" id="coldform_casing" value="TRUE" <?php if ($coldform_casing == "TRUE") { echo "checked=\"checked\""; } ?> />
					<br /><?php _e('Check this box if the challenge response should be case-insensitive.', 'coldform') ?></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Carbon Copies:', 'coldform') ?></th>
					<td><input type="checkbox" name="coldform_carbon" id="coldform_carbon" value="TRUE" <?php if ($coldform_carbon == "TRUE") { echo "checked=\"checked\""; } ?> />
					<br /><?php _e('Check this box if you want to enable users to receive carbon copies.', 'coldform') ?></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Time Offset:', 'coldform') ?></th>
					<td><input type="text" size="55" name="coldform_offset" id="coldform_offset" value="<?php echo $coldform_offset; ?>" />
					<br /><?php _e('Please specify any time offset here. If no offset, enter "0" (zero).', 'coldform') ?>
					<br /><?php _e('Current Coldform time:', 'coldform') ?> <?php echo date("l, F jS, Y @ g:i a", time()+get_option('coldform_offset')*60*60); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Share Link:', 'coldform') ?></th>
					<td><input type="checkbox" name="coldform_credit" id="coldform_credit" value="TRUE" <?php if ($coldform_credit == "TRUE") { echo "checked=\"checked\""; } ?> />
					<br /><?php _e('Share a link to the Coldform homepage?', 'coldform') ?></td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><?php _e('Field Captions', 'coldform') ?></legend>
			<table width="100%" cellspacing="2" cellpadding="5" class="editform">
				<tr valign="top">
					<th scope="row"><?php _e('Caption for Name Field:', 'coldform') ?></th>
					<td><input type="text" size="55" name="coldform_nametext" id="coldform_nametext" value="<?php echo $coldform_nametext; ?>" />
					<br /><?php _e('This is the caption that corresponds with the Name field.', 'coldform') ?></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Caption for Email Field:', 'coldform') ?></th>
					<td><input type="text" size="55" name="coldform_mailtext" id="coldform_mailtext" value="<?php echo $coldform_mailtext; ?>" />
					<br /><?php _e('This is the caption that corresponds with the Email field.', 'coldform') ?></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Caption for Website Field:', 'coldform') ?></th>
					<td><input type="text" size="55" name="coldform_sitetext" id="coldform_sitetext" value="<?php echo $coldform_sitetext; ?>" />
					<br /><?php _e('This is the caption that corresponds with the Website field.', 'coldform') ?></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Caption for Subject Field:', 'coldform') ?></th>
					<td><input type="text" size="55" name="coldform_subjtext" id="coldform_subjtext" value="<?php echo $coldform_subjtext; ?>" />
					<br /><?php _e('This is the caption that corresponds with the Subject field.', 'coldform') ?></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Caption for Message Field:', 'coldform') ?></th>
					<td><input type="text" size="55" name="coldform_messtext" id="coldform_messtext" value="<?php echo $coldform_messtext; ?>" />
					<br /><?php _e('This is the caption that corresponds with the Message field.', 'coldform') ?></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Caption for Carbon Copy:', 'coldform') ?></th>
					<td><input type="text" size="55" name="coldform_copytext" id="coldform_copytext" value="<?php echo $coldform_copytext; ?>" />
					<br /><?php _e('This caption corresponds with the Carbon Copy checkbox.', 'coldform') ?></td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><?php _e('Default Messages', 'coldform') ?></legend>
			<table width="100%" cellspacing="2" cellpadding="5" class="editform">
				<tr valign="top">
					<th scope="row"><?php _e('Success Message:', 'coldform') ?></th>
					<td><textarea rows="5" cols="55" name="coldform_success" id="coldform_success" style="width: 77%;"><?php echo $coldform_success; ?></textarea>
					<br /><?php _e('When the form is sucessfully submitted, this message will be displayed to the sender.', 'coldform') ?></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Incorrect Response:', 'coldform') ?></th>
					<td><textarea rows="5" cols="55" name="coldform_spam" id="coldform_spam" style="width: 77%;"><?php echo $coldform_spam; ?></textarea>
					<br /><?php _e('When the challenge question is answered incorrectly, this message will be displayed.', 'coldform') ?></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Error Message:', 'coldform') ?></th>
					<td><textarea rows="5" cols="55" name="coldform_error" id="coldform_error" style="width: 77%;"><?php echo $coldform_error; ?></textarea>
					<br /><?php _e('If the user skips a required field, this message will be displayed.', 'coldform') ?></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Error Fields', 'coldform') ?></th>
					<td><textarea rows="5" cols="55" name="coldform_style" id="coldform_style" style="width: 77%;"><?php echo $coldform_style; ?></textarea>
					<br /><?php _e('Here you may specify the default CSS for error fields, or add other attributes.', 'coldform') ?></td>
				</tr>
			</table>
		</fieldset>
		<p class="submit">
			<input type="submit" name="submit" value="<?php _e('Update Coldform Options', 'coldform') ?> &raquo;" />
		</p>
	</form>
	<table width="100%" cellspacing="2" cellpadding="5" class="editform">
		<tr valign="top">
			<td>
				<?php _e('<strong>Thanks for using Contact Coldform!</strong>', 'coldform') ?><br />
				<?php _e('If you like this free plugin, please blog about it, link to it, or send a link so that I may see it in action! Thanks!', 'coldform') ?>
			</td>
		</tr>
	</table>
</div>