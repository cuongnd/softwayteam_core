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
		<label for="params[payment_params][secret_code]"><?php
			echo WoobookingText::_( 'REQUIRED' );
		?></label>
	</td>
	<td>
		<?php echo $this->data['secret_code']; ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][vendor]"><?php
			echo WoobookingText::_( 'ATOS_MERCHANT_ID' );
		?></label>
	</td>
	<td>
		<input type="text" name="params[payment_params][vendor]" value="<?php echo $this->escape(@$this->element->payment_params->vendor); ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][user]"><?php
			echo WoobookingText::_( 'HIKA_USERNAME' );
		?></label>
	</td>
	<td>
		<input type="text" name="params[payment_params][user]" value="<?php echo $this->escape(@$this->element->payment_params->user); ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][password]"><?php
			echo WoobookingText::_( 'HIKA_PASSWORD' );
		?></label>
	</td>
	<td>
		<input type="text" name="params[payment_params][password]" value="<?php echo $this->escape(@$this->element->payment_params->password); ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][partner]"><?php
			echo WoobookingText::_( 'PARTNER' );
		?></label>
	</td>
	<td>
		<input type="text" name="params[payment_params][partner]" value="<?php echo $this->escape(@$this->element->payment_params->partner); ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][validation]"><?php
			echo WoobookingText::_('ENABLE_VALIDATION');
		?></label>
	</td>
	<td><?php
		echo Html::_('select.booleanlist', "params[payment_params][validation]" , '', @$this->element->payment_params->validation);
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
		<label for="params[payment_params][cancel_url]"><?php
			echo WoobookingText::_( 'CANCEL_URL' );
		?></label>
	</td>
	<td>
		<input type="text" name="params[payment_params][cancel_url]" value="<?php echo $this->escape(@$this->element->payment_params->cancel_url); ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][return_url]"><?php
			echo WoobookingText::_( 'RETURN_URL' );
		?></label>
	</td>
	<td>
		<input type="text" name="params[payment_params][return_url]" value="<?php echo $this->escape(@$this->element->payment_params->return_url); ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][type]"><?php
			echo WoobookingText::_( 'HIKA_TYPE' );
		?></label>
	</td>
	<td><?php
		$arr = array(
			Html::_('select.option', 'iframe', 'Iframe' ),
			Html::_('select.option', 'form', 'Form' ),
		);
		echo Html::_('select.genericlist',  $arr, "params[payment_params][type]", 'onchange="iframe_size_hide()"', 'value', 'text', @$this->element->payment_params->type);
		?><br/>
		<div id="height">
				<label for="params[payment_params][height]"><?php
					echo WoobookingText::_( 'HEIGHT' );
				?></label>
				<input type="text"  name="params[payment_params][height]" value="<?php echo $this->escape(@$this->element->payment_params->height); ?>" />
		</div>
		<div id="width">
				<label for="params[payment_params][width]"><?php
					echo WoobookingText::_( 'PRODUCT_WIDTH' );
				?></label>
				<input type="text"  name="params[payment_params][width]" value="<?php echo $this->escape(@$this->element->payment_params->width); ?>" />
		</div>
		<script type="text/javascript">
		function iframe_size_hide() {
			var e = document.getElementById('datapaymentpayment_paramstype');
			var val = e.options[e.selectedIndex].text;
			if(val.localeCompare("Form")== 0){
				var i = document.getElementById("height").style.display = 'none';
				var i = document.getElementById("width").style.display = 'none';
			}else if(val.localeCompare("Iframe")== 0){
				var i = document.getElementById("height").style.display = '';
				var i = document.getElementById("width").style.display = '';
			}
			return true;
		}
		window.hikashop.ready(function(){iframe_size_hide();});
		</script>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][test_mode]"><?php
			echo 'TEST_MODE';
		?></label>
	</td>
	<td><?php
		if(!isset($this->element->payment_params->test_mode))
			$this->element->payment_params->test_mode = 1;
		echo Html::_('select.booleanlist', "params[payment_params][test_mode]" , '', $this->element->payment_params->test_mode);
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
