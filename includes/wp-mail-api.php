<?php

/*
 * kingmailer-wordpress-plugin - Sending mail from Wordpress using Kingmailer
 * Copyright (C) 2020 Krishna Moniz
 * Copyright (C) 2016 Mailgun, et al.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

 // Include MG filter functions
if (!include dirname(__FILE__).'/km-filter.php') {
    Kingmailer::deactivate_and_die(dirname(__FILE__).'/km-filter.php');
}

/**
 * km_api_last_error is a compound getter/setter for the last error that was
 * encountered during a Kingmailer API call.
 *
 * @param	string	$error	OPTIONAL
 *
 * @return	string	Last error that occurred.
 *
 * @since	1.5.0
 */
function km_api_last_error($error = null)
{
    static $last_error;

    if (null === $error) {
        return $last_error;
    } else {
        $tmp = $last_error;
        $last_error = $error;

        return $tmp;
    }
}

/*
 * Wordpress filter to mutate a `To` header to use recipient variables.
 * Uses the `km_use_recipient_vars_syntax` filter to apply the actual
 * change. Otherwise, just a list of `To` addresses will be returned.
 *
 * @param string|array $to_addrs Array or comma-separated list of email addresses to mutate.
 *
 * @return array Array containing list of `To` addresses and recipient vars array
 *
 * @since 1.5.7
 */
add_filter('km_mutate_to_rcpt_vars', 'km_mutate_to_rcpt_vars_cb');
function km_mutate_to_rcpt_vars_cb($to_addrs)
{
    if (is_string($to_addrs)) {
        $to_addrs = explode(',', $to_addrs);
    }

    if (has_filter('km_use_recipient_vars_syntax')) {
        $use_rcpt_vars = apply_filters('km_use_recipient_vars_syntax', null);
        if ($use_rcpt_vars) {
            $vars = array();

            $idx = 0;
            foreach ($to_addrs as $addr) {
                $rcpt_vars[$addr] = array('batch_msg_id' => $idx);
                $idx++;
            }

            // TODO: Also add folding to prevent hitting the 998 char limit on headers.
            return array(
                'to'        => '%recipient%',
                'rcpt_vars' => json_encode($rcpt_vars),
            );
        }
    }

    return array(
        'to'        => $to_addrs,
        'rcpt_vars' => null,
    );
}

/**
 * wp_mail function to be loaded in to override the core wp_mail function
 * from wp-includes/pluggable.php.
 *
 * Based off of the core wp_mail function, but with modifications required to
 * send email using the Kingmailer HTTP API
 *
 * @param	string|array	$to				Array or comma-separated list of email addresses to send message.
 * @param	string			$subject		Email subject
 * @param	string			$message		Message contents
 * @param	string|array	$headers		Optional. Additional headers.
 * @param	string|array	$attachments	Optional. Files to attach.
 *
 * @return	bool	Whether the email contents were sent successfully.
 *
 * @since	0.1
 */
function wp_mail($to, $subject, $message, $headers = '', $attachments = array())
{

    // Get the plugin options
    $kingmailer = get_option('kingmailer');
    $host = (defined('KINGMAILER_HOST') && KINGMAILER_HOST) ? KINGMAILER_HOST : $kingmailer['host'];
    $api_key = (defined('KINGMAILER_APIKEY') && KINGMAILER_APIKEY) ? KINGMAILER_APIKEY : $kingmailer['api_key'];
    $domain = (defined('KINGMAILER_DOMAIN') && KINGMAILER_DOMAIN) ? KINGMAILER_DOMAIN : $kingmailer['domain'];

    // No point in continuing if don't have an API key or domain
    if (empty($api_key) || empty($domain)) {
        return false;
    }

    // The API expects a plain text message (Create a plain text that ignores filters)
    $plain_body = strip_tags($message);








    // Compact the input, apply the filters, and extract them back out
    extract(apply_filters('wp_mail', compact('to', 'subject', 'message', 'headers', 'attachments')));

    // The API expects the mail headers as part of the body json (default behaviour html)
    $body_headers = array('Content-Type' => 'text/html; charset="UTF-8"');

    // The API allows for CC and BCC recipients (must be outside the headers)
    $cc = array();
    $bcc = array();

    // Headers
    // The Wordpress headers will either be a string with each header separated
    // by a new line or an array where each element is a header
    if (!empty($headers)) {

        if (!is_array($headers)) {
            // Explode the string headers out if necessary
            $headers = explode("\n", str_replace("\r\n", "\n", $headers));
        }

        // If it's actually got contents
        if (!empty($headers)) {
            // Iterate through the raw headers and copy them to the body_headers
            foreach ((array) $headers as $header) {
                if (strpos($header, ':') === false) {
                    if (false !== stripos($header, 'boundary=')) {
                        $parts = preg_split('/boundary=/i', trim($header));
                        $boundary = trim(str_replace(array("'", '"'), '', $parts[1]));
                    }
                    continue;
                }

                // Explode them out and clean up
                list($name, $content) = explode(':', trim($header), 2);
                $name = trim($name);
                $content = trim($content);

                switch (strtolower($name)) {
                case 'from':
                    // Mainly for legacy -- process a From: header if it's there
                    if (strpos($content, '<') !== false) {
                        $from_name = substr($content, 0, strpos($content, '<') - 1);
                        $from_name = str_replace('"', '', $from_name);
                        $from_name = trim($from_name);

                        $from_email = substr($content, strpos($content, '<') + 1);
                        $from_email = str_replace('>', '', $from_email);
                        $from_email = trim($from_email);
                    } else {
                        $from_email = trim($content);
                    }
                    break;
                case 'content-type':
                    // Pull out the content-type and charset. We'll need them later.
                    if (strpos($content, ';') !== false) {
                        list($type, $charset) = explode(';', $content);
                        $content_type = trim($type);
                        if (false !== stripos($charset, 'charset=')) {
                            $charset = trim(str_replace(array('charset=', '"'), '', $charset));
                        } elseif (false !== stripos($charset, 'boundary=')) {
                            $boundary = trim(str_replace(array('BOUNDARY=', 'boundary=', '"'), '', $charset));
                            $charset = '';
                        }
                    } else {
                        $content_type = trim($content);
                    }    
                    break;
                case 'cc':
                    $cc = array_merge((array) $cc, explode(',', $content));
                    break;
                case 'bcc':
                    $bcc = array_merge((array) $bcc, explode(',', $content));
                    break;
                default:
                    // Just copy all other headers
                    $body_headers[trim($name)] = trim($content);
                    break;
                }
            }
        }
    }

    // Content-type
    // Remember when we said, we'll use the content-type later. This is later.
    // If we are not given a Content-Type in the supplied headers, write the message body 
    // to a file and try to determine the mimetype using get_mime_content_type.
    if (!isset($content_type)) {
        $tmppath = tempnam(sys_get_temp_dir(), 'mg');
        $tmp = fopen($tmppath, 'w+');

        fwrite($tmp, $message);
        fclose($tmp);

        $content_type = get_mime_content_type($tmppath, 'text/plain');

        unlink($tmppath);
    }

    // Allow external content type filter to function normally
    if (has_filter('wp_mail_content_type')) {
        $content_type = apply_filters('wp_mail_content_type', $content_type);
    }

    // If we don't have a charset from the input headers
    if (!isset($charset)) {
        $charset = get_bloginfo('charset');
    }

    // Allow external char set filter to function normally
    if (has_filter('wp_mail_charset')) {
        $charset = apply_filters('wp_mail_charset', $charset);
    }

    // Set the content-type and charset in the headers
    if (!empty($content_type)) {
        $body_headers['Content-Type'] = $content_type;
    }
    if (isset($charset)) {
        $body_headers['Content-Type'] = rtrim($body_headers['Content-Type'], '; ')."; charset={$charset}";
    }

    // Message
    // Determine what the message body should look like
    if ('text/plain' === $content_type) {
        // $plain_body = $message;
    } else if ('text/html' === $content_type) {
        $html_body  = $message;
    } else {
        // Treat Unknown Content-Type as html 

        $html_body  = $message;
    }

    // The API expects a single sender with name and email
    $from_name = km_detect_from_name(isset($from_name) ? $from_name : null);
    $from_email = km_detect_from_address(isset($from_email) ? $from_email : null);
    
    // The API expects the To field to be an array
    if(is_string($to)){
        $to = explode(',', $to);
    }

    // Now we have the minimum requirements for a valid API call
    $body = array(
        'from'       => "{$from_name} <{$from_email}>",
        'sender'     => "{$from_name} <{$from_email}>",
        'to'         => $to,
        'subject'    => $subject,
        'plain_body' => $plain_body,
        'headers'    => $body_headers
    );

    // Optional fields
    // The API allows for an HTML body
    if(!('text/plain' === $content_type) && isset($html_body)){
        $body['html_body'] = $html_body;
    }

    // The API allows for CC fields (must be an array)
    if (!empty($cc) && is_array($cc)) {
        $body['cc'] = $cc;
    }

    // The API allows for BCC fields (must be an array)
    if (!empty($bcc) && is_array($bcc)) {
        $body['bcc'] = $bcc;
    }
 
    // The API allows for Attachments fields (must be an array)
    if (!is_array($attachments)) {
        $attachments = explode("\n", str_replace("\r\n", "\n", $attachments));
    }

    // Add the API key and the content-type
    // TODO: handle files and other content-types
    $api_headers = array();
    $api_headers['X-Server-API-Key'] = $api_key;
    $api_headers['Content-Type'] = 'application/json';

    $endpoint = 'https://kingmailer.org/api/v1/send/message';
    $url = $endpoint;

    // Save everything as an object that wordpress can use for API calls
    $data = array(
        'headers'     => $api_headers,
        'body'        => json_encode($body),
        'method'      => 'POST',
        'data_format' => 'body',
    );







    // TODO: Kingmailer only supports 50 recipients per request, since we are
    // overriding this function, let's add looping here to handle that
    $response = wp_remote_post($url, $data);

    if (is_wp_error($response)) {
        // Store WP error in last error.
        km_api_last_error($response->get_error_message());


        return false;
    } 

    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = json_decode(wp_remote_retrieve_body($response));

    // TODO Kingmaile API will respond differently
    // Mailgun API should *always* return a `message` field, even when
    // $response_code != 200, so a lack of `message` indicates something
    // is broken.
    if ((int) $response_code != 200 && !isset($response_body->status)) {
        // Store response code and HTTP response message in last error.
        $response_message = wp_remote_retrieve_response_message($response);
        $errmsg = "$response_code - $response_message";
        km_api_last_error($errmsg);

        return false;
    }

    // Not sure there is any additional checking that needs to be done here, but why not?
    if ($response_body->status != 'success') {

        // show an error message
        if(isset($response_body->data)){
            $response_body_data = $response_body->data;
            if(isset($response_body_data->message)){
                km_api_last_error($response_body_data->message);
            }
        }

        return false;
    }

    return true;
}

function km_build_payload_from_body($body, $boundary) {
    $payload = '';

    // Iterate through pre-built params and build payload:
    foreach ($body as $key => $value) {
        if (is_array($value)) {
            $parent_key = $key;
            foreach ($value as $key => $value) {
                $payload .= '--'.$boundary;
                $payload .= "\r\n";
                $payload .= 'Content-Disposition: form-data; name="'.$parent_key."\"\r\n\r\n";
                $payload .= $value;
                $payload .= "\r\n";
            }
        } else {
            $payload .= '--'.$boundary;
            $payload .= "\r\n";
            $payload .= 'Content-Disposition: form-data; name="'.$key.'"'."\r\n\r\n";
            $payload .= $value;
            $payload .= "\r\n";
        }
    }

    return $payload;
}

function km_build_payload_from_mime($body, $boundary) {
}

function km_build_attachments_payload($attachments, $boundary) {
    $payload = '';

    // If we have attachments, add them to the payload.
    if (!empty($attachments)) {
        $i = 0;
        foreach ($attachments as $attachment) {
            if (!empty($attachment)) {
                $payload .= '--'.$boundary;
                $payload .= "\r\n";
                $payload .= 'Content-Disposition: form-data; name="attachment['.$i.']"; filename="'.basename($attachment).'"'."\r\n\r\n";
                $payload .= file_get_contents($attachment);
                $payload .= "\r\n";
                $i++;
            }
        }
    } else {
        return null;
    }

    return $payload;
}
