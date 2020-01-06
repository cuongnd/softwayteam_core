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
		<label for="params[payment_params][url]">
			<?php echo WoobookingText::_( 'URL' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="params[payment_params][url]" value="<?php echo @$this->element->payment_params->url; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][shop_id]">
			<?php echo WoobookingText::_( 'BLUEPAID_SHOP_ID' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="params[payment_params][shop_id]" value="<?php echo @$this->element->payment_params->shop_id; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][status_url]">
			<?php echo WoobookingText::sprintf( 'STATUS_URL',$this->element->payment_name ); ?>
		</label>
	</td>
	<td>
		<input type="hidden" name="params[payment_params][secure_key]" value="<?php echo @$this->element->payment_params->secure_key; ?>" />
		<?php echo str_replace( '&', '&amp;',@$this->element->payment_params->status_url.'&secure_key='.@$this->element->payment_params->secure_key); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][notification]">
			<?php echo WoobookingText::sprintf( 'ALLOW_NOTIFICATIONS_FROM_X', $this->element->payment_name);  ?>
		</label>
	</td>
	<td>
		<?php echo Html::_('select.booleanlist', "params[payment_params][notification]" , '',@$this->element->payment_params->notification	); ?>
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
		<label for="params[payment_params][ips]">
			<?php echo WoobookingText::_( 'IPS' ); ?>
		</label>
	</td>
	<td>
		<textarea id="bluepaid_ips" name="params[payment_params][ips]" ><?php echo (!empty($this->element->payment_params->ips) && is_array($this->element->payment_params->ips)?trim(implode(',',$this->element->payment_params->ips)):''); ?></textarea>
		<br/>
		<a href="#" onclick="return refresh_ips();"><?php echo WoobookingText::_('REFRESH_IPS');?></a>
		<script type="text/javascript">
		function refresh_ips() {
			var w = window, d = document, o = w.Oby;
			o.xRequest(
				'<?php echo hikashop_completeLink('plugins&plugin_type=payment&task=edit&name='.$this->name.'&subtask=ips',true,true);?>',
				null,
				function(xhr) {
					d.getElementById('bluepaid_ips').value = xhr.responseText;
				}
			);
			return false;
		}
		</script>
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
