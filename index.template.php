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
	
	/* We have different variants of this theme for our users. */
	$settings['theme_variants'] = array('light', 'dark');
}

// The main sub template above the content.
function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>';

	// The ?fin20 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index', $context['theme_variant'], '.css?fin20" />
	<link href="http://fonts.googleapis.com/css?family=Titillium+Web:400,700|Alegreya+Sans:400,700" rel="stylesheet" type="text/css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" type="text/javascript"></script>';

	// Some browsers need an extra stylesheet due to bugs/compatibility issues.
	foreach (array('ie7', 'ie6', 'webkit') as $cssfix)
		if ($context['browser']['is_' . $cssfix])
			echo '
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/', $cssfix, '.css" />';

	// RTL languages require an additional stylesheet.
	if ($context['right_to_left'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/rtl.css" />';

	// Here comes the JavaScript bits!
	echo '
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js?fin20"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/theme.js?fin20"></script>
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
<body>';

//While in development, only I can see this theme live on RMRK. Partly for secrecy, partly to stop users getting trapped on a broken theme. 
/*
	$dev_uids = array(273, 1, 14929, 2, 0, 15930, 5726, 14269, 3489, 2572, 880, 2697);
	if (!in_array($context['user']['id'], $dev_uids)){
		die('<div style="padding: 16px; border: 1px solid #f00; border-radius: 4px; margin: 60px auto 0px auto; width: 60%; font-size: 130%; line-height: 160%;"><div style="text-align:center;"><img src="'.$settings['images_url'].'/rmrk7/logo_final.png" alt="RMRK Logo"/> </div>Sorry, you can use this theme when it\'s done! It\'s quite possibly broken right now. <a href="'.$scripturl.'?action=theme;sa=pick;u='.$context['user']['id'].';th=0;'.$context['session_var'].'='.$context['session_id'].'">This link will take you back to the default theme</a>.</div></body></html>');
	}
	
	*/
	
//We're giving project wonderful a try.
echo '
<script type="text/javascript">
   (function(){function pw_load(){
      if(arguments.callee.z)return;else arguments.callee.z=true;
      var d=document;var s=d.createElement(\'script\');
      var x=d.getElementsByTagName(\'script\')[0];
      s.type=\'text/javascript\';s.async=true;
      s.src=\'//www.projectwonderful.com/pwa.js\';
      x.parentNode.insertBefore(s,x);}
   if (window.attachEvent){
    window.attachEvent(\'DOMContentLoaded\',pw_load);
    window.attachEvent(\'onload\',pw_load);}
   else{
    window.addEventListener(\'DOMContentLoaded\',pw_load,false);
    window.addEventListener(\'load\',pw_load,false);}})();
</script>';
	
//Valentines day!
if (($context['user']['is_logged'] && !isset($_COOKIE['RMRKvalentines']) && (date("n_j") == "2_14")) || isset($_GET['valentines'])) {
echo '<script type="text/javascript">
	function SetCookie(c_name,value,expiredays)
	{
		var exdate=new Date()
		exdate.setDate(exdate.getDate()+expiredays)
		document.cookie=c_name+ "=" +escape(value)+
		((expiredays==null) ? "" : ";expires="+exdate.toGMTString())
	}
</script>
<div style="width:100%; padding: 30px 50px; line-height: 200%; background: #f00 url(http://rmrk.net/src/heart.png) 75% 50% no-repeat; color: #fff; font-weight: bold;border-bottom: 10px solid #D40000;">
	<span style="font-size: 160%;">Hey, ',$context['user']['name'],'. </span><span style="font-size:80%;color: #FF6F72;"> You\'ve spent ',$context['user']['total_time_logged_in']['days'],' day(s) and ',$context['user']['total_time_logged_in']['hours'],' hour(s) with us.</span><br /><div style="width: 65%;">';
	
	if($context['user']['total_time_logged_in']['days'] < 1) echo 'Though you haven\'t been around much yet, we\'d like to express our gratitude and we appreciate you being around. Here\'s to hoping you have a great Valentines Day with someone you care about very much.';
	elseif($context['user']['total_time_logged_in']['days'] < 2) echo 'You\'ve not been around as long as some members, but here\'s a little message to show we appreciate you all the same. We at RMRK hope you have a lovely valentines day with someone special to you.';
	elseif($context['user']['total_time_logged_in']['days'] < 7) echo 'Happy Valentines day from us at RMRK, thank you for contributing and posting for so long. We hope you stay with us for next year\'s Valentines too!';
	elseif($context['user']['total_time_logged_in']['days'] < 20) echo 'It\'s Valentines day, and we at RMRK just wanted to say thank you for being such an active member. We really appreciate you, and hope you have a wonderful day with someone you love.<br />Happy Valentines, ',$context['user']['name'],'!';
	else echo 'You\'ve been with us for such a long time - you\'re awesome! Have a great Valentines day with someone special.<br />XOXOX RMRK~';
	
	echo'<br /><br />
	<a href="',$scripturl,'" onClick="SetCookie(\'RMRKvalentines\',\'Loved\',\'7\')" style="border: 1px solid #FF6F72;border-radius:3px;padding:5px;color:#fff;font-size:90%;">Aw, shucks. Thanks!</a>
	
</div></div>';

}
	
}


function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo !empty($settings['forum_width']) ? '
<div id="wrapper" style="width: ' . $settings['forum_width'] . '">' : '', '
	<div id="header"><div class="frame">
		<div id="top_section">
			<a href="', $scripturl, '" id="logo"></a>';
			
			//RMRK7 Upper right Search / User section
			echo '
			<div id="user_search_section">';
			
			//User info area! This stuff is all custom.
			echo '<div id="user_head">
				<span class="user_head_content">';
			
			if ($context['user']['is_logged']) {
				//Do you have an avatar image?
				if (!empty($context['user']['avatar']))
				{
					$context['user']['avatar']['image'] = strtr($context['user']['avatar']['image'], array("class=\"avatar\"" => "class=\"avatar_t\""));
				echo '<img src="',$context['user']['avatar']['href'],'" alt="avatar" style="float:left; margin-left: 11px; margin-right: 6px; max-height:37px;" class="avatar" alt="Avatar"/>';
				} else echo '<a href="' . $scripturl . '?action=profile;u=', $context['user']['id'], ';sa=forumProfile"><img src="' . $settings['images_url'] . '/rmrk7/noavatar.png" class="avatar" alt="missing avatar" width="26" height="37" style="float:left; padding-right:2px;"/></a>';
				
				//In case you forgot, this is your name. It's also an easy link to your profile.
				echo '<a href="' . $scripturl . '?action=profile;u=', $context['user']['id'], ';sa=forumProfile">',$context['user']['name'],'</a> | ';
				
				//Do you have any new messages?
				if ($context['user']['unread_messages'] > 0) {
					echo '<a href="',$scripturl,'?action=pm"><img src="' . $settings['images_url'] . '/rmrk7/pm_new.png" alt="new messages" /></a>';
				} else echo '<a href="',$scripturl,'?action=pm"><img src="' . $settings['images_url'] . '/rmrk7/pm_none.png" alt="new messages" /></a>';
				
				//A...pokeball?
				if ($scripturl == "http://rmrk.net/index.php") echo ' | <a href="http://rmrk.net/pokemon/?trainer='.$context['user']['id'].'"><img src="'.$settings['images_url'].'/rmrk7/pokeball.png" alt="Pokemon" /></a>';
				elseif ($scripturl == "http://127.0.0.1/rmrk7/index.php") echo ' | <a href="http://127.0.0.1/rmrk7/pokemon/?trainer='.$context['user']['id'].'"><img src="'.$settings['images_url'].'/rmrk7/pokeball.png" alt="Pokemon" /></a>';
				
				//Give them the classic, server stressing unread links.
				echo ' | <a href="' . $scripturl . '?action=unread">Unread Posts</a> | 
				<a href="' . $scripturl . '?action=unreadreplies">Unread Replies</a>';
				
			} else { //Show a login form to guests.
				echo '<form action="', $scripturl, '?action=login2" method="post" style="display:inline;" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
									<input type="text" name="user" value="Username" size="17" /> 
									<input type="password" name="passwrd" size="17" />
									<select name="cookielength" style="display:none;">
										<option value="60">', $txt['smf53'], '</option>
										<option value="1440">', $txt['smf47'], '</option>
										<option value="10080">', $txt['smf48'], '</option>
										<option value="43200">', $txt['smf49'], '</option>
										<option value="-1" selected="selected">', $txt['smf50'], '</option>
									</select>
									<input type="submit" value="Login" />

									<input type="hidden" name="hash_passwrd" value="" />
					</form> | <a href="',$scripturl,'?action=register">Register</a>';
			}
			
			echo ' | <form id="topsearch" action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">
					<input type="text" name="search" value="Search..." class="input_text" onblur="if(this.value==\'\') this.value=\'Search...\';" onfocus="if(this.value==\'Search...\') this.value=\'\';" />&nbsp;
					<input type="hidden" name="advanced" value="0" />';

	// Search within current topic?
	if (!empty($context['current_topic']))
		echo '
					<input type="hidden" name="topic" value="', $context['current_topic'], '" />';
	// If we're on a certain board, limit it to this board ;).
	elseif (!empty($context['current_board']))
		echo '
					<input type="hidden" name="brd[', $context['current_board'], ']" value="', $context['current_board'], '" />';

	echo '</form>
	</span>';
			
			echo '</div>';
			
			// Show the menu here, according to the menu sub template.
	template_menu();
			
			
			echo '</div>';
			
			//End RMRK7 Upper right Search / User section
			
	

	echo '
			
		</div>
		<br class="clear" />';

	// Define the upper_section toggle in JavaScript.
	echo '
		<script type="text/javascript"><!-- // --><![CDATA[
			var oMainHeaderToggle = new smc_Toggle({
				bToggleEnabled: true,
				bCurrentlyCollapsed: ', empty($options['collapse_header']) ? 'false' : 'true', ',
				aSwappableContainers: [
					\'upper_section\'
				],
				aSwapImages: [
					{
						sId: \'upshrink\',
						srcExpanded: smf_images_url + \'/upshrink.png\',
						altExpanded: ', JavaScriptEscape($txt['upshrink_description']), ',
						srcCollapsed: smf_images_url + \'/upshrink2.png\',
						altCollapsed: ', JavaScriptEscape($txt['upshrink_description']), '
					}
				],
				oThemeOptions: {
					bUseThemeSettings: ', $context['user']['is_guest'] ? 'false' : 'true', ',
					sOptionName: \'collapse_header\',
					sSessionVar: ', JavaScriptEscape($context['session_var']), ',
					sSessionId: ', JavaScriptEscape($context['session_id']), '
				},
				oCookieOptions: {
					bUseCookie: ', $context['user']['is_guest'] ? 'true' : 'false', ',
					sCookieName: \'upshrink\'
				}
			});
		// ]]></script>';

	
	echo '
		<br class="clear" />
	</div></div>';

	// The main content should go here.
	echo '
	<div id="content_section"><div class="frame">
		<div id="main_content_section">';

	// Custom banners and shoutboxes should be placed here, before the linktree.
	// Don't mind if I do.
	echo '<br /><div style="margin: 5px auto 0px auto; clear:both; width: 728px; text-align: center;"><span style="font-size: 80%; display:none;"><a href="http://wiki.rmrk.net/index.php/Project_Wonderful" target="_blank">Read more about advertising your project here</a></span>
<div id="pw_adbox_72237_5_0"></div>
<script type="text/javascript"></script>
<noscript><map name="admap72237" id="admap72237"><area href="http://www.projectwonderful.com/out_nojs.php?r=0&c=0&id=72237&type=5" shape="rect" coords="0,0,728,90" title="" alt="" target="_blank" /></map>
<table cellpadding="0" cellspacing="0" style="width:728px;border-style:none;background-color:#ffffff;"><tr><td><img src="http://www.projectwonderful.com/nojs.php?id=72237&type=5" style="width:728px;height:90px;border-style:none;" usemap="#admap72237" alt="" /></td></tr><tr><td style="background-color:#ffffff;" colspan="1"><center><a style="font-size:10px;color:#0000ff;text-decoration:none;line-height:1.2;font-weight:bold;font-family:Tahoma, verdana,arial,helvetica,sans-serif;text-transform: none;letter-spacing:normal;text-shadow:none;white-space:normal;word-spacing:normal;" href="http://www.projectwonderful.com/advertisehere.php?id=72237&type=5" target="_blank">Ads by Project Wonderful!  Your ad here, right now: $0</a></center></td></tr></table>
</noscript></div>
';

	// Show the navigation tree.
	theme_linktree();
}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
		</div>
	</div></div>';

	// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
	echo '
	<div id="footer_section"><div class="frame">
		<div class="footer_ad_container">
			<script type="text/javascript"><!--
			google_ad_client = "ca-pub-7302574677795924";
			/* RMRK7-728x90-Light */
			google_ad_slot = "8894737398";
			google_ad_width = 728;
			google_ad_height = 90;
			//-->
			</script>
			<script type="text/javascript"
			src="//pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
		</div>
		
		<div id="quicklinks">
			<span><a href="http://wiki.rmrk.net"><img src="',$settings['images_url'],'/rmrk7/icon_wiki.png" alt=""/>RMRK Wiki</a></span>
			<span><a href="',$scripturl,'?action=chat"><img src="',$settings['images_url'],'/rmrk7/icon_irc.png" alt=""/>IRC Chat</a></span>
			<span><a href="',$scripturl,'?action=donate" style="color:#007300;"><img src="',$settings['images_url'],'/rmrk7/icon_server.png" alt=""/>Donations</a></span>';
			if ($context['user']['is_logged']) {
				if ($context['theme_variant'] == "_light") echo '<span><a href="',$scripturl,'?action=theme;sa=pick;u='.$context['user']['id'].';th='.$settings['theme_id'].';'.$context['session_var'].'='.$context['session_id'].';vrt=dark"><img src="',$settings['images_url'],'/rmrk7/switch_dark.png" alt=""/>Dark Style</a></span>';
			elseif ($context['theme_variant'] == "_dark") echo '<span><a href="',$scripturl,'?action=theme;sa=pick;u='.$context['user']['id'].';th='.$settings['theme_id'].';'.$context['session_var'].'='.$context['session_id'].';vrt=light"><img src="',$settings['images_url'],'/rmrk7/switch_light.png" alt=""/>Light Style</a></span>';
			} else {
				if ($context['theme_variant'] == "_light") echo '<span><a href="',$scripturl,'?theme='.$settings['theme_id'].';vrt=dark"><img src="',$settings['images_url'],'/rmrk7/switch_dark.png" alt=""/>Dark Style</a></span>';
			elseif ($context['theme_variant'] == "_dark") echo '<span><a href="',$scripturl,'?theme='.$settings['theme_id'].';vrt=light"><img src="',$settings['images_url'],'/rmrk7/switch_light.png" alt=""/>Light Style</a></span>';
			}
			echo '
		</div>
		
		<ul class="reset">
			<li class="copyright">', theme_copyright(), '</li>
		</ul>';
		
	//Theme Info
	echo '<p>RMRK7 Theme, Triple Infinity Beta &amp; <a href="https://github.com/Roph/rmrk7" target="_blank">Open Source</a></p>';
	
	echo '<p><img src="',$settings['images_url'],'/rmrk7/negativeman.png" alt=";_;" /></p>';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
		<p>', $txt['page_created'], $context['load_time'], $txt['seconds_with'], $context['load_queries'], $txt['queries'], '</p>';

	echo '
	</div></div>', !empty($settings['forum_width']) ? '
</div>' : '';
}

function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
</body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree($force_show = false)
{
	global $context, $settings, $options, $shown_linktree;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;

	echo '
	<div class="navigate_section">
		<ul>';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
			<li', ($link_num == count($context['linktree']) - 1) ? ' class="last"' : '', '>';

		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo $settings['linktree_link'] && isset($tree['url']) ? '
				<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>' : '<span>' . $tree['name'] . '</span>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		// Don't show a separator for the last one.
		if ($link_num != count($context['linktree']) - 1)
			echo ' &#187;';

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
		<div id="main_menu">
			<ul class="dropmenu" id="menu_nav">';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
				<li id="button_', $act, '">
					<a class="', $button['active_button'] ? 'active ' : '', 'firstlevel" href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
						<span class="', isset($button['is_last']) ? 'last ' : '', 'firstlevel">', $button['title'], '</span>
					</a>';
		if (!empty($button['sub_buttons']))
		{
			echo '
					<ul>';

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
		</div>';
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
				<li><a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_strip_' . $key . (isset($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a></li>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

	echo '
		<div class="buttonlist', !empty($direction) ? ' float' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul>',
				implode('', $buttons), '
			</ul>
		</div>';
}

?>