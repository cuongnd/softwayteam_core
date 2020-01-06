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
		<label for="params[payment_params][pspid]">
			<?php echo WoobookingText::_('PSPID'); ?>
		</label>
	</td>
	<td>
		<input type="text" name="params[payment_params][pspid]" value="<?php echo @$this->element->payment_params->pspid; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][shain_passphrase]">
			SHA-IN PASSPHRASE
		</label>
	</td>
	<td>
		<input type="text" name="params[payment_params][shain_passphrase]" value="<?php echo @$this->element->payment_params->shain_passphrase; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][shaout_passphrase]">
			SHA-OUT PASSPHRASE
		</label>
	</td>
	<td>
		<input type="text" name="params[payment_params][shaout_passphrase]" value="<?php echo @$this->element->payment_params->shaout_passphrase; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][hash_method]">
			<?php echo WoobookingText::_( 'HASH_METHOD' ); ?>
		</label>
	</td>
	<td>
		<?php
		$values = array();
		if( function_exists('hash') || function_exists('sha1') ) {
			$values[] = Html::_('select.option', 'sha1',WoobookingText::_('SHA1'));
		} else {
			$values[] = Html::_('select.option', 'sha1',WoobookingText::_('SHA1').' '.WoobookingText::_('not present'), 'value', 'text', true);
		}
		if( function_exists('hash')){
			$values[] = Html::_('select.option', 'sha256',WoobookingText::_('SHA256'));
			$values[] = Html::_('select.option', 'sha512',WoobookingText::_('SHA512'));
		}else{
			$values[] = Html::_('select.option', 'sha256',WoobookingText::_('SHA256').' '.WoobookingText::_('not present'), 'value', 'text', true);
			$values[] = Html::_('select.option', 'sha512',WoobookingText::_('SHA512').' '.WoobookingText::_('not present'), 'value', 'text', true);
		}

		echo Html::_('select.genericlist',   $values, "params[payment_params][hash_method]" , 'class="inputbox" size="1"', 'value', 'text', @$this->element->payment_params->hash_method ); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][environnement]">
			<?php echo WoobookingText::_( 'ENVIRONNEMENT' ); ?>
		</label>
	</td>
	<td>
		<?php
		$values = array();
		$values[] = Html::_('select.option', 'production', WoobookingText::_('HIKA_PRODUCTION'));
		$values[] = Html::_('select.option', 'test', WoobookingText::_('HIKA_TEST'));

		echo Html::_('select.genericlist',   $values, "params[payment_params][environnement]" , 'class="inputbox" size="1"', 'value', 'text', @$this->element->payment_params->environnement ); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label>
			After payment URL
		</label>
	</td>
	<td>
		<?php echo htmlentities(@$this->element->payment_params->status_url); ?>
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
		<label for="params[payment_params][pending_status]">
			<?php echo WoobookingText::_( 'PENDING_STATUS' ); ?>
		</label>
	</td>
	<td>
		<?php echo $this->data['order_statuses']->display("params[payment_params][pending_status]",@$this->element->payment_params->pending_status); ?>
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
