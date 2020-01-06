<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_WOO_BOOKING_EXEC') or die('Restricted access');
?><tr>
	<td class="key">
		<label for="params[payment_params][email]"><?php
			echo WoobookingText::_('HIKA_EMAIL');
		?></label>
	</td>
	<td>
		<input type="text" name="params[payment_params][email]" value="<?php echo $this->escape(@$this->element->payment_params->email); ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][classical]"><?php
			echo WoobookingText::_('PAYPAL_CLASSICAL');
		?></label>
	</td>
	<td><?php
		if(!isset($this->element->payment_params->classical))
			$this->element->payment_params->classical = false;
		echo Html::_('select.booleanlist', "params[payment_params][classical]" , ' onchange="pp_adative_classical(this);"', $this->element->payment_params->classical);
	?>
<script type="text/javascript">
function pp_adative_classical(el) {
	var value = (el.value == "1"), elements = document.getElementsByTagName('tr');
	for (var i = 0; i < elements.length; i++) {
		if(elements[i].className == "pp_adative_opt")
			elements[i].style.display = (value ? 'none' : '');
	}
}
window.hikashop.ready(function(){
	var el = {value:<?php echo (int)$this->element->payment_params->classical; ?>};
	pp_adative_classical(el);
});
</script>
	</td>
</tr>
<tr class="pp_adative_opt">
	<td class="key">
		<label for="params[payment_params][username]"><?php
			echo WoobookingText::_('HIKA_USERNAME');
		?></label>
	</td>
	<td>
		<input type="text" name="params[payment_params][username]" value="<?php echo $this->escape(@$this->element->payment_params->username); ?>" />
	</td>
</tr>
<tr class="pp_adative_opt">
	<td class="key">
		<label for="params[payment_params][password]"><?php
			echo WoobookingText::_('HIKA_PASSWORD');
		?></label>
	</td>
	<td>
		<input type="text" name="params[payment_params][password]" value="<?php echo $this->escape(@$this->element->payment_params->password); ?>" />
	</td>
</tr>
<tr class="pp_adative_opt">
	<td class="key">
		<label for="params[payment_params][signature]"><?php
			echo WoobookingText::_('SIGNATURE');
		?></label>
	</td>
	<td>
		<input type="text" name="params[payment_params][signature]" value="<?php echo $this->escape(@$this->element->payment_params->signature); ?>" />
	</td>
</tr>
<tr class="pp_adative_opt">
	<td class="key">
		<label for="params[payment_params][applicationid]"><?php
			echo 'Application Id';
		?></label>
	</td>
	<td>
		<input type="text" name="params[payment_params][applicationid]" value="<?php echo $this->escape(@$this->element->payment_params->applicationid); ?>" />
	</td>
</tr>
<tr class="pp_adative_opt">
	<td class="key">
		<label for="params[payment_params][reverse_all_on_error]"><?php
			echo 'Reverse all on error';
		?></label>
	</td>
	<td><?php
		echo Html::_('select.booleanlist', "params[payment_params][reverse_all_on_error]" , '', @$this->element->payment_params->reverse_all_on_error);
	?></td>
</tr>
<tr class="pp_adative_opt">
	<td class="key">
		<label for="params[payment_params][payment_mode]"><?php
			echo 'Payment mode';
		?></label>
	</td>
	<td><?php
		$arr = array(
			Html::_('select.option', 'chained', 'Chained'),
			Html::_('select.option', 'parallel', 'Parallel'),
		);
		echo Html::_('select.genericlist',  $arr, "params[payment_params][payment_mode]", '', 'value', 'text', @$this->element->payment_params->payment_mode);
	?></td>
</tr>
<tr class="pp_adative_opt">
	<td class="key">
		<label for="params[payment_params][display_mode]"><?php
			echo 'Display mode';
		?></label>
	</td>
	<td><?php
		$arr = array(
			Html::_('select.option', 'redirect', 'Redirect'),
			Html::_('select.option', 'popup', 'Popup'),
		);
		echo Html::_('select.genericlist',  $arr, "params[payment_params][display_mode]", '', 'value', 'text', @$this->element->payment_params->display_mode);
	?></td>
</tr>
<tr class="pp_adative_opt">
	<td class="key">
		<label for="params[payment_params][fee_mode]"><?php
			echo 'Fee mode';
		?></label>
	</td>
	<td><?php
		$arr = array(
			Html::_('select.option', 'each', 'Each Receiver'),
			Html::_('select.option', 'sender', 'Sender'),
			Html::_('select.option', 'primary', 'Primary Receiver'),
			Html::_('select.option', 'secondary', 'Secondary Receiver(s)'),
		);
		echo Html::_('select.genericlist',  $arr, "params[payment_params][fee_mode]", '', 'value', 'text', @$this->element->payment_params->fee_mode);
	?></td>
</tr>
<tr class="pp_adative_opt">
	<td class="key">
		<label for="params[payment_params][store_secondary]"><?php
			echo 'Put store as a secondary receiver';
		?></label>
	</td>
	<td>
		<?php echo Html::_('select.booleanlist', "params[payment_params][store_secondary]" , '', @$this->element->payment_params->store_secondary); ?>
		<p>
			<em><strong>Important</strong>: This option is not recommended.<br/>
			It won't work correctly if you have several vendors in a single order.</em>
		</p>
	</td>
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
		<label for="params[payment_params][sandbox]"><?php
			echo WoobookingText::_('SANDBOX');
		?></label>
	</td>
	<td><?php
		if(!isset($this->element->payment_params->sandbox) && isset($this->element->payment_params->debug))
			$this->element->payment_params->sandbox = $this->element->payment_params->debug;
		echo Html::_('select.booleanlist', "params[payment_params][sandbox]" , '', @$this->element->payment_params->sandbox);
	?></td>
</tr>
<tr class="pp_adative_opt">
	<td class="key">
		<label for="params[payment_params][notify_wrong_emails]"><?php
			echo 'Notify for wrong emails';
		?></label>
	</td>
	<td><?php
		echo Html::_('select.booleanlist', "params[payment_params][notify_wrong_emails]" , '', @$this->element->payment_params->notify_wrong_emails);
	?></td>
</tr>
<tr class="pp_adative_opt">
	<td class="key">
		<label for="params[payment_params][use_fsock]"><?php
			echo 'Use Raw sockets instead of cURL';
		?></label>
	</td>
	<td><?php
		echo Html::_('select.booleanlist', "params[payment_params][use_fsock]" , '', @$this->element->payment_params->use_fsock);
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
