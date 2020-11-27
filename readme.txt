Kingmailer SMTP
=====================

Contributors: Krishna Moniz, Navin Poeran
Tags: kingmailer, smtp, http, api, mail, email, routing
Requires at least: 3.3
Tested up to: 5.5.1
Stable tag: 0.2
License: GPLv2 or later


Easily send email from your WordPress site through Kingmailer using the HTTP API.


== Description ==

[Kingmailer](https://kingmailer.co/) is a simple and fully featured mail server / SMTP service build for sending transactional emails. Think Sendgrid and Mailgun but our focus is solely on delivery of transactional emails. Start sending, receiving or routing emails with a few clicks.

Our service is build for people who want a mail server / SMTP service to be simple and easy to use. Every month, thousands of emails are sent, received, routed using our delightful and powerful SMTP service. Try for free.

This plugin provides you with a way to send email when the server you are on does not support SMTP or where outbound SMTP is restricted by using the Kingmailer HTTP API for sending email. 

This plugin is based on the Mailgun for WordPress plugin and also WP Mail SMTP . 

== Installation ==

1. Upload the `kingmailer-smtp` folder to the `/wp-content/plugins/` directory or install directly through the plugin installer
2. Activate the plugin through the 'Plugins' menu in WordPress or by using the link provided by the plugin installer
3. Visit the settings page in the Admin at `Settings -> Kingmailer` and configure the plugin with your account details
4. Click the Test Configuration button to verify that your settings are correct.


== Frequently Asked Questions ==

- Testing the configuration fails when using the HTTP API

Your web server may not allow outbound HTTP connections. Set `Use HTTP API` to "No", and fill out the configuration options to SMTP and test again. A different possibility may be: your server IP is blocked by Spamhaus or other services that regulates spam. Please check if your IP is not blacklisted. 

- Can this be configured globally for WordPress Multisite?

This plugin is not yet tested with WordPress Multisite.

- The plain text alternative of my mail looks different from my template.

Plain text alternatives are for mail clients that cannot process HTML. As such, html tags are stripped from the plain text alternative. This is done before applying the wp_mail filters.

== Screenshots ==

1. Configuration options for using the Kingmailer HTTP API
2. Configuration options for using the Kingmailer SMTP servers


== Changelog ==

= 0.2 (2020-11-22): =
* Updated text

= 0.1 (2020-10-17): =
* Initial Release
