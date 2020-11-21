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

?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br/></div>
<!--	<span class="alignright">
				<a target="_blank" href="https://kingmailer.org/login">
					<img src="https://assets.mailgun.com/img/mailgun.svg" alt="Kingmailer" style="width:10em;"/>
				</a>
			</span>-->
	<h2><?php _e('Kingmailer', 'kingmailer'); ?></h2>

	<p>
		<?php
			$url = 'https://kingmailer.co/';
			$link = sprintf(
				wp_kses(
					__('A <a href="%1$s" target="%2$s">Kingmailer</a> account is required to use this plugin and the Kingmailer service.', 'kingmailer'),
					array('a' => array(
							'href' => array(),
							'target' => array()
						)
					)
				), esc_url($url), '_blank'
			);
			echo $link;
		?>
	</p>

	<h3><?php _e('Configuration', 'kingmailer'); ?></h3>
	<form id="kingmailer-form" action="options.php" method="post">
		<?php settings_fields('kingmailer'); ?>

		<table class="form-table">
		<tr valign="top">
				<th scope="row">
					<?php _e('Website Domain', 'kingmailer'); ?>
				</th>
				<td>
					<input type="text" class="regular-text"
						   name="kingmailer[domain]"
						   value="<?php esc_attr_e($this->get_option('domain')); ?>"
						   placeholder="example.com"
					/>
					<p class="description">
						<?php
							$link = sprintf(
								wp_kses(
									__('The domain you have added in your Kingmailer-account. Visit <a href="%1$s" target="%2$s">Kingmailer.co</a> to sign up for an account.', 'kingmailer'),
									array('a' => array(
											'href' => array(),
											'target' => array()
										)
									)
								), esc_url($url), '_blank'
							);
							echo $link;
						?>
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('From Name', 'kingmailer'); ?>
				</th>
				<td>
					<input type="text" class="regular-text"
						   name="kingmailer[from-name]"
						   value="<?php esc_attr_e($this->get_option('from-name')); ?>"
						   placeholder="Excited User"
					/>
					<p class="description">
						<?php
							_e('The "User Name" part of the sender information (<code>"Excited User &lt;info@example.com&gt;"</code>).', 'kingmailer');
						?>
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('From Address', 'kingmailer'); ?>
				</th>
				<td>
					<input type="text"
						   class="regular-text"
						   name="kingmailer[from-address]"
						   value="<?php esc_attr_e($this->get_option('from-address')); ?>"
						   placeholder="info@example.com"
					/>
					<p class="description">
						<?php
							_e('The &lt;info@example.com&gt; part of the sender information (<code>"Excited User &lt;info@example.com&gt;"</code>). This address will appear as the `From` address on sent mail. <strong>It is recommended that the @mydomain portion matches your Kingmailer sending domain.</strong>', 'kingmailer');
						?>
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e('Override "From" Details', 'kingmailer'); ?>
				</th>
				<td>
					<select name="kingmailer[override-from]">
						<option value="1"<?php selected('1', $this->get_option('override-from', null, '1')); ?>><?php _e('Yes', 'kingmailer'); ?></option>
						<option value="0"<?php selected('0', $this->get_option('override-from', null, '0')); ?>><?php _e('No', 'kingmailer'); ?></option>
					</select>
					<p class="description">
						<?php
							_e('If enabled, all emails will be sent with the above <code>From Name</code> and <code>From Address</code>, regardless of values set by other plugins. Useful for cases where other plugins don\'t play nice with our settings. Defaults to "Yes".', 'kingmailer');
						?>
					</p>
				</td>
			</tr>
			<tr valign="top" style="display: none;">
				<th scope="row">
					<?php _e('Use HTTP API', 'kingmailer'); ?>
				</th>
				<td>
					<select id="kingmailer-api" name="kingmailer[use_api]">
						<option value="1"<?php selected('1', $this->get_option('use_api')); ?>><?php _e('Yes', 'kingmailer'); ?></option>
						<option value="0"<?php selected('0', $this->get_option('use_api')); ?>><?php _e('No', 'kingmailer'); ?></option>
					</select>
					<p class="description">
						<?php
							_e('Set this to "No" if your server cannot make outbound HTTP connections or if emails are not being delivered. "No" will cause this plugin to use SMTP. Defaults to "Yes".', 'kingmailer');
						?>
					</p>
				</td>
			</tr>
			<tr valign="top" class="kingmailer-api">
				<th scope="row">
					<?php _e('API-host', 'kingmailer'); ?>
				</th>
				<td>
					<input type="text" class="regular-text"
						   name="kingmailer[api_host]"
						   value="<?php esc_attr_e($this->get_option('api_host')); ?>"
						   placeholder="kingmailer.org"
						   readonly
					/>
					<p class="description">
						<?php _e('Your Kingmailer API-host. Fixed at "kingmailer.org"', 'kingmailer'); ?>
						<br />
						<?php _e('Only valid for use with HTTP API.', 'kingmailer'); ?>
					</p>
				</td>
			</tr>
			<tr valign="top" class="kingmailer-api">
				<th scope="row">
					<?php _e('API Key', 'kingmailer'); ?>
				</th>
				<td>
					<input type="text" class="regular-text" name="kingmailer[api_key]"
						   value="<?php esc_attr_e($this->get_option('api_key')); ?>"
						   placeholder="ur7keAGRjYB3W5NfKQEm563Z"
					/>
					<p class="description">
						<?php _e('Your Kingmailer API key. Create an API by visiting credentials in your Kingmailer-account.', 'kingmailer'); ?>
						<br />
						<?php _e('Only valid for use with HTTP API.', 'kingmailer'); ?>
					</p>
				</td>
			</tr>
			<tr valign="top" class="kingmailer-smtp" style="display: none;">
				<th scope="row">
					<?php _e('SMTP-host', 'kingmailer'); ?>
				</th>
				<td>
					<input type="text" class="regular-text"
						   name="kingmailer[host]"
						   value="<?php esc_attr_e($this->get_option('host')); ?>"
						   placeholder="kingmailer.org"
						   readonly
					/>
					<p class="description">
						<?php _e('Your Kingmailer SMTP-host. Fixed at "kingmailer.org"', 'kingmailer'); ?>
						<br />
						<?php _e('Only valid for use with SMTP.', 'kingmailer'); ?>
					</p>
				</td>
			</tr>
			<tr valign="top" class="kingmailer-smtp" style="display: none;">
				<th scope="row">
					<?php _e('Username', 'kingmailer'); ?>
				</th>
				<td>
					<input type="text" class="regular-text"
						   name="kingmailer[username]"
						   value="<?php esc_attr_e($this->get_option('username')); ?>"
						   placeholder="postmaster"
					/>
					<p class="description">
						<?php _e('Your Kingmailer SMTP username.', 'kingmailer'); ?>
						<br />
						<?php _e('Only valid for use with SMTP.', 'kingmailer'); ?>
					</p>
				</td>
			</tr>
			<tr valign="top" class="kingmailer-smtp" style="display: none;">
				<th scope="row">
					<?php _e('Password', 'kingmailer'); ?>
				</th>
				<td>
					<input type="text" class="regular-text"
						   name="kingmailer[password]"
						   value="<?php esc_attr_e($this->get_option('password')); ?>"
						   placeholder="my-password"
					/>
					<p class="description">
						<?php _e('Your Kingmailer SMTP password that goes with the above username.', 'kingmailer'); ?>
						<br />
						<?php _e('Only valid for use with SMTP.', 'kingmailer'); ?>
					</p>
				</td>
			</tr>
			<tr valign="top" class="kingmailer-smtp" style="display: none;">
				<th scope="row">
					<?php _e('Use Secure SMTP', 'kingmailer'); ?>
				</th>
				<td>
					<select name="kingmailer[secure]">
						<option value="1"<?php selected('1', $this->get_option('secure')); ?>><?php _e('Yes', 'kingmailer'); ?></option>
						<option value="0"<?php selected('0', $this->get_option('secure')); ?>><?php _e('No', 'kingmailer'); ?></option>
					</select>
					<p class="description">
						<?php _e('Set this to "Yes" to send you mails over a secure SSL/TLS connection. If you set this to "No" your password will be sent in plain text. Default is "Yes".', 'kingmailer'); ?>
						<br />
						<?php _e('Only valid for use with SMTP.', 'kingmailer'); ?>
					</p>
				</td>
			</tr>
			<!-- <tr valign="top" class="kingmailer-smtp">
				<th scope="row">
					<?php // _e('Security Type', 'kingmailer'); ?>
				</th>
				<td>
					<select name="kingmailer[sectype]">
						<option value="ssl"<?php  // selected('ssl', $this->get_option('sectype')); ?>>SSL</option>
						<option value="tls"<?php // selected('tls', $this->get_option('sectype')); ?>>TLS</option>
					</select>
					<p class="description">
						<?php
						//	_e('Leave this at "TLS" unless mail sending fails. This option only matters for Secure SMTP. Default "TLS".', 'kingmailer');
						?>
					</p>
				</td>
			</tr> -->
		</table>
		<p>
			<?php
				_e('Please click <code>Save Changes</code> before attempting to test the configuration.', 'kingmailer');
			?>
		</p>
		<p class="submit">
			<input type="submit"
				   class="button-primary"
				   value="<?php _e('Save Changes', 'kingmailer'); ?>"
			/>
			<input type="button"
				   id="kingmailer-test"
				   class="button-secondary"
				   value="<?php _e('Send Test Mail', 'kingmailer'); ?>"
			/>
		</p>
	</form>
</div>
