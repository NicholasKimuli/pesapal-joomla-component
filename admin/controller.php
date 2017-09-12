<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_donation
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * General Controller of Donation component
 *
 * @package     Joomla.Administrator
 * @subpackage  com_donation
 * @since       0.0.7
 */
class DonationController extends JControllerLegacy
{
	/**
	 * The default view for the display method.
	 *
	 * @var string
	 * @since 12.2
	 */
	protected $default_view = 'donations';
}