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

function template_main()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	// Show some statistics if stat info is off.
	if (!$settings['show_stats_index'])
		echo '
	<div id="index_common_stats">
		', $txt['members'], ': ', $context['common_stats']['total_members'], ' &nbsp;&#8226;&nbsp; ', $txt['posts_made'], ': ', $context['common_stats']['total_posts'], ' &nbsp;&#8226;&nbsp; ', $txt['topics'], ': ', $context['common_stats']['total_topics'], '
		', ($settings['show_latest_member'] ? ' ' . $txt['welcome_member'] . ' <strong>' . $context['common_stats']['latest_member']['link'] . '</strong>' . $txt['newest_member'] : '') , '
	</div>';

	// Show the news fader?  (assuming there are things to show...)
	if ($settings['show_newsfader'] && !empty($context['fader_news_lines']))
	{
		echo '
	<div id="newsfader">
		<div class="cat_bar">
			<h3 class="catbg">
				<img id="newsupshrink" src="', $settings['images_url'], '/collapse.gif" alt="*" title="', $txt['upshrink_description'], '" align="bottom" style="display: none;" />
				', $txt['news'], '
			</h3>
		</div>
		<ul class="reset" id="smfFadeScroller"', empty($options['collapse_news_fader']) ? '' : ' style="display: none;"', '>';

		foreach ($context['news_lines'] as $news)
			echo '
			<li>', $news, '</li>';

		echo '
		</ul>
	</div>
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/fader.js"></script>
	<script type="text/javascript"><!-- // --><![CDATA[

		// Create a news fader object.
		var oNewsFader = new smf_NewsFader({
			sSelf: \'oNewsFader\',
			sFaderControlId: \'smfFadeScroller\',
			sItemTemplate: ', JavaScriptEscape('<strong>%1$s</strong>'), ',
			iFadeDelay: ', empty($settings['newsfader_time']) ? 5000 : $settings['newsfader_time'], '
		});

		// Create the news fader toggle.
		var smfNewsFadeToggle = new smc_Toggle({
			bToggleEnabled: true,
			bCurrentlyCollapsed: ', empty($options['collapse_news_fader']) ? 'false' : 'true', ',
			aSwappableContainers: [
				\'smfFadeScroller\'
			],
			aSwapImages: [
				{
					sId: \'newsupshrink\',
					srcExpanded: smf_images_url + \'/collapse.gif\',
					altExpanded: ', JavaScriptEscape($txt['upshrink_description']), ',
					srcCollapsed: smf_images_url + \'/expand.gif\',
					altCollapsed: ', JavaScriptEscape($txt['upshrink_description']), '
				}
			],
			oThemeOptions: {
				bUseThemeSettings: ', $context['user']['is_guest'] ? 'false' : 'true', ',
				sOptionName: \'collapse_news_fader\',
				sSessionVar: ', JavaScriptEscape($context['session_var']), ',
				sSessionId: ', JavaScriptEscape($context['session_id']), '
			},
			oCookieOptions: {
				bUseCookie: ', $context['user']['is_guest'] ? 'true' : 'false', ',
				sCookieName: \'newsupshrink\'
			}
		});
	// ]]></script>';
	}
	echo '
	<div id="boardindex_table" class="table-responsive">
		<table class="table table-hover table-strip">';

	/* Each category in categories is made up of:
	id, href, link, name, is_collapsed (is it collapsed?), can_collapse (is it okay if it is?),
	new (is it new?), collapse_href (href to collapse/expand), collapse_image (up/down image),
	and boards. (see below.) */
	foreach ($context['categories'] as $category)
	{
		// If theres no parent boards we can see, avoid showing an empty category (unless its collapsed)
		if (empty($category['boards']) && !$category['is_collapsed'])
			continue;

		echo '
			<thead class="cat_bar" id="category_', $category['id'], '">
				<tr>
					<td colspan="4">
						<div>
							<h3 class="sinmargen">';

		// If this category even can collapse, show a link to collapse it.
		if ($category['can_collapse'])
			echo '
								<a href="', $category['collapse_href'], '"> <span class="icon margin-left-sm">keyboard_arrow_down</span> </a>';

		echo ' ',$category['link'];

		if (!$context['user']['is_guest'] && !empty($category['show_unread']))
			echo '
							<div class="pull-right">
								<a href="', $scripturl, '?action=unread;c=', $category['id'], '">', $txt['view_unread_category'], '</a>
							</div>';


		echo '
							</h3>
						</div>
					</td>
				</tr>
			</thead>';

		// Assuming the category hasn't been collapsed...
		if (!$category['is_collapsed'])
		{

			echo '
			<tbody id="category_', $category['id'], '_boards">';
			/* Each board in each category's boards has:
			new (is it new?), id, name, description, moderators (see below), link_moderators (just a list.),
			children (see below.), link_children (easier to use.), children_new (are they new?),
			topics (# of), posts (# of), link, href, and last_post. (see below.) */
			foreach ($category['boards'] as $board)
			{
				echo '
				<tr id="board_', $board['id'], '" >
					<td class="echoneing"  ', !empty($board['children']) ? ' rowspan="2"' : '', '>
						<a href="', ($board['is_redirect'] || $context['user']['is_guest'] ? $board['href'] : $scripturl . '?action=unread;board=' . $board['id'] . '.0;children'), '">';

				// If the board or children is new, show an indicator.
				if ($board['new'] || $board['children_new'])
				{echo '
							<a class="avatar avatar-brand-accent avatar-md mdc-bg-blue-700 mdc-text-grey-50" href="', $board['href'], '" name="b', $board['id'], '"><span class="icon">android</span></a>';}

				// Is it a redirection board?
				elseif ($board['is_redirect'])
					echo '
							<a class="avatar avatar-brand avatar-md mdc-bg-cyan-700 mdc-text-grey-50" href="', $board['href'], '" name="b', $board['id'], '"><span class="icon">subdirectory_arrow_right</span></a>';
				// No new posts at all! The agony!!
				else
					echo '
							<a class="avatar avatar-md mdc-bg-blue-grey-300 mdc-text-grey-50" href="', $board['href'], '" name="b', $board['id'], '"><span class="icon">android</span></a>';

				echo '
						</a>
					</td>
					<td class="info">
						<a class="subject" href="', $board['href'], '" name="b', $board['id'], '">', $board['name'], '</a>';

				// Has it outstanding posts for approval?
				if ($board['can_approve_posts'] && ($board['unapproved_posts'] || $board['unapproved_topics']))
					echo '
						<a href="', $scripturl, '?action=moderate;area=postmod;sa=', ($board['unapproved_topics'] > 0 ? 'topics' : 'posts'), ';brd=', $board['id'], ';', $context['session_var'], '=', $context['session_id'], '" title="', sprintf($txt['unapproved_posts'], $board['unapproved_topics'], $board['unapproved_posts']), '" class="moderation_link">(!)</a>';

				echo '

						<p>', $board['description'] , '</p>';

				// Show the "Moderators: ". Each has name, href, link, and id. (but we're gonna use link_moderators.)
				if (!empty($board['moderators']))
					echo '
						<p class="moderators">', count($board['moderators']) == 1 ? $txt['moderator'] : $txt['moderators'], ': ', implode(', ', $board['link_moderators']), '</p>';

				// Show some basic information about the number of posts, etc.
				echo '
					</td>
					<td class="stats windowbg">
						<p>', comma_format($board['posts']), ' ', $board['is_redirect'] ? $txt['redirects'] : $txt['posts'], ' <br />
						', $board['is_redirect'] ? '' : comma_format($board['topics']) . ' ' . $txt['board_topics'], '
						</p>
					</td>
					<td class="lastpost">';

				/* The board's and children's 'last_post's have:
				time, timestamp (a number that represents the time.), id (of the post), topic (topic id.),
				link, href, subject, start (where they should go for the first unread post.),
				and member. (which has id, name, link, href, username in it.) */
				if (!empty($board['last_post']['id']))
					echo '
						<p><strong>', $txt['last_post'], '</strong>  ', $txt['by'], ' ', $board['last_post']['member']['link'] , '<br />
						', $txt['in'], ' ', $board['last_post']['link'], '<br />
						', $txt['on'], ' ', $board['last_post']['time'],'
						</p>';
				echo '
					</td>
				</tr>';
				// Show the "Child Boards: ". (there's a link_children but we're going to bold the new ones...)
				if (!empty($board['children']))
				{
					// Sort the links into an array with new boards bold so it can be imploded.
					$children = array();
					/* Each child in each board's children has:
							id, name, description, new (is it new?), topics (#), posts (#), href, link, and last_post. */
					foreach ($board['children'] as $child)
					{
						if (!$child['is_redirect'])
							$child['link'] = '<a href="' . $child['href'] . '" ' . ($child['new'] ? 'class="new_posts" ' : '') . 'title="' . ($child['new'] ? $txt['new_posts'] : $txt['old_posts']) . ' (' . $txt['board_topics'] . ': ' . comma_format($child['topics']) . ', ' . $txt['posts'] . ': ' . comma_format($child['posts']) . ')">' . $child['name'] . ($child['new'] ? '</a> <a href="' . $scripturl . '?action=unread;board=' . $child['id'] . '" title="' . $txt['new_posts'] . ' (' . $txt['board_topics'] . ': ' . comma_format($child['topics']) . ', ' . $txt['posts'] . ': ' . comma_format($child['posts']) . ')"><img src="' . $settings['lang_images_url'] . '/new.gif" class="new_posts" alt="" />' : '') . '</a>';
						else
							$child['link'] = '<a href="' . $child['href'] . '" title="' . comma_format($child['posts']) . ' ' . $txt['redirects'] . '">' . $child['name'] . '</a>';

						// Has it posts awaiting approval?
						if ($child['can_approve_posts'] && ($child['unapproved_posts'] || $child['unapproved_topics']))
							$child['link'] .= ' <a href="' . $scripturl . '?action=moderate;area=postmod;sa=' . ($child['unapproved_topics'] > 0 ? 'topics' : 'posts') . ';brd=' . $child['id'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '" title="' . sprintf($txt['unapproved_posts'], $child['unapproved_topics'], $child['unapproved_posts']) . '" class="moderation_link">(!)</a>';

						$children[] = $child['new'] ? '<strong>' . $child['link'] . '</strong>' : $child['link'];
					}
					echo '
					<tr id="board_', $board['id'], '_children">
						<td colspan="3" class="children windowbg">
							<strong>', $txt['parent_boards'], '</strong>: ', implode(', ', $children), '
						</td>
					</tr>';
				}
			}
			echo '
			</tbody>';
		}
	}
	echo '
		</table>
	</div>';

	if ($context['user']['is_logged'])
	{
		echo '
	<div id="posting_icons" align="center">';

		// Mark read button.
		$mark_read_button = array(
			'markread' => array('text' => 'mark_as_read', 'image' => 'markread.gif', 'lang' => true, 'url' => $scripturl . '?action=markasread;sa=all;' . $context['session_var'] . '=' . $context['session_id']),
		);

		echo '
			<ul class="nav-justified ulsinpuntos">
				<li><span class="avatar avatar-brand-accent avatar-xs mdc-bg-blue-700 mdc-text-grey-50" style="display:inline-block;" ><span class="icon">android</span></span>&nbsp;', $txt['new_posts'], '</li>
				<li><span class="avatar avatar-xs mdc-bg-blue-grey-300 mdc-text-grey-50" style="display:inline-block;"><span class="icon">android</span></span>&nbsp;', $txt['old_posts'], '</li>
				<li><span class="avatar avatar-brand avatar-xs mdc-bg-cyan-700 mdc-text-grey-50" style="display:inline-block;" > <span class="icon">subdirectory_arrow_right</span></span>&nbsp;', $txt['redirect_board'], '</li>
			</ul>
		
	</div>';

		// Show the mark all as read button?
		if ($settings['show_mark_read'] && !empty($context['categories']))
			echo '<div class="mark_read">', template_button_strip($mark_read_button, 'right'), '</div>';
	}
	else
	{
		echo '
	<div id="posting_icons" align="center">
		<ul class="nav-justified ulsinpuntos">
			<li><span class="avatar avatar-xs mdc-bg-blue-grey-300 mdc-text-grey-50" style="display:inline-block;"><span class="icon">android</span></span>&nbsp;', $txt['old_posts'], '</li>
			<li><span class="avatar avatar-brand avatar-xs mdc-bg-cyan-700 mdc-text-grey-50" style="display:inline-block;" > <span class="icon">subdirectory_arrow_right</span></span>&nbsp;', $txt['redirect_board'], '</li>
		</ul>
	</div>';
	}

	template_info_center();
}

/**
 *
 */
function template_info_center()
{
	global $context, $settings, $options, $txt, $scripturl, $modSettings;

	$tabs = '';
	$contenido = '';

    if (!empty($settings['number_recent_posts']) && (!empty($context['latest_posts']) || !empty($context['latest_post'])))
    {
        $tabs .= '
                  <li>
                        <a class="waves-attach waves-light" data-toggle="tab" href="#recent"><span class="icon">note</span>'.$txt['recent_posts'].'</a>
                  </li>';

		$contenido .= '
				<div class="tab-pane fade active in" id="recent">
				';

		// Only show one post.
		if ($settings['number_recent_posts'] == 1)
		{
			// latest_post has link, href, time, subject, short_subject (shortened with...), and topic. (its id.)
			$contenido .= $txt['recent_view']. ' &quot;'. $context['latest_post']['link']. '&quot; '. $txt['recent_updated']. ' ('. $context['latest_post']['time']. ')';
		}

		// Show lots of posts.
		elseif (!empty($context['latest_posts']))
		{
			/* Each post in latest_posts has:
					board (with an id, name, and link.), topic (the topic's id.), poster (with id, name, and link.),
					subject, short_subject (shortened with...), time, link, and href. */
			foreach ($context['latest_posts'] as $post)
				$contenido .= '
					<div>
					 <span class="icon">fiber_new</span><b>'. $post['link']. '</b> '. $txt['by']. ' '. $post['poster']['link']. ' ('. $post['board']['link']. ') <span class="pull-right visible-md-block visible-lg-block">'. $post['time']. '</span>
					</div>
					';
		}
		$contenido.= '
					<a href="'.$scripturl.'?action=recent" ><span class="icon">note</span> '.$txt['recent_posts'].'</a>
				</div>';
    }
	// Show information about events, birthdays, and holidays on the calendar.
	if ($context['show_calendar'])
	{
		$tabs .= '<li class="active">
                        <a class="waves-attach waves-light" data-toggle="tab" href="#calendar"><span class="icon">date_range</span>'.($context['calendar_only_today'] ? $txt['calendar_today'] : $txt['calendar_upcoming']).'</a>
                  </li>';
		$contenido .= '
				<div class="tab-pane fade" id="calendar">
					<small>
				';
		// Holidays like "Christmas", "Chanukah", and "We Love [Unknown] Day" :P.
		if (!empty($context['calendar_holidays']))
			$contenido .= '
						<span class="holiday">'. $txt['calendar_prompt']. ' '. implode(', ', $context['calendar_holidays']). '</span><br />';

		// People's birthdays. Like mine. And yours, I guess. Kidding.
		if (!empty($context['calendar_birthdays'])) {
			$contenido .= '
						<span class="birthday">'.($context['calendar_only_today'] ? $txt['birthdays'] : $txt['birthdays_upcoming']).'</span>';
			/* Each member in calendar_birthdays has:
                    id, name (person), age (if they have one set?), is_last. (last in list?), and is_today (birthday is today?) */
			foreach ($context['calendar_birthdays'] as $member)
				$contenido .= '
						<a href="'.$scripturl.'?action=profile;u='.$member['id'].'">'.($member['is_today'] ? '<strong>' : '').$member['name'].($member['is_today'] ? '</strong>' : '').(isset($member['age']) ? ' ('.$member['age'].')' : '').'</a>'.($member['is_last'] ? '<br />' : ', ');

		}
		// Events like community get-togethers.
		if (!empty($context['calendar_events']))
		{
			$contenido .= '
					<span class="event">'.( $context['calendar_only_today'] ? $txt['events'] : $txt['events_upcoming']). '</span>';
			/* Each event in calendar_events should have:
				title, href, is_last, can_edit (are they allowed?), modify_href, and is_today. */
			foreach ($context['calendar_events'] as $event)
				$contenido .= '
					'. ($event['can_edit'] ? '<a href="' . $event['modify_href'] . '" title="' . $txt['calendar_edit'] . '"><img src="' . $settings['images_url'] . '/icons/modify_small.gif" alt="*" /></a> ' : ''). ($event['href'] == '' ? '' : '<a href="' . $event['href'] . '">'). ($event['is_today'] ? '<strong>' . $event['title'] . '</strong>' : $event['title']). ($event['href'] == '' ? '' : '</a>'). ($event['is_last'] ? '<br />' : ', ');
		}
		$contenido .= '
					</small>
					<a href="'.$scripturl.'?action=calendar" ><span class="icon">date_range</span>'.($context['calendar_only_today'] ? $txt['calendar_today'] : $txt['calendar_upcoming']).'</a>
				</div>';
	}

	// Show statistical style information...
	if ($settings['show_stats_index'])
	{

		$tabs .= '
			  <li>
					<a class="waves-attach waves-light" data-toggle="tab" href="#stats"><span class="icon">assessment</span>'.$txt['forum_stats'].'</a>
			  </li>';
		$contenido .= '
				<div class="tab-pane fade" id="stats">
					<p>
						'. $context['common_stats']['total_posts']. ' '. $txt['posts_made']. ' '. $txt['in']. ' '. $context['common_stats']['total_topics']. ' '. $txt['topics']. ' '. $txt['by']. ' '. $context['common_stats']['total_members']. ' '. $txt['members']. '. '. (!empty($settings['show_latest_member']) ? $txt['latest_member'] . ': <strong> ' . $context['common_stats']['latest_member']['link'] . '</strong>' : ''). '<br />
						'. (!empty($context['latest_post']) ? $txt['latest_post'] . ': <strong>&quot;' . $context['latest_post']['link'] . '&quot;</strong>  ( ' . $context['latest_post']['time'] . ' )<br />' : ''). '
						<a href="'. $scripturl. '?action=recent">'. $txt['recent_view']. '</a>'. ($context['show_stats'] ? '<br />
						<a href="' . $scripturl . '?action=stats">' . $txt['more_stats'] . '</a>' : ''). '
					</p>
					<a href="'. $scripturl. '?action=stats"><span class="icon">assessment</span>'.$txt['forum_stats'].'</a>
				</div>';
	}



	echo'
    <p>new info center</p>
    <div class="container">
        <div class="row">
            <div class="xs-col-12">
                <div class="card">
                    <div class="card-main">
                        <div class="card-header">
                            <div class="card-inner">
                                <h3 class="sinmargen"> 
                                    <span id="upshrink_ic" class="icon">expand_more</span>', sprintf($txt['info_center_title'], $context['forum_name_html_safe']), ' 
                                </h3>
                            </div>
                        </div>
                        <div id="upshrinkHeaderIC"', empty($options['collapse_header_ic']) ? '' : ' style="display: none;"', '>		
							<div class="card-inner margin-top-no">
							 	<nav class="tab-nav tab-nav-brand margin-top-no">
										<ul class="nav nav-justified">
											',$tabs,'
										</ul>
								</nav>
								',$contenido,'                           
							</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	';

	// Info center collapse object.
	echo '
	<script type="text/javascript"><!-- // --><![CDATA[
		var oInfoCenterToggle = new smc_Toggle({
			bToggleEnabled: true,
			bCurrentlyCollapsed: ', empty($options['collapse_header_ic']) ? 'false' : 'true', ',
			aSwappableContainers: [
				\'upshrinkHeaderIC\'
			],
			aSwapImages: [
				{
					sId: \'upshrink_ic\',
					srcExpanded: smf_images_url + \'/collapse.gif\',
					altExpanded: ', JavaScriptEscape($txt['upshrink_description']), ',
					srcCollapsed: smf_images_url + \'/expand.gif\',
					altCollapsed: ', JavaScriptEscape($txt['upshrink_description']), '
				}
			],
			oThemeOptions: {
				bUseThemeSettings: ', $context['user']['is_guest'] ? 'false' : 'true', ',
				sOptionName: \'collapse_header_ic\',
				sSessionVar: ', JavaScriptEscape($context['session_var']), ',
				sSessionId: ', JavaScriptEscape($context['session_id']), '
			},
			oCookieOptions: {
				bUseCookie: ', $context['user']['is_guest'] ? 'true' : 'false', ',
				sCookieName: \'upshrinkIC\'
			}
		});
	// ]]></script>';
}
?>
