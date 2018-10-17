<?php
/**********************************************************************************
* Subs-BBCode-XBox.php
***********************************************************************************
* This mod is licensed under the 2-clause BSD License, which can be found here:
*	http://opensource.org/licenses/BSD-2-Clause
***********************************************************************************
* This program is distributed in the hope that it is and will be useful, but      *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY    *
* or FITNESS FOR A PARTICULAR PURPOSE.                                            *
**********************************************************************************/
if (!defined('SMF')) 
	die('Hacking attempt...');

function BBCode_XBox(&$bbc)
{
	// Format: [xboxdvr width=x height=x frameborder=x]{xboxdvr URL}[/xboxdvr]
	$bbc[] = array(
		'tag' => 'xboxdvr',
		'type' => 'unparsed_content',
		'parameters' => array(
			'width' => array('match' => '(\d+)'),
			'height' => array('optional' => true, 'match' => '(\d+)'),
			'frameborder' => array('optional' => true, 'match' => '(\d+)'),
		),
		'validate' => 'BBCode_XBoxDVR_Validate',
		'content' => '{width}|{height}|{frameborder}',
		'disabled_content' => '$1',
	);

	// Format: [xboxdvr width=x height=x frameborder=x]{xboxdvr URL}[/xboxdvr]
	$bbc[] = array(
		'tag' => 'xboxdvr',
		'type' => 'unparsed_content',
		'parameters' => array(
			'frameborder' => array('match' => '(\d+)'),
		),
		'validate' => 'BBCode_XBoxDVR_Validate',
		'content' => '0|0|{frameborder}',
		'disabled_content' => '$1',
	);

	// Format: [xboxdvr]{xboxdvr URL}[/xboxdvr]
	$bbc[] = array(
		'tag' => 'xboxdvr',
		'type' => 'unparsed_content',
		'validate' => 'BBCode_XBoxDVR_Validate',
		'content' => '0|0|0',
		'disabled_content' => '$1',
	);

	// Format: [xboxclips width=x height=x frameborder=x]{xboxclips URL}[/xboxclips]
	$bbc[] = array(
		'tag' => 'xboxclips',
		'type' => 'unparsed_content',
		'parameters' => array(
			'frameborder' => array('match' => '(\d+)'),
		),
		'validate' => 'BBCode_XBoxClips_Validate',
		'content' => '{frameborder}',
		'disabled_content' => '$1',
	);

	// Format: [xboxclips]{xboxclips URL}[/xboxclips]
	$bbc[] = array(
		'tag' => 'xboxclips',
		'type' => 'unparsed_content',
		'validate' => 'BBCode_XBoxClips_Validate',
		'content' => '0',
		'disabled_content' => '$1',
	);
}

function BBCode_XBox_Button(&$buttons)
{
	$buttons[count($buttons) - 1][] = array(
		'image' => 'xbox_dvr',
		'code' => 'xboxdvr',
		'description' => 'xboxdvr',
		'before' => '[xboxdvr]',
		'after' => '[/xboxdvr]',
	);
	$buttons[count($buttons) - 1][] = array(
		'image' => 'xbox_clips',
		'code' => 'xboxclips',
		'description' => 'xboxclips',
		'before' => '[xboxclips]',
		'after' => '[/xboxclips]',
	);
}

function BBCode_XBoxDVR_Validate(&$tag, &$data, &$disabled)
{
	global $txt, $modSettings;
	
	if (empty($data))
		return ($tag['content'] = $txt['XBox_no_post_id']);
	$data = strtr(trim($data), array('<br />' => ''));
	if (strpos($data, 'http://') !== 0 && strpos($data, 'https:\/\/') !== 0)
		$data = 'http://' . $data;
	$pattern = '#(http|https):\/\/(|(.+?).)xboxdvr\.com/gamer/([\w\_]+)/video/(\d+)#i';
	if (!preg_match($pattern, $data, $parts))
		return ($tag['content'] = $txt['XBox_no_post_id']);
	list($width, $height, $frameborder) = explode('|', $tag['content']);
	if (empty($width) && !empty($modSettings['XBox_default_width']))
		$width = $modSettings['XBox_default_width'];
	if (empty($height) && !empty($modSettings['XBox_default_height']))
		$height = $modSettings['XBox_default_height'];
	$tag['content'] = '<div style="' . (empty($width) ? '' : 'max-width: ' . $width . 'px;') . (empty($height) ? '' : 'max-height: ' . $height . 'px;') . '"><div class="xbox-wrapper">' .
		'<iframe src="' . $data .'/embed" scrolling="no" frameborder="' . $frameborder . '" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div></div>';
}

function BBCode_XBoxClips_Validate(&$tag, &$data, &$disabled)
{
	global $txt, $modSettings;
	
	if (empty($data))
		return ($tag['content'] = $txt['XBox_no_post_id']);
	$data = strtr(trim($data), array('<br />' => ''));
	if (strpos($data, 'http://') !== 0 && strpos($data, 'https:\/\/') !== 0)
		$data = 'http://' . $data;
	$pattern = '#(http|https):\/\/(|(.+?).)xboxclips\.com\/([\w\+\-\_]+)\/([0-9a-fA-F\-]{36})#i';
	if (!preg_match($pattern, $data, $parts))
		return ($tag['content'] = $txt['XBox_no_post_id']);
	$frame = $tag['content'];
	$tag['content'] = '<div style="width: 570px; height: 345px;"><div class="xbox-wrapper">' .
		'<iframe src="' . $data .'/embed" scrolling="no" frameborder="' . $frame . '"  webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div></div>';
}

function BBCode_XBox_LoadTheme()
{
	global $context, $settings;
	$context['html_headers'] .= '
	<link rel="stylesheet" type="text/css" href="' . $settings['default_theme_url'] . '/css/BBCode-XBox.css" />';
	$context['allowed_html_tags'][] = '<iframe>';
}

function BBCode_XBox_Settings(&$config_vars)
{
	$config_vars[] = array('int', 'XBox_default_width');
	$config_vars[] = array('int', 'XBox_default_height');
}

function BBCode_XBox_Embed(&$message, &$smileys, &$cache_id, &$parse_tags)
{
	$replace = (strpos($cache_id, 'sig') !== false ? '[url]$0[/url]' : '[xboxdvr]$0[/xboxdvr]');
	$pattern = '~(?<=[\s>\.(;\'"]|^)(http|https):\/\/(|([\w]+)\.)xboxdvr\.com/gamer\/([\w\_]+)\/video\/(\d+)\??[/\w\-_\~%@\?;=#}\\\\]?~';
	$message = preg_replace($pattern, $replace, $message);

	$replace = (strpos($cache_id, 'sig') !== false ? '[url]$0[/url]' : '[xboxclips]$0[/xboxclips]');
	$pattern = '~(?<=[\s>\.(;\'"]|^)(http|https):\/\/(|(.+?)\.)xboxclips\.com\/([\w\+\-\_]+)\/([0-9a-fA-F\-]{36})\??[/\w\-_\~%@\?;=#}\\\\]?~';
	$message = preg_replace($pattern, $replace, $message);

	if (strpos($cache_id, 'sig') !== false)
	{
		$message = preg_replace('#\[xboxdvr.*\](.*)\[\/xboxdvr\]#i', '[url]$1[/url]', $message);
		$message = preg_replace('#\[xboxclips.*\](.*)\[\/xboxclips\]#i', '[url]$1[/url]', $message);
	}
}

?>