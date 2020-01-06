<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.6.3
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_WOO_BOOKING_EXEC') or die('Restricted access');
?><tr>
	<td class="key">
		<label for="params[payment_params][url]"><?php
			echo WoobookingText::_( 'Url' );
		?></label>
	</td>
	<td>

		<input type="text" class="form-control" disabled name="params[payment_params][url]" value="<?php echo $this->escape(@$this->element->payment_params->url); ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][email]"><?php
			echo WoobookingText::_( 'Email' );
		?></label>
	</td>
	<td>
		<input type="text" class="form-control" name="params[payment_params][email]" value="<?php echo $this->escape(@$this->element->payment_params->email); ?>" />
	</td>
</tr>

