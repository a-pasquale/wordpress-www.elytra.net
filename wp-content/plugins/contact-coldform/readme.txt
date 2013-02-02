=== Contact Coldform ===

Plugin Name: Contact Coldform
Plugin URI: http://perishablepress.com/contact-coldform/
Description: Secure, lightweight and flexible contact form with plenty of options and squeaky clean markup.
Tags: captcha, contact, contact form, email, form, mail
Author: Jeff Starr
Author URI: http://monzilla.biz/
Contributors: specialk
Donate link: http://digwp.com/book/
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 20130103
Version: 20130103
License: GPL v2

Contact Coldform is a secure, lightweight and flexible contact form with plenty of options and squeaky clean markup.

== Description ==

[Contact Coldform](http://perishablepress.com/contact-coldform/) is a secure, lightweight, flexible contact form with plenty of options and squeaky clean markup. Coldform blocks spam while making it easy for your visitors to contact you from your WordPress-powered website. The comprehensive Settings Page makes it easy to take full control with plenty of options and several built-in themes for styling the form. Coldform delivers everything you need and nothing you don&rsquo;t -- no frills, no gimmicks, just pure contact-form satisfaction.

**Overview**

* Plug-n-play: use shortcode or template tag to display Coldform anywhere
* Sweet emails: Coldform sends descriptive, well-formatted messages in plain-text
* Safe and secure: Coldform blocks spam and filters malicious content
* Ultra-clean code: lightweight, standards-compliant, semantic, valid HTML markup
* Fully customizable: easy to configure and style from the Coldform Settings page

**Features**

* Slick, toggling-panel Settings Page makes it easy to customize and style Coldform
* Style Coldform using built-in "coldskins" or upload some custom CSS
* Provides template tag to display Coldform anywhere in your theme
* Provides shortcode to display Coldform on any post or page
* Displays customizable confirmation message to the sender

**Anti-spam &amp; Security**

* Captcha: Coldform includes challenge question/answer (w/ option to disable for users)
* Bot trap: hidden input field further reduces automated spam
* Firewall: secure form processing protects against bad bots and malicious input
* User-friendly: same-page error messages to help users complete required fields

**Customize Everything**

* Includes option to enable users to receive carbon copies
* Coldform message includes IP, host, agent, and other user details
* Customizable form-field captions, error messages, and success message
* Includes three built-in themes "coldskins" to style, or
* Style the Coldform with your own custom CSS
* Option to add a custom prefix to the subject line
* Option to disable the captcha for registered users

**Clean Codes**

Coldform brings delicious code on every front:

* Squeaky-clean PHP: every line like a fine wine
* Crispy-clean markup: valid, semantic source code with proper formatting
* Shiny-clean emails: Coldform emails deliver descriptive, well-formatted content
* Better performance: conditional loading of styles only when Coldform is displayed

**More Features**

* Works perfectly without JavaScript.
* Option to load CSS and custom styles only when/where Coldform is displayed
* Option to reset default settings
* Options to customize many aspects of the form
* Options to customize success, error, and spam messages
* Option to enable and disable CSS styles

== Installation ==

Typical plugin install: upload, activate, and customize in the WP Admin.

1. Unzip and upload the entire directory to your "plugins" folder and activate
2. Use the shortcode to display Coldform on any post or page, or:
3. Use the template tag to display the Coldform anywhere in your theme.
4. Visit the Coldform Settings Page to configure your options and for more info.

Shortcode: `[coldform]`

Template tag: `<?php if (function_exists('contact_coldform_public')) contact_coldform_public(); ?>`

Check out the [Coldform Demo](http://bluefeed.net/wordpress/contact-coldform/) and its [CSS hooks](http://m0n.co/b).

For more information, visit the [Coldform Homepage](http://perishablepress.com/contact-coldform/).

== Upgrade Notice ==

__Important!__ Many things have changed in the new version of the plugin. Please copy/paste your current Coldform settings to a safe place. Then update the plugin as usual, using your saved settings while configuring the new version.

== Screenshots ==

Screenshots available at the [Coldform Homepage](http://perishablepress.com/contact-coldform/#screenshots).

== Changelog ==

= Version 20130103 =

* Added margins to buttons (now required due to CSS changes in WP 3.5)

= Version 20121119 =

* Now supports both shortcodes: `[coldform]` and `[contact_coldform]`
* Renamed `register_my_style()` to `contact_coldform_register_style()`
* Removed border on all fieldsets via CSS
* Added padding to input and textareas via CSS
* Replaced answer with question in anti-spam placeholder
* Added placeholder attributes to error fields
* Fixed styles to load on success page

= Version 20121031 =

* rebuilt with cleaner, smarter code
* restructured markup, cleaner hooks
* revamped settings page with toggling
* includes three "coldskins" for styling
* enable user to upload custom CSS styles
* toggle on/off the built-in coldskins
* conditional load of styles only on Coldform
* improved markup for required, error, success output
* option to disable the captcha for registered users
* now use admin email, name, site title by default
* now using built-in wp_mail for email
* removed the credit link and option
* add option for subject line prefix
* add HTML5 placeholder attributes
* add hidden anti-spam field

= Version 0.88.1 =

* Compatibility with WordPress version 2.8.1 by setting `admin_menu`.

= Version 0.88.0 =

* Initial release.

== Frequently Asked Questions ==

To ask a question, visit the [Coldform Homepage](http://perishablepress.com/contact-coldform/) or [contact me](http://perishablepress.com/contact/).

== Donations ==

I created this plugin with love for the WP community. To show support, consider purchasing my new book, [.htaccess made easy](http://htaccessbook.com/), or my WordPress book, [Digging into WordPress](http://digwp.com/).

Links, tweets and likes also appreciated. Thanks! :)