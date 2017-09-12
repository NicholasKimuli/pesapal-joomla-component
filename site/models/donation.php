<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_donation
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Donation Model
 *
 * @since  0.0.1
 */
class DonationModelDonation extends JModelItem
{
	/**
	 * @var string message
	 */
	protected $message;

	/**
	 * Get the message
         *
	 * @return  string  The message to be displayed to the user
	 */
	public function getMsg()
	{
		if (!isset($this->message))
		{
			$jinput = JFactory::getApplication()->input;
            $id     = $jinput->get('id', 1, 'INT');
            $fname = $_POST['fname'];

			// switch ($id)
			// {
			// 	case 2:
			// 		$this->message = 'Good bye World!';
			// 		break;
			// 	default:
			// 	case 1:
					$this->message = $fname;
			// 		break;
			// }
		}

		return $this->message;
	}
}