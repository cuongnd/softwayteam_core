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
		<label for="params[payment_params][merchantid]">
			<?php echo WoobookingText::_( 'MERCHANT_NUMBER' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="params[payment_params][merchantid]" value="<?php echo @$this->element->payment_params->merchantid; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][cpihash]">
			<?php echo WoobookingText::_( 'CPI_STORE_KEY' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="params[payment_params][cpihash]" value="<?php echo @$this->element->payment_params->cpihash; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][instant_capture]">
			<?php echo WoobookingText::_( 'INSTANTCAPTURE' ); ?>
		</label>
	</td>
	<td>
		<?php echo Html::_('select.booleanlist', "params[payment_params][instant_capture]" , '',@$this->element->payment_params->instant_capture	); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][debug]">
			<?php echo WoobookingText::_( 'DEBUG' ); ?>
		</label>
	</td>
	<td>
		<?php echo Html::_('select.booleanlist', "params[payment_params][debug]" , '',@$this->element->payment_params->debug	); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][cancel_url]">
			<?php echo WoobookingText::_( 'CANCEL_URL' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="params[payment_params][cancel_url]" value="<?php echo @$this->element->payment_params->cancel_url; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][return_url]">
			<?php echo WoobookingText::_( 'RETURN_URL' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="params[payment_params][return_url]" value="<?php echo @$this->element->payment_params->return_url; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][invalid_status]">
			<?php echo WoobookingText::_( 'INVALID_STATUS' ); ?>
		</label>
	</td>
	<td>
		<?php echo $this->data['order_statuses']->display("params[payment_params][invalid_status]",@$this->element->payment_params->invalid_status); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][verified_status]">
			<?php echo WoobookingText::_( 'VERIFIED_STATUS' ); ?>
		</label>
	</td>
	<td>
		<?php echo $this->data['order_statuses']->display("params[payment_params][verified_status]",@$this->element->payment_params->verified_status); ?>
	</td>
</tr>
