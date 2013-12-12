<?php
/**
 * @copyright	Copyright (C) 2008 - 2009  All rights reserved.
 * @license		
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
 * Module chrome for rendering the module in a slider
 */
function modChrome_slider($module, &$params, &$attribs)
{
	jimport('joomla.html.pane');
	// Initialize variables
	$sliders = & JPane::getInstance('sliders');
	$sliders->startPanel( JText::_( $module->title ), 'module' . $module->id );
	echo $module->content;
	$sliders->endPanel();
}
?>