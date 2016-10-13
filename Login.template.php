<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Android Latino
 * @copyright 2016 Android Latino
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0
 */

//Notas : faltan algunas funciones migrar a material Dessing

// This is just the basic "login" form.
function template_login()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt;

	echo '
		<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/sha1.js"></script>
		<div class="row">
			<div class="col-md-4 col-md-offset-4 col-sx-12">
				<div class="card">

					<div class="card-main">
						<div class="card-inner">
							<h3>
								',$txt['login'],'
							</h3>
						</div>
						<form action="', $scripturl, '?action=login2" name="frmLogin" id="frmLogin" method="post" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>

					';

	// Did they make a mistake last time?
	if (!empty($context['login_errors']))
		foreach ($context['login_errors'] as $error)
			echo '
				<p class="mdc-text-red-900">', $error, '</p>';

	// Or perhaps there's some special description for this time?
	if (isset($context['description']))
		echo '
				<p class="description">', $context['description'], '</p>';

	// Now just get the basic information - username, password, etc.
	echo '
							<div class="card-inner">

									<div class="form-group form-group-label">
										<div class="row">
											<div class="col-md-12 ">
												<label class="floating-label" for="',$txt['username'],'">',$txt['username'],'</label>
												<input type="text" name="user" value="', $context['default_username'], '" class="form-control" />
											</div>
										</div>
									</div>

									<div class="form-group form-group-label">
										<div class="row">
											<div class="col-md-12 ">
												<label class="floating-label" for="',$txt['password'],'">',$txt['password'],'</label>
												<input type="password" name="passwrd" value="', $context['default_password'], '" class="form-control" placeholder="',$txt['password'],'"/>
											</div>
										</div>
									</div>

					';

	if (!empty($modSettings['enableOpenID']))
		echo '
						<div class="content-sub-heading"><p class="text-center">&mdash;', $txt['or'], '&mdash;<p></div>

						<div class="form-group">
							<input type="text" name="openid_identifier" class="form-control openid_login" placeholder="',$txt['openid'],' no Obligatorio" />
							<a href="', $scripturl, '?action=helpadmin;help=register_openid" onclick="return reqWin(this.href);" class="help">(?)</a>
						</div>
				';

	echo '
						<div class="form-group form-group-label">
							<label class="floating-label" for="', $txt['mins_logged_in'], '"> ', $txt['mins_logged_in'], ': </label>
							<input type="text" name="cookielength" size="4" maxlength="4" value="', $modSettings['cookieTime'], '"', $context['never_expire'] ? ' disabled="disabled"' : '', ' class="form-control" />
						</div>
						<div class="form-group">
							<div class="checkbox checkbox-adv">
								<label for="', $txt['always_logged_in'], '">

									<input class="access-hide" id="', $txt['always_logged_in'], '" name="cookieneverexp" ', $context['never_expire'] ? ' checked="checked"' : '', ' type="checkbox" onclick="this.form.cookielength.disabled = this.checked;" >

									', $txt['always_logged_in'], '

									<span class="checkbox-circle"></span>
									<span class="checkbox-circle-check"></span>
									<span class="checkbox-circle-icon icon">done</span>
								</label>
							</div>
						</div>
					</div>
				<div class="card-action">
			';
	// If they have deleted their account, give them a chance to change their mind.
	if (isset($context['login_show_undelete']))
		echo '
						<label for="', $txt['undelete_account'], '">

							<input class="access-hide" id="', $txt['undelete_account'], '" name="undelete" type="checkbox" >
							', $txt['undelete_account'], '
							<span class="checkbox-circle"></span>
							<span class="checkbox-circle-check"></span>
							<span class="checkbox-circle-icon icon">done</span>
						</label>
						';
		echo '
						<a href="#" onclick="document.getElementById(\'frmLogin\').submit()" class="btn mdc-text-blue-800 btn-flat waves-attach waves-button waves-effect">', $txt['login'], '</a>

						<a href="', $scripturl, '?action=reminder" class="btn mdc-text-blue-800 btn-flat waves-attach waves-button waves-effect">', $txt['forgot_your_password'], '</a>
						<input type="hidden" name="hash_passwrd" value="" />

					</div>
				</form>
			</div>
		</div>
	</div>
</div>
		';

	// Focus on the correct input - username or password.
	echo '
		<script type="text/javascript"><!-- // --><![CDATA[
			document.forms.frmLogin.', isset($context['default_username']) && $context['default_username'] != '' ? 'passwrd' : 'user', '.focus();
		// ]]></script>';
}

// Tell a guest to get lost or login!
function template_kick_guest()
{
	global $context, $settings, $options, $scripturl, $modSettings, $txt;

	// This isn't that much... just like normal login but with a message at the top.
	echo '
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/sha1.js"></script>
	<form action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" name="frmLogin" id="frmLogin"', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
		<div class="tborder login">
			<div class="cat_bar">
				<h3 class="catbg">', $txt['warning'], '</h3>
			</div>';

	// Show the message or default message.
	echo '
			<p class="information centertext">
				', empty($context['kick_message']) ? $txt['only_members_can_access'] : $context['kick_message'], '<br />
				', $txt['login_below'], ' <a href="', $scripturl, '?action=register">', $txt['register_an_account'], '</a> ', sprintf($txt['login_with_forum'], $context['forum_name_html_safe']), '
			</p>';

	// And now the login information.
	echo '
			<div class="cat_bar">
				<h3 class="catbg">
					<img src="', $settings['images_url'], '/icons/login_sm.gif" alt="" class="icon" /> ', $txt['login'], '
				</h3>
			</div>
			<span class="upperframe"><span></span></span>
			<div class="roundframe">
				<dl>
					<dt>', $txt['username'], ':</dt>
					<dd><input type="text" name="user" size="20" class="input_text" /></dd>
					<dt>', $txt['password'], ':</dt>
					<dd><input type="password" name="passwrd" size="20" class="input_password" /></dd>';

	if (!empty($modSettings['enableOpenID']))
		echo '
				</dl>
				<p><strong>&mdash;', $txt['or'], '&mdash;</strong></p>
				<dl>
					<dt>', $txt['openid'], ':</dt>
					<dd><input type="text" name="openid_identifier" class="input_text openid_login" size="17" /></dd>
				</dl>
				<hr />
				<dl>';

	echo '
					<dt>', $txt['mins_logged_in'], ':</dt>
					<dd><input type="text" name="cookielength" size="4" maxlength="4" value="', $modSettings['cookieTime'], '" class="input_text" /></dd>
					<dt>', $txt['always_logged_in'], ':</dt>
					<dd><input type="checkbox" name="cookieneverexp" class="input_check" onclick="this.form.cookielength.disabled = this.checked;" /></dd>
				</dl>
				<p class="centertext"><input type="submit" value="', $txt['login'], '" class="button_submit" /></p>
				<p class="centertext smalltext"><a href="', $scripturl, '?action=reminder">', $txt['forgot_your_password'], '</a></p>
			</div>
			<span class="lowerframe"><span></span></span>
			<input type="hidden" name="hash_passwrd" value="" />
		</div>
	</form>';

	// Do the focus thing...
	echo '
		<script type="text/javascript"><!-- // --><![CDATA[
			document.forms.frmLogin.user.focus();
		// ]]></script>';
}

// This is for maintenance mode.
function template_maintenance()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Display the administrator's message at the top.
	echo '
<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/sha1.js"></script>

<div class="row">
	<div class="col-md-4 col-md-offset-4 col-sx-12">
		<div class="card">
			<div class="card-main">
				
				<div class="card-header card-red">
					<div class="card-inner">
							<h3 class="card-heading">', $context['title'], '</h3>
							 <p><i class="icon">build</i> ', $context['description'], '</p>
					</div>
				</div>

				<form id="frmLogin" action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '"', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
					<div class="card-inner">
						<h3 class="card-heading">', $txt['admin_login'], '</h3>
					</div>

					<div class="card-inner">
						<div class="form-group form-group-label">
							<div class="row">
								<div class="col-md-12 ">
									<label class="floating-label" for="',$txt['username'],'">',$txt['username'],'</label>
									<input type="text" name="user" value="', $context['default_username'], '" class="form-control" />
								</div>
							</div>
							
							<div class="form-group form-group-label">
								<div class="row">
									<div class="col-md-12 ">
										<label class="floating-label" for="',$txt['password'],'">',$txt['password'],'</label>
										<input type="password" name="passwrd" value="', $context['default_password'], '" class="form-control" placeholder="',$txt['password'],'"/>
									</div>
								</div>
							</div>
							<div class="form-group form-group-label">
								<label class="floating-label" for="', $txt['mins_logged_in'], '"> ', $txt['mins_logged_in'], ': </label>
								<input type="text" name="cookielength" size="4" maxlength="4" value="', $modSettings['cookieTime'], '"', $context['never_expire'] ? ' disabled="disabled"' : '', ' class="form-control" />
							</div>
							<div class="form-group">
								<div class="checkbox checkbox-adv">
									<label for="', $txt['always_logged_in'], '">
										<input class="access-hide" id="', $txt['always_logged_in'], '" name="cookieneverexp" ', $context['never_expire'] ? ' checked="checked"' : '', ' type="checkbox" onclick="this.form.cookielength.disabled = this.checked;" >
										', $txt['always_logged_in'], '
										<span class="checkbox-circle"></span>
										<span class="checkbox-circle-check"></span>
										<span class="checkbox-circle-icon icon">done</span>
									</label>
								</div>
							</div>

							<div class="card-action">
								<a href="#" onclick="document.getElementById(\'frmLogin\').submit()" class="btn mdc-text-blue-800 btn-flat waves-attach waves-button waves-effect">', $txt['login'], '</a>
							</div>
						</div>
					</div>
					<input type="hidden" name="hash_passwrd" value="" />
				</form>
			</div>
		</div>
	</div>
</div>
';
}

// This is for the security stuff - makes administrators login every so often.
function template_admin_login()
{
	global $context, $settings, $options, $scripturl, $txt;

	// Since this should redirect to whatever they were doing, send all the get data.
	echo '
<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/sha1.js"></script>

<div class="row">
	<div class="col-md-4 col-md-offset-4 col-sx-12">
		<div class="card">
			<form class="form-inline" action="', $scripturl, $context['get_data'], '" method="post" accept-charset="', $context['character_set'], '" name="frmLogin" id="frmLogin" onsubmit="hashAdminPassword(this, \'', $context['user']['username'], '\', \'', $context['session_id'], '\');">
				<div class="card-main">
					<div class="card-header">
						<div class="card-inner">
							<span class="card-heading"><span class="icon">supervisor_account</span> ', $txt['admin_login'], ' <a href="', $scripturl, '?action=helpadmin;help=securityDisable_why" onclick="return reqWin(this.href);" class="help"><span class="icon">help</a></span>
						</div>
					</div>
					<div class="card-inner">';
						if (!empty($context['incorrect_password']))
							echo '
								<div class="mdc-text-red-900">', $txt['admin_incorrect_password'], '</div>';
						echo '
						<div class="row">
							<div class="form-group form-group-label">
								<div class="col-md-12 ">
									<label class="floating-label" for="',$txt['password'],'">',$txt['password'],'  </label>

									<input type="password" name="admin_pass" value="" class="form-control" />
									
								</div>
							</div>
						</div>
					</div>
					<div class="card-action">
						<a href="#" onclick="document.getElementById(\'frmLogin\').submit()" class="btn mdc-text-blue-800 btn-flat waves-attach waves-button waves-effect">', $txt['login'], '</a>
						<button type="submit" class="btn btn-default" value="', $txt['login'], '" >Submit</button>';

	// Make sure to output all the old post data.
	echo $context['post_data'],' 		
					</div>
				</div>
			<input type="hidden" name="admin_hash_pass" value="" />
			</form>
		</div>
	</div>
</div>
';

// Focus on the password box.
echo '
<script type="text/javascript"><!-- // --><![CDATA[
	document.forms.frmLogin.admin_pass.focus();
// ]]></script>';
}

// Activate your account manually?
function template_resend()
{
	global $context, $settings, $options, $txt, $scripturl;

	// Just ask them for their code so they can try it again...
	echo '
		<form action="', $scripturl, '?action=activate;sa=resend" method="post" accept-charset="', $context['character_set'], '">
			<div class="title_bar">
				<h3 class="titlebg">', $context['page_title'], '</h3>
			</div>
			<span class="upperframe"><span></span></span>
			<div class="roundframe">
				<dl>
					<dt>', $txt['invalid_activation_username'], ':</dt>
					<dd><input type="text" name="user" size="40" value="', $context['default_username'], '" class="input_text" /></dd>
				</dl>
				<p>', $txt['invalid_activation_new'], '</p>
				<dl>
					<dt>', $txt['invalid_activation_new_email'], ':</dt>
					<dd><input type="text" name="new_email" size="40" class="input_text" /></dd>
					<dt>', $txt['invalid_activation_password'], ':</dt>
					<dd><input type="password" name="passwd" size="30" class="input_password" /></dd>
				</dl>';

	if ($context['can_activate'])
		echo '
				<p>', $txt['invalid_activation_known'], '</p>
				<dl>
					<dt>', $txt['invalid_activation_retry'], ':</dt>
					<dd><input type="text" name="code" size="30" class="input_text" /></dd>
				</dl>';

	echo '
				<p><input type="submit" value="', $txt['invalid_activation_resend'], '" class="button_submit" /></p>
			</div>
			<span class="lowerframe"><span></span></span>
		</form>';
}

?>
