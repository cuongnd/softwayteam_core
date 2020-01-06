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
		<label for="params[payment_params][email]"><?php
			echo WoobookingText::_( 'HIKA_EMAIL' );
		?></label>
	</td>
	<td>
		<input type="text" name="params[payment_params][email]" value="<?php echo $this->escape(@$this->element->payment_params->email); ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][address_type]"><?php
			echo WoobookingText::_( 'PAYPAL_ADDRESS_TYPE' );
		?></label>
	</td>
	<td><?php
		echo $this->data['address']->display('params[payment_params][address_type]', @$this->element->payment_params->address_type);
	?></td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][address_override]"><?php
			echo WoobookingText::_( 'ADDRESS_OVERRIDE' );
		?></label>
	</td>
	<td><?php
		echo Html::_('select.booleanlist', "params[payment_params][address_override]" , '', @$this->element->payment_params->address_override);
	?></td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][details]"><?php
			echo WoobookingText::_('SEND_DETAILS_OF_ORDER');
		?></label>
	</td>
	<td><?php
		echo Html::_('select.booleanlist', "params[payment_params][details]" , '', @$this->element->payment_params->details);
	?></td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][iframe]"><?php
			echo WoobookingText::_( 'IFRAME' );
		?></label>
	</td>
	<td><?php
		echo Html::_('select.booleanlist', "params[payment_params][iframe]" , '', @$this->element->payment_params->iframe);
	?></td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][sandbox]"><?php
			echo WoobookingText::_( 'SANDBOX' );
		?></label>
	</td>
	<td><?php
		echo Html::_('select.booleanlist', "params[payment_params][sandbox]" , '', @$this->element->payment_params->sandbox);
	?></td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][debug]"><?php
			echo WoobookingText::_('DEBUG');
		?></label>
	</td>
	<td><?php
		echo Html::_('select.booleanlist', "params[payment_params][debug]" , '', @$this->element->payment_params->debug);
	?></td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][cancel_url]"><?php
			echo WoobookingText::_('CANCEL_URL');
		?></label>
	</td>
	<td>
		<input type="text" name="params[payment_params][cancel_url]" value="<?php echo $this->escape(@$this->element->payment_params->cancel_url); ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][return_url]"><?php
			echo WoobookingText::_('RETURN_URL');
		?></label>
	</td>
	<td>
		<input type="text" name="params[payment_params][return_url]" value="<?php echo $this->escape(@$this->element->payment_params->return_url); ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][logoImage]"><?php
			echo WoobookingText::_('HEADER_IMAGE');
		?></label>
	</td>
	<td>
		<input type="text" name="params[payment_params][logoImage]" value="<?php echo $this->escape(@$this->element->payment_params->logoImage); ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][ips]"><?php
			echo WoobookingText::_('IPS');
		?></label>
	</td>
	<td>
		<textarea id="paypal_ips" name="params[payment_params][ips]" ><?php echo (!empty($this->element->payment_params->ips) && is_array($this->element->payment_params->ips)?trim(implode(',',$this->element->payment_params->ips)):''); ?></textarea>
		<br/>
		<a href="#" onclick="return paypal_refreshIps();"><?php echo WoobookingText::_('REFRESH_IPS');?></a>
<script type="text/javascript">
function paypal_refreshIps() {
	var w = window, d = document, o = w.Oby;
	o.xRequest('<?php echo hikashop_completeLink('plugins&plugin_type=payment&task=edit&name='.$this->data['name'].'&subtask=ips',true,true);?>', null, function(xhr) {
		d.getElementById('paypal_ips').value = xhr.responseText;
	});
	return false;
}
</script>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][invalid_status]"><?php
			echo WoobookingText::_('INVALID_STATUS');
		?></label>
	</td>
	<td><?php
		echo $this->data['order_statuses']->display("params[payment_params][invalid_status]", @$this->element->payment_params->invalid_status);
	?></td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][pending_status]"><?php
			echo WoobookingText::_('PENDING_STATUS');
		?></label>
	</td>
	<td><?php
		echo $this->data['order_statuses']->display("params[payment_params][pending_status]", @$this->element->payment_params->pending_status);
	?></td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][verified_status]"><?php
			echo WoobookingText::_('VERIFIED_STATUS');
		?></label>
	</td>
	<td><?php
		echo $this->data['order_statuses']->display("params[payment_params][verified_status]", @$this->element->payment_params->verified_status);
	?></td>
</tr>
