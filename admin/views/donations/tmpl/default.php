<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_donation
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>

<form action="index.php?option=com_donation&view=donations" method="post" id="adminForm" name="adminForm">
	<table class="table table-striped table-hover">
		<thead>
		<tr>
			<th width="10%">
                <?php echo JText::_('COM_DONATION_NUM'); ?>
            </th>
			<th width="5%">
				<?php echo JHtml::_('grid.checkall'); ?>
			</th>
			<th width="20%">
                <?php echo JText::_('COM_DONATION_NAME') ;?>
			</th>
			<th width="20%">
                <?php echo JText::_('COM_DONATION_EMAIL'); ?>
			</th>
			<th width="20%">
                <?php echo JText::_('COM_DONATION_AMOUNT'); ?>
			</th>
			<th width="20%">
                <?php echo JText::_('COM_DONATION_MOBILE'); ?>
			</th>
			<th width="5%">
                <?php echo JText::_('COM_DONATION_ID'); ?>
			</th>
		</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php if (!empty($this->items)) : ?>
				<?php foreach ($this->items as $i => $row) : ?>

					<tr>
						<td>
							<?php echo $this->pagination->getRowOffset($i); ?>
						</td>
						<td>
                            <?php echo JHtml::_('grid.id', $i, $row->id); ?>
						</td>
						<td>
                            <?php echo $row->fname . ' ' . $row->lname; ?>
						</td>
						<td align="center">
							<?php echo $row->email; ?>
						</td>
						<td align="center">
							<?php echo 'KES ' . $row->amount; ?>
						</td>
						<td align="center">
							<?php echo $row->mobile; ?>
						</td>
						<td align="center">
							<?php echo $row->id; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</form>