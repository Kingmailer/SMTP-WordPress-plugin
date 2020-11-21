Kingmailer for WordPress
=====================

Contributors: Krishna Moniz, Mailgun, sivel, lookahead.io, m35dev
Tags: kingmailer, smtp, http, api, mail, email
Requires at least: 3.3
Tested up to: 5.5.1
Stable tag: 0.2
License: GPLv2


Easily send email from your WordPress site through Kingmailer using the HTTP API or SMTP.


== Description ==

[Kingmailer](http://www.kingmailer.co/) is an easy to use email service build for anyone, not just developers. Give Kingmailer a try.

This plugin provides you with a way to send email when the server you are on does not support SMTP or where outbound SMTP is restricted by using the Kingmailer HTTP API for sending email. 

All you need to use the plugin is a [Kingmailer account](https://kingmailer.co/). No credit card required. Upgrade or cancel anytime. We accept credit card, PayPal and Bitcoin payments.

This plugin is based on the excellent [Mailgun for wordpress](https://wordpress.org/plugins/mailgun/) plugin. 

== Installation ==

1. Upload the `kingmailer` folder to the `/wp-content/plugins/` directory or install directly through the plugin installer
2. Activate the plugin through the 'Plugins' menu in WordPress or by using the link provided by the plugin installer
3. Visit the settings page in the Admin at `Settings -> Kingmailer` and configure the plugin with your account details
4. Click the Test Configuration button to verify that your settings are correct.


== Frequently Asked Questions ==

- Testing the configuration fails when using the HTTP API

Your web server may not allow outbound HTTP connections. Set `Use HTTP API` to "No", and fill out the configuration options to SMTP and test again.

- Testing the configuration fails when using SMTP

Your web server may not allow outbound SMTP connections on port 465 for secure connections or 587 for unsecured connections. Try changing `Use Secure SMTP` to "No" or "Yes" depending on your current configuration and testing again. If both fail, try setting `Use HTTP API` to "Yes" and testing again.

If you *have* to use SMTP and something is still going horribly wrong, enable debug mode in WordPress and also add the `KM_DEBUG_SMTP` constant to your `wp-config.php`, like so:

`
define( 'KM_DEBUG_SMTP', true );
`

- Can this be configured globally for WordPress Multisite?

Yes, using the following constants that can be placed in wp-config.php:

`
KINGMAILER_HOST       Type: string
     ex. define('KINGMAILER_HOST', 'kingmailer.org');
KINGMAILER_USEAPI       Type: boolean  Choices: '0' or '1' (0 = false/no)
KINGMAILER_APIKEY       Type: string
KINGMAILER_DOMAIN       Type: string
KINGMAILER_USERNAME     Type: string
KINGMAILER_PASSWORD     Type: string
KINGMAILER_SECURE       Type: boolean  Choices: '0' or '1' (0 = false/no)
KINGMAILER_SECTYPE      Type: string   Choices: 'ssl' or 'tls'
KINGMAILER_FROM_NAME    Type: string
KINGMAILER_FROM_ADDRESS Type: string
`

- What hooks are available for use with other plugins?

`km_use_recipient_vars_syntax` (TODO krishna remove)
  Mutates messages to use recipient variables syntax - see
  https://documentation.mailgun.com/user_manual.html#batch-sending for more info.

  Should accept a list of `To` addresses.

  Should *only* return `true` or `false`.

`km_mutate_message_body`
  Allows an external plugin to mutate the message body before sending.

  Should accept an array, `$body`.

  Should return a new array to replace `$body`.

`km_mutate_attachments`
  Allows an external plugin to mutate the attachments on the message before
  sending.

  Should accept an array, `$attachments`.

  Should return a new array to replace `$attachments`.


- The plain text alternative of my mail looks different from my template.

Plain text alternatives are for mail clients that cannot process HTML. As such, html tags are stripped from the plain text alternative. This is done before applying the wp_mail filters.

== Screenshots ==

1. Configuration options for using the Kingmailer HTTP API
2. Configuration options for using the Kingmailer SMTP servers


== Changelog ==

= 0.1 (2020-10-17): =
* Initial Release

