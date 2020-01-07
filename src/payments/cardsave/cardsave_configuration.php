<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.6.3
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_SOFT_WAY_EXEC') or die('Restricted access');
?><tr>
	<td class="key">
		<label for="params[payment_params][api]">
			<?php echo SoftWayText::_( 'API' ); ?>
		</label>
	</td>
	<td>
		<?php
		$values = array();
		$values[] = Html::_('select.option', 'direct',SoftWayText::_('Direct'));
		if (extension_loaded('soap')) {
			$values[] = Html::_('select.option', 'hosted',SoftWayText::_('Hosted'));
		} else {
			$values[] = Html::_('select.option', 'hosted',SoftWayText::_('Hosted (SOAP not present)'), 'value', 'text', true);
		}
		echo Html::_('select.genericlist',   $values, "params[payment_params][api]" , 'class="inputbox" size="1"', 'value', 'text', @$this->element->payment_params->api ); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][merchantid]">
			<?php echo SoftWayText::_( 'MERCHANT_NUMBER' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="params[payment_params][merchantid]" value="<?php echo @$this->element->payment_params->merchantid; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][password]">
			<?php echo SoftWayText::_( 'HIKA_PASSWORD' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="params[payment_params][password]" value="<?php echo @$this->element->payment_params->password; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][sharedkey]">
			<?php echo SoftWayText::_( 'SHARED_KEY' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="params[payment_params][sharedkey]" value="<?php echo @$this->element->payment_params->sharedkey; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][hash_method]">
			<?php echo SoftWayText::_( 'HASH_METHOD' ); ?>
		</label>
	</td>
	<td>
		<?php
		$values = array();
		if( function_exists('mhash') || function_exists('sha1') ) {
			$values[] = Html::_('select.option', 'sha1',SoftWayText::_('SHA1'));
			$values[] = Html::_('select.option', 'hmacsha1',SoftWayText::_('HMAC SHA1'));
		} else {
			$values[] = Html::_('select.option', 'sha1',SoftWayText::_('SHA1').' '.SoftWayText::_('not present'), 'value', 'text', true);
			$values[] = Html::_('select.option', 'hmacsha1',SoftWayText::_('HMAC SHA1').' '.SoftWayText::_('not present'), 'value', 'text', true);
		}

		if( function_exists('mhash') || function_exists('md5') ) {
			$values[] = Html::_('select.option', 'md5',SoftWayText::_('MD5'));
			$values[] = Html::_('select.option', 'hmacmd5',SoftWayText::_('HMAC MD5'));
		} else {
			$values[] = Html::_('select.option', 'md5',SoftWayText::_('MD5').' '.SoftWayText::_('not present'), 'value', 'text', true);
			$values[] = Html::_('select.option', 'hmacmd5',SoftWayText::_('HMAC MD5').' '.SoftWayText::_('not present'), 'value', 'text', true);
		}

		echo Html::_('select.genericlist',   $values, "params[payment_params][hash_method]" , 'class="inputbox" size="1"', 'value', 'text', @$this->element->payment_params->hash_method ); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][instant_capture]">
			<?php echo SoftWayText::_( 'INSTANTCAPTURE' ); ?>
		</label>
	</td>
	<td>
		<?php echo Html::_('select.booleanlist', "params[payment_params][instant_capture]" , '',@$this->element->payment_params->instant_capture	); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][ask_ccv]">
			<?php echo SoftWayText::_( 'CARD_VALIDATION_CODE' ); ?>
		</label>
	</td>
	<td>
		<?php echo Html::_('select.booleanlist', "params[payment_params][ask_ccv]" , '',@$this->element->payment_params->ask_ccv	); ?>
	</td>
</tr>
<!-- MANDATORY PART -->
<tr>
	<td class="key">
		<label for="params[payment_params][cv2mandatory]">
			<?php echo SoftWayText::_( 'CV2_MANDATORY' ); ?>
		</label>
	</td>
	<td>
		<?php echo Html::_('select.booleanlist', "params[payment_params][cv2mandatory]" , '',@$this->element->payment_params->cv2mandatory	); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][address1mandatory]">
			<?php echo SoftWayText::_( 'ADDRESS1_MANDATORY' ); ?>
		</label>
	</td>
	<td>
		<?php echo Html::_('select.booleanlist', "params[payment_params][address1mandatory]" , '',@$this->element->payment_params->address1mandatory	); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][citymandatory]">
			<?php echo SoftWayText::_( 'CITY_MANDATORY' ); ?>
		</label>
	</td>
	<td>
		<?php echo Html::_('select.booleanlist', "params[payment_params][citymandatory]" , '',@$this->element->payment_params->citymandatory	); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][postcodemandatory]">
			<?php echo SoftWayText::_( 'POSTCODE_MANDATORY' ); ?>
		</label>
	</td>
	<td>
		<?php echo Html::_('select.booleanlist', "params[payment_params][postcodemandatory]" , '',@$this->element->payment_params->postcodemandatory	); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][statemandatory]">
			<?php echo SoftWayText::_( 'STATE_MANDATORY' ); ?>
		</label>
	</td>
	<td>
		<?php echo Html::_('select.booleanlist', "params[payment_params][statemandatory]" , '',@$this->element->payment_params->statemandatory	); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][countrymandatory]">
			<?php echo SoftWayText::_( 'COUNTRY_MANDATORY' ); ?>
		</label>
	</td>
	<td>
		<?php echo Html::_('select.booleanlist', "params[payment_params][countrymandatory]" , '',@$this->element->payment_params->countrymandatory	); ?>
	</td>
</tr>
<!-- END OF MANDATORY PART -->

<tr>
	<td class="key">
		<label for="params[payment_params][gw_entrypoint]">
			<?php echo SoftWayText::_( 'GATEWAY_DOMAIN' ); ?>
		</label>
	</td>
	<td>
		https://gwX.<input type="text" name="params[payment_params][gw_entrypoint]" value="<?php echo @$this->element->payment_params->gw_entrypoint; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][gw_port]">
			<?php echo SoftWayText::_( 'GATEWAY_PORT' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="params[payment_params][gw_port]" value="<?php echo @$this->element->payment_params->gw_port; ?>" />
	</td>
</tr>

<tr>
	<td class="key">
		<label for="params[payment_params][debug]">
			<?php echo SoftWayText::_( 'DEBUG' ); ?>
		</label>
	</td>
	<td>
		<?php echo Html::_('select.booleanlist', "params[payment_params][debug]" , '',@$this->element->payment_params->debug	); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][cancel_url]">
			<?php echo SoftWayText::_( 'CANCEL_URL' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="params[payment_params][cancel_url]" value="<?php echo @$this->element->payment_params->cancel_url; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][return_url]">
			<?php echo SoftWayText::_( 'RETURN_URL' ); ?>
		</label>
	</td>
	<td>
		<input type="text" name="params[payment_params][return_url]" value="<?php echo @$this->element->payment_params->return_url; ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][invalid_status]">
			<?php echo SoftWayText::_( 'INVALID_STATUS' ); ?>
		</label>
	</td>
	<td>
		<?php echo $this->data['order_statuses']->display("params[payment_params][invalid_status]",@$this->element->payment_params->invalid_status); ?>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="params[payment_params][verified_status]">
			<?php echo SoftWayText::_( 'VERIFIED_STATUS' ); ?>
		</label>
	</td>
	<td>
		<?php echo $this->data['order_statuses']->display("params[payment_params][verified_status]",@$this->element->payment_params->verified_status); ?>
	</td>
</tr>
