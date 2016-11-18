<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines
 * @copyright 2011 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0
 */

/*	This template is, perhaps, the most important template in the theme. It
	contains the main template layer that displays the header and footer of
	the forum, namely with main_above and main_below. It also contains the
	menu sub template, which appropriately displays the menu; the init sub
	template, which is there to set the theme up; (init can be missing.) and
	the linktree sub template, which sorts out the link tree.

	The init sub template should load any data and set any hardcoded options.

	The main_above sub template is what is shown above the main content, and
	should contain anything that should be shown up there.

	The main_below sub template, conversely, is shown after the main content.
	It should probably contain the copyright statement and some other things.

	The linktree sub template should display the link tree, using the data
	in the $context['linktree'] variable.

	The menu sub template should display all the relevant buttons the user
	wants and or needs.

	For more information on the templating system, please see the site at:
	http://www.simplemachines.org/
*/

// Initialize the template... mainly little settings.
function template_init()
{
	global $context, $settings, $options, $txt;

	/* Use images from default theme when using templates from the default theme?
		if this is 'always', images from the default theme will be used.
		if this is 'defaults', images from the default theme will only be used with default templates.
		if this is 'never' or isn't set at all, images from the default theme will not be used. */
	$settings['use_default_images'] = 'never';

	/* What document type definition is being used? (for font size and other issues.)
		'xhtml' for an XHTML 1.0 document type definition.
		'html' for an HTML 4.01 document type definition. */
	$settings['doctype'] = 'xhtml';

	/* The version this template/theme is for.
		This should probably be the version of SMF it was created for. */
	$settings['theme_version'] = '2.0';

	/* Set a setting that tells the theme that it can render the tabs. */
	$settings['use_tabs'] = true;

	/* Use plain buttons - as opposed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status separate from topic icons? */
	$settings['separate_sticky_lock'] = true;

	/* Does this theme use the strict doctype? */
	$settings['strict_doctype'] = false;

	/* Does this theme use post previews on the message index? */
	$settings['message_index_preview'] = false;

	/* Set the following variable to true if this theme requires the optional theme strings file to be loaded. */
	$settings['require_theme_strings'] = true;
}

// The main sub template above the content.
function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html>
<html lang="es-PE"', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>';

	// The ?fin20 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/external/daemonitemd/css/base.min.css?fin20" />
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/external/daemonitemd/css/project.min.css?fin20" />
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/external/mdcp/material-design-color-palette.min.css?fin20" />
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/theme.css?fin20" />';

	// Some browsers need an extra stylesheet due to bugs/compatibility issues.
	foreach (array('ie7', 'ie6', 'webkit') as $cssfix)
		if ($context['browser']['is_' . $cssfix])
			echo '
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/', $cssfix, '.css" />';

	// RTL languages require an additional stylesheet.
	if ($context['right_to_left'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/rtl.css" />';

	//Miniupdate: Chrome acepta el atributo theme-color, asi que vamos a colorear la navbar
	echo '<meta name="theme-color" content="#388ccc">';

	// Here comes the JavaScript bits!
	echo '
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/script.js?fin20"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/theme.js?fin20"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/external/jquery/dist/jquery.min.js?fin20"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/external/daemonitemd/js/base.min.js?fin20"></script>
	
		<script type="text/javascript"><!-- // --><![CDATA[
		var smf_theme_url = "', $settings['theme_url'], '";
		var smf_default_theme_url = "', $settings['default_theme_url'], '";
		var smf_images_url = "', $settings['images_url'], '";
		var smf_scripturl = "', $scripturl, '";
		var smf_iso_case_folding = ', $context['server']['iso_case_folding'] ? 'true' : 'false', ';
		var smf_charset = "', $context['character_set'], '";', $context['show_pm_popup'] ? '
		var fPmPopup = function ()
		{
			if (confirm("' . $txt['show_personal_messages'] . '"))
				window.open(smf_prepareScriptUrl(smf_scripturl) + "action=pm");
		}
		addLoadEvent(fPmPopup);' : '', '
		var ajax_notification_text = "', $txt['ajax_in_progress'], '";
		var ajax_notification_cancel_text = "', $txt['modify_cancel'], '";
	// ]]></script>';

	echo '
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title_html_safe'], '" />', !empty($context['meta_keywords']) ? '
	<meta name="keywords" content="' . $context['meta_keywords'] . '" />' : '', '
	<title>', $context['page_title_html_safe'], '</title>';

	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex" />';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '" />';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help" />
	<link rel="search" href="', $scripturl, '?action=search" />
	<link rel="contents" href="', $scripturl, '" />';

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?type=rss;action=.xml" />';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (!empty($context['current_topic']))
		echo '
	<link rel="prev" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=prev" />
	<link rel="next" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=next" />';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0" />';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	echo '
</head>
<body class="page-brand">';
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;
	/*Empezando a escribir lo del body, thx smf*/

	/*Navbar responsive, estilizada al foro*/
	echo '
			<header class="header header-transparent header-waterfall">

				<table>
					<tr>
						<td class="tresrayas">
							<ul class="nav nav-list pull-left">
								<li>
									<a data-toggle="menu" href="#al_menu">
										<span class="icon icon-lg">menu</span>
									</a>
								</li>
							</ul>
						</td>
						<td class="titulo-overflow contenido">

							<a class="header-affix-hide header-logo margin-left-no margin-right-no" data-offset-top="50" data-spy="affix" href="', $scripturl, '">',$context['forum_name'],'</a>

							<h1><span class="header-affix header-logo margin-left-no margin-right-no" data-offset-top="50" data-spy="affix">';
	//Trabajando en el fix del titulo, ya que esto afecta solo al index.template.php
	//Sacar el forumname
	$valnombre=strlen($context['forum_name']);
	$valortitulo=substr($context['page_title_html_safe'],0,$valnombre);
	//Aca se pregunta si tiene un valor igual al nombre de foro, asi se sabe que hacer. En caso de que si, se hace una cuenta para mostrar solo index
	if ($valortitulo== $context['forum_name'])
	{
		$rmtitulo=strlen($context['forum_name'])+ 3;
		$cttitulo=strlen($context['page_title_html_safe']);
		echo substr($context['page_title_html_safe'], $rmtitulo, $cttitulo);}
	//Sino se muestra la el titulo de la pagina lo más normal posible
	else
	{
		echo $context['page_title_html_safe'];}
	echo '</span></h1>
						</td>




						<td class="tbusqueda ocultar-pequeno">
							<div class="busqueda  ">
								<form action="https://www.google.com" id="cse-search-box">
									<div>
										<input type="hidden" name="cx" value="partner-pub-5234228783629303:3299510057" />
										<input type="hidden" name="ie" value="UTF-8" />
										<input style="background-color:transparent; " type="text" name="q" class="form-control" />
										<input class="oculto" type="submit" name="sa" value="Buscar" />
									</div>
								</form>

								<script type="text/javascript" src="https://www.google.com/coop/cse/brand?form=cse-search-box&amp;lang=es"></script>

							</div>
						</td>
						<td class="menu-usuario">
							<ul class="nav nav-list">
								<li class="dropdown margin-right pull-right">
									<a class="dropdown-toggle padding-left-no padding-right-no" data-toggle="dropdown">
										<span class="access-hide">', $context['user']['name'],'</span>
										<span class="avatar avatar-sm"><img alt="avatar de ', $context['user']['name'],'" src="', !empty($context['user']['avatar']['href']) ? $context['user']['avatar']['href'] : $settings['images_url']. '/noavatar.png' ,'" alt="', $context['user']['name'],'" /></span>
									</a>
									<ul class="dropdown-menu">';
	if ($context['user']['is_logged'])
	{
		echo
		'
												<li>
													<a class="padding-right-lg waves-attach" href="', $scripturl, '?action=profile;area=forumprofile;"><span class="icon icon-lg margin-right">edit</span>' , $txt['edit_profile'] , '</a>
												</li>
												<li>
													<a class="padding-right-lg waves-attach" href="' , $scripturl , '?action=profile;area=account;"><span class="icon icon-lg margin-right">account_box</span>' , $txt['profile_account'] , '</a>
												</li>
												<li>
													<a class="padding-right-lg waves-attach" href="' , $scripturl , '?action=logout;sesc=', $context['session_id'], '"><span class="icon icon-lg margin-right">exit_to_app</span>' , $txt['logout'] , '</a>
												</li>';
	}
	else
	{
		echo '
												<li>
													<a class="padding-right-lg waves-attach" href="' , $scripturl , '?action=login"><span class="icon icon-lg margin-right">person</span>' , $txt['login'] , '</a>
												</li>
												<li>
													<a class="padding-right-lg waves-attach" href="' , $scripturl , '?action=register"><span class="icon icon-lg margin-right">assignment_ind</span>' , $txt['register'] , '</a>
												</li>';
	}
	echo '
									</ul>
								</li>
							</ul>
						</td>
					</tr>
				</table>
			</header>

	';
//Añadiendo el panel de usuario de Materialize adaptado a este FW.
	echo '
	<nav aria-hidden="true" class="menu" id="al_menu" tabindex="-1">
		<div class="menu-scroll mdc-bg-blue-grey-700">
			<div class="menu-top">
				<div class="menu-top-img">
					<img src="', !empty($context['user']['avatar']['href']) ? $context['user']['avatar']['href'] : $settings['images_url']. '/material/defaultbg.png' ,'" alt="User Background ', $context['user']['name'],'" >
				</div>
				<div class="menu-top-info">
					<a class="menu-top-user" href="', $scripturl, '?action=profile"><span class="avatar pull-left"><img src="', !empty($context['user']['avatar']['href']) ? $context['user']['avatar']['href'] : $settings['images_url']. '/noavatar.png' ,'" alt="', $context['user']['name'],'"></span>';
	if ($context['user']['is_logged'])
	{echo $context['user']['name'];}
	else
	{echo $txt['guest_title'];}
	echo'
					</a>
				</div>
				<div class="menu-top-info-sub">
					<small>    Menú de Usuario    </small>
				</div>
			</div>
			<div class="menu-content">
				<ul class="nav">
				<li>
					<a class="waves-attach" href="', $scripturl, '"><span class="icon icon-lg margin-right">home</span>',$txt['home'],'</a>
				</li>';
	//Editando el listado para mostrar los menus mas importantes del mod
	if(!empty($context['user']['is_logged']))
	{
		echo '
					<li>
						<a class="waves-attach" href="', $scripturl, '?action=profile"><span class="icon icon-lg margin-right">account_circle</span>',$txt['forumprofile_short'],'</a>
					</li>
					<li>
						<a class="waves-attach" href="', $scripturl, '?action=pm"><span class="icon icon-lg margin-right">inbox</span>',$txt['pm'],'</a>
					</li>
					<li class="hidden-md">
						<a class="waves-attach" href="javascript:void(0)" data-target="#doc_menu_profile_settings" data-toggle="collapse"><span class="icon icon-lg margin-right">menu</span>',$txt['almenu'],'</a>
						<span class="collapsed menu-collapse-toggle waves-attach" data-target="#doc_menu_profile_settings" data-toggle="collapse"><i class="icon menu-collapse-toggle-close">close</i><i class="icon menu-collapse-toggle-default">add</i></span>
						<ul class="collapse menu-collapse" id="doc_menu_profile_settings">';
		menu_lateral();
		echo '</ul>
					</li>';
		// Muestra los Likes del Like Mod
		if (!empty($modSettings['LikePosts::$LikePostsUtils->showLikeNotification())']))
		{
			echo '
					<li>
						<a class="waves-attach" href="', $scripturl, '?action=likepostsstats"><span class="icon icon-lg margin-right">thumb_up</span>', $txt['like_show_notifications'], '</a>
					</li>';
		}
	}
	echo '<li>
						<a class="waves-attach" href="', $scripturl, '?action=help"><span class="icon icon-lg margin-right">help</span>',$txt['help'],'</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>';
//Añadiendo un content, en el cual despues vemos si agregar algo mas
	echo'
	<div class="content">
		<div class="content-heading">
		</div>
		<div class="container">
			<div class="row">
				<div class="visible-md-block visible-lg-block">
					<div class="card margin-bottom-no">
				    <div class="card-main">
			        <div class="card-inner mdc-text-grey-900"  style="margin-bottom: 0px;">
								<div class="container">
									<div class="row">
										<div class="col-md-6">
										';
	if (!$context['user']['is_logged'])
	{
		// Otherwise they're a guest - this time ask them to either register or login - lazy bums...
		echo'
											<form class="form-inline" id="login-form" action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
												<div class="container-fluid">
													<div class="col-md-6">
														<div class="form-group sinmargen">
															<div class="form-group-label sinmargen">
																<label class="floating-label" for="',$txt['username'],'">',$txt['username'],'</label>
																<input type="text" name="user" size="10" class="form-control" placeholder="',$txt['username'],'"/>
															</div>
															<div class="form-group-label sinmargen" style="margin-top: 10px;">
																<label class="floating-label" for="',$txt['password'],'">',$txt['password'],'</label>														
																<input type="password" name="passwrd" size="10" class="form-control" placeholder="',$txt['password'],'"/>
															</div>
														</div>
																			';
		if (!empty($modSettings['enableOpenID']))
			echo '
														<input placeholder="',$txt['openid'],' no Obligatorio" type="text" name="openid_identifier" id="openid_url" size="25" class="form-control openid_login" />';

		echo '
														<input type="hidden" name="hash_passwrd" value="" />
													</div>
													<div class="col-md-6">
														<div class="form-group sinmargen">
															<select name="cookielength" class="form-control">
																<option value="60">', $txt['one_hour'], '</option>
																<option value="1440">', $txt['one_day'], '</option>
																<option value="10080">', $txt['one_week'], '</option>
																<option value="43200">', $txt['one_month'], '</option>
																<option value="-1" selected="selected">', $txt['forever'], '</option>
															</select>
														</div>
														<div>
															<a href="#" onclick="document.getElementById(\'login-form\').submit()" class="btn mdc-text-blue-800 btn-flat waves-attach waves-button waves-effect">', $txt['login'], '</a>
															<a class="btn mdc-text-blue-800 btn-flat waves-attach waves-button" href="' , $scripturl , '?action=register">' , $txt['register'] , '
															</a>
															<span class="oculto">
																<input type="submit" value="', $txt['login'], '" class="btn btn-default" />
															</span>
														</div>
													</div>
												</div>
											</form>
														';
	}
	// If the user is logged in, display stuff like their name, new messages, etc.
	else {
		echo '
											<div class="bienvenido">
												',$txt['welmsg_welcome'].' '.$context['user']['name'].'
											</div>
														';
		echo '
											<div>
												<a class="btn mdc-text-blue-800 btn-flat waves-attach waves-button" href="', $scripturl, '?action=unread"> ', $txt['view_unread_category'] , '
												</a>
								        <a class="btn mdc-text-blue-800 btn-flat waves-attach waves-button" href="', $scripturl, '?action=unreadreplies"> ', $txt['replies'] , '</a>
											</div>';
		// Is the forum in maintenance mode?
		if ($context['in_maintenance'] && $context['user']['is_admin'])
		{
			echo '<span class="notice">', $txt['maintain_mode_on'], '</span>';
		}
		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
		{
			echo '<span>', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '</span>';
		}

		if (!empty($context['open_mod_reports']) && $context['show_open_reports'])
		{
			echo '<span><a href="', $scripturl, '?action=moderate;area=reports">', sprintf($txt['mod_reports_waiting'], $context['open_mod_reports']), '</a></span>';
		}
	}


	echo'
										</div>
										<div class="col-md-6">
										';
	//aqui para noticias en dispositivos grandes
	echo'
											<div class="text-center">',$txt['date'],': ', $context['current_time'], '.</div>
											';

	if (!empty($settings['enable_news']))
	{echo show_news("desktop");}

	echo '
										</div>
									</div>
								</div>

								<div class="row">
									<div class="container ">
										<div class="visible-md-block visible-lg-block">
												', template_menu(),'
										</div>
									</div>
								</div>
							</div>';
	//aqui debe de ir el div card action
	echo'
						</div>
					</div>
				</div>
			</div>
		</div>


	';//dejo sin cerrar el <div class="content"> para cerrar en el final del documento

// Agregar la seccion de noticias desplazables para tablets y celulares.
	if (!empty($settings['enable_news']))
	{	echo show_news("movil")	;}

	// Define the upper_section toggle in JavaScript.

	echo '
		<div class="clearfix"></div>';

	// The main content should go here.
	echo'
	<main class="contenedor">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
	';
	// Custom banners and shoutboxes should be placed here, before the linktree.

	// Show the navigation tree.
	theme_linktree();
	echo'

	';
}

function show_news($cols="desktop"){
	global $txt, $context;

	$titlewarp='
	'.
		($cols=="movil" ? '
	<div class="hidden-lg hidden-md">
		<div class="container">
			<div class="row">
				<div class="col-md-offset-4 col-md-4">':'	<div class="visible-md-block visible-lg-block">' )
		.'
					<div class="tile-wrap">
						<div class="tile tile-collapse">
							<div data-target="'. ($cols=="movil" ? '#doc_tile_example_2':'#doc_tile_example_1').'" data-toggle="tile">
								<div class="pull-left tile-side" data-ignore="tile">
									<div class="avatar avatar-sm mdc-bg-blue-500">
										<span class="icon icofix">public</span>
									</div>
								</div>
								<div class="tile-inner">
									<div class="text-overflow"><strong>'. $txt['news']. '</strong></div>
								</div>
							</div>
							<div class="tile-active-show collapse" id="'.($cols=="movil" ? : 'doc_tile_example_1').'">
								<div class="tile-sub">
									<p>'. $context['random_news_line']. '</p>
								</div>
							</div>
						</div>
					</div>'.
		($cols=="movil" ? '
					</div>
				</div>
			</div>
		</div>':'</div>')

		.'
	';
	echo $titlewarp;
}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
                </div>
			</div>
		</div>
	</main>';

	// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
	echo '
	<footer>
		<div class="container-fluid">
			<div id="row">
				<div class="col-xs-12 text-center">
					<ul class="nav-justified ulsinpuntos">
						
							<li class="padding-right-lg waves-attach copyright">', theme_copyright(), '</li>
							<li class="padding-right-lg waves-attach"><a id="button_xhtml" href="http://validator.w3.org/check?uri=referer" target="_blank" class="new_win" title="', $txt['valid_xhtml'], '"><span>', $txt['xhtml'], '</span></a></li>
							', !empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']) ? '<li class="padding-right-lg waves-attach "><a id="button_rss" href="' . $scripturl . '?action=.xml;type=rss" class="new_win"><span>' . $txt['rss'] . '</span></a></li>' : '', '
							<li class="padding-right-lg waves-attach last"><a id="button_wap2" href="', $scripturl , '?wap2" class="new_win"><span>', $txt['wap2'], '</span></a></li>
						
					</ul>';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
					<p>', $txt['page_created'], $context['load_time'], $txt['seconds_with'], $context['load_queries'], $txt['queries'], '</p>';

	echo '
				</div>
			</div>
		</div>
	</footer>', !empty($settings['forum_width']) ? '
</div>' : '';
}

function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
</body>
</html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree($force_show = false)
{
	global $context, $settings, $options, $shown_linktree;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;

	echo '
	<div class="sinmargen">
		<ul class="breadcrumb">';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
			<li>';

		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo $settings['linktree_link'] && isset($tree['url']) ? '
				<a href="' . $tree['url'] . '">' . $tree['name'] . '</a>' : $tree['name'] ;

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		echo '
			</li>';
	}
	echo '
		</ul>
	</div>';

	$shown_linktree = true;
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
		<nav class="tab-nav tab-nav-brand card-menu">
			<ul class="nav nav-justified">';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
				<li class="', $button['active_button'] ? 'active ' : '',' ', empty($button['sub_buttons']) ? '' : 'dropdown' ,'">
					<a ', !empty($button['sub_buttons']) ? 'href="#" class="dropdown-toggle waves-attach" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"' : 'href="'. $button['href']. '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : ' class="waves-attach" ',' >', $button['title'], '
					</a>';
		if (!empty($button['sub_buttons']))
		{
			//primero el boton de  dorpdown toggle que no apunta a nada mas que al dropdow, asi que lo colocamos como primera opcion en la lista
			echo '
					<ul class="dropdown-menu">
						<li><a href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] .'"' :'',' >', $button['title'] ,'</a></li>	';
			foreach ($button['sub_buttons'] as $childbutton)
			{
				echo '
						<li>
							<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', '>
								<span', isset($childbutton['is_last']) ? ' class="last"' : '', '>', $childbutton['title'], !empty($childbutton['sub_buttons']) ? '...' : '', '</span>
							</a>';
				// 3rd level menus :)
				if (!empty($childbutton['sub_buttons']))
				{
					echo '
							<ul>';

					foreach ($childbutton['sub_buttons'] as $grandchildbutton)
						echo '
								<li>
									<a href="', $grandchildbutton['href'], '"', isset($grandchildbutton['target']) ? ' target="' . $grandchildbutton['target'] . '"' : '', '>
										<span', isset($grandchildbutton['is_last']) ? ' class="last"' : '', '>', $grandchildbutton['title'], '</span>
									</a>
								</li>';

					echo '
							</ul>';
				}

				echo '
						</li>';
			}
			echo '
					</ul>';
		}
		echo '
				</li>';
	}

	echo '
			</ul>
		</nav>';
}

//Esta function es para el menú del costado, para celulares
function menu_lateral()
{
	global $context, $settings, $options, $scripturl, $txt;
	//Ahora nos encargamos de usar los elementos de la lista
	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
				<li>
					<a href="', $button['href'], '" class="waves-attach waves-effect">', $button['title'], '
					</a>';
		echo '
				</li>';
	}
}

// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $strip_options = array())
{
	global $settings, $context, $txt, $scripturl;

	if (!is_array($strip_options))
		$strip_options = array();

	// List the buttons in reverse order for RTL languages.
	if ($context['right_to_left'])
		$button_strip = array_reverse($button_strip, true);

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
			$buttons[] = '
				<li class="padding-right-lg waves-attach"><a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="btn btn-flat btn-brand  button_strip_' . $key . (isset($value['active']) ? ' active' : '') . ' " href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a></li>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

	echo '
		<div class="sinmargen buttonlist', !empty($direction) ? ' float' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul class="nav nav-list ulsinpuntos sinmargen">',
	implode('', $buttons), '
			</ul>
		</div>
		';
}
function template_fbtn_strip($button_strip){

		$buttons =

	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
			$buttons[] = '
				<a' . (isset($value['id']) ? ' id="fbutton_strip_' . $value['id'] . '"' : '') . ' class="fbtn waves-attach waves-circle  fbutton_strip_' . $key . (isset($value['active']) ? ' active' : '') . ' " href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span class="material-icons">' . $txt[$value['text']] . '</span></a>
				';
	}

		if (empty($buttons))
		return;


		echo '
	<div class="fbtn-container">
		<div class="fbtn-inner">
			<a class="fbtn fbtn-lg fbtn-brand-accent waves-attach waves-circle waves-light" data-toggle="dropdown"><span class="fbtn-text fbtn-text-left">Menú Principal</span><span class="fbtn-ori icon">apps</span><span class="fbtn-sub icon">close</span></a>
			<div class="fbtn-dropup">
				<a class="fbtn waves-attach waves-circle" href="#" id="ir_arriba"><span class="fbtn-text fbtn-text-left">Ir arriba</span><span class="material-icons">expand_less</span></a>',
		implode('', $buttons),'
			</div>
		</div>
	</div>';
}


?>
