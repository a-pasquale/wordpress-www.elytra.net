=== Plugin Name ===
Contributors: utkarsh
Donate link: http://utkar.sh
Tags: typekit, typography, webfonts, fonts
Requires at least: 2.9
Tested up to: 3.1-alpha
Stable tag: trunk

Allows you to add Typekit fonts to your site, by targetting them to specific elements using css selectors from the admin panel.
== Description ==

This plugin allows you to add Typekit fonts to your site, by targetting them to specific elements using css selectors from the admin panel. It uses the new Typekit API to fetch fonts' info from your account.

Enter your Typekit API key from Settings -> Advanced Typekit, and the plugin will fetch all the fonts you've added to your kit.
Enter the css selectors you want to target for each font, along with any extra css.
The extra css is only applied when the browser has loaded the font.

This plugin uses the Google WebFont Loader to load your Typekit fonts.

Click on the Screenshot link above to preview the plugins admin page.

Note: You need PHP5 on your server, and a Typekit account to use this plugin.

== Installation ==

1. Upload `advanced-typekit` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. Pretty much self explanatory. If you wish to add a specific custom css for a selector, make sure they both are
 in the same line (each line can have unique extra css). If you wish to apply the same extra css to several selectors,
 separate them with a `,`, just as you would do in css.

== Changelog ==
= 1.0.1 =
* Added an option to manually disable plugin output

= 1.0 =
* Initial Version
