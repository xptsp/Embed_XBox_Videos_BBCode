<?php
/**********************************************************************************
* add_remove_hooks.php                                                            *
***********************************************************************************
* This mod is licensed under the 2-clause BSD License, which can be found here:
*	http://opensource.org/licenses/BSD-2-Clause
***********************************************************************************
* This program is distributed in the hope that it is and will be useful, but      *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY    *
* or FITNESS FOR A PARTICULAR PURPOSE.                                            *
**********************************************************************************/

// If we have found SSI.php and we are outside of SMF, then we are running standalone.
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF')) // If we are outside SMF and can't find SSI.php, then throw an error
	die('<b>Error:</b> Cannot install - please verify you put this file in the same place as SMF\'s SSI.php.');
if (SMF == 'SSI')
	db_extend('packages');
	
// Define the hooks
$hook_functions = array(
	'integrate_pre_include' => '$sourcedir/Subs-BBCode-XBox.php',
	'integrate_load_theme' => 'BBCode_XBox_LoadTheme',
	'integrate_bbc_codes' => 'BBCode_XBox',
	'integrate_bbc_buttons' => 'BBCode_XBox_Button',
	'integrate_general_mod_settings' => 'BBCode_XBox_Settings',
// SMF 2.1+ Hooks below this line:
	'integrate_pre_parsebbc' => 'BBCode_XBox_Embed',
);

// Adding or removing them?
if (!empty($context['uninstalling']))
	$call = 'remove_integration_function';
else
	$call = 'add_integration_function';

// Do the deed
foreach ($hook_functions as $hook => $function)
	$call($hook, $function);

if (SMF == 'SSI')
   echo 'Congratulations! You have successfully installed this mod!';

?>