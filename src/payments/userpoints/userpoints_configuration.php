<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.6.3
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_WOO_BOOKING_EXEC') or die('Restricted access');
?>		<tr>
			<td class="key">
				<label for="params[payment_params][points_mode]"><?php
					echo WoobookingText::_('POINTS_MODE');
				?></label>
			</td>
			<td><?php
				echo Html::_('select.genericlist', $this->data['modes'], "params[payment_params][points_mode]", '', 'value', 'text', @$this->element->payment_params->points_mode);
			?></td>
		</tr>
		<tr>
			<td class="key">
				<label for="params[payment_params][value]"><?php
					echo WoobookingText::sprintf('RATES', $this->element->payment_name);
				?></label>
			</td>
			<td>
				<?php echo '1 '.WoobookingText::sprintf( 'POINTS' ).' '.WoobookingText::sprintf( 'EQUALS', $this->element->payment_name); ?>
				<input style="width: 50px;" type="text" name="params[payment_params][value]" value="<?php echo @$this->element->payment_params->value; ?>" />
				<?php  echo $this->data['currency']->currency_code. ' ' .$this->data['currency']->currency_symbol; ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="params[payment_params][tax_id]"><?php
					echo WoobookingText::_('TAXATION_CATEGORY');
				?></label>
			</td>
			<td><?php
				echo $this->categoryType->display('params[payment_params][tax_id]', @$this->element->payment_params->tax_id, true);
			?></td>
		</tr>
		<tr>
			<td class="key">
				<label for="params[payment_params][percent]"><?php
					echo WoobookingText::sprintf('GROUP_POINTS_BY', $this->element->payment_name);
				?></label>
			</td>
			<td>
				<input style="width: 50px;" type="text" name="params[payment_params][grouppoints]" value="<?php echo @$this->element->payment_params->grouppoints; ?>" /> <?php echo WoobookingText::sprintf( 'POINTS' );?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="params[payment_params][percent]"><?php
					echo WoobookingText::sprintf('MAXIMUM_POINTS', $this->element->payment_name);
				?></label>
			</td>
			<td>
				<input style="width: 50px;" type="text" name="params[payment_params][maxpoints]" value="<?php echo @$this->element->payment_params->maxpoints; ?>" /> <?php echo WoobookingText::sprintf( 'POINTS' );?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="params[payment_params][rounding]"><?php
					echo WoobookingText::_('POINTS_ROUDING');
				?></label>
			</td>
			<td>
				<input style="width: 50px;" type="text" name="params[payment_params][rounding]" value="<?php echo @$this->element->payment_params->rounding; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="params[payment_params][allowshipping]"><?php
					echo WoobookingText::sprintf('SHIPPING', $this->element->payment_name);
				?></label>
			</td>
			<td><?php
				echo Html::_('select.booleanlist', "params[payment_params][allowshipping]" , '',@$this->element->payment_params->allowshipping);
			?></td>
		</tr>
		<tr>
			<td class="key">
				<label for="params[payment_params][partialpayment]"><?php
					echo WoobookingText::sprintf('ALLOW_PARTIAL_PAYMENT', $this->element->payment_name);
				?></label>
			</td>
			<td><?php
				echo Html::_('select.booleanlist', "params[payment_params][partialpayment]" , 'onclick="setVisible(this.value);"',@$this->element->payment_params->partialpayment	);
			?></td>
		</tr>
<?php
$display = '';
if(empty($this->element->payment_params->partialpayment)){
	$display = ' style="display:none;"';
}
?>
		<tr>
			<td class="key">
				<div id="opt"<?php echo $display?>>
					<label for="params[payment_params][percentmax]"><?php
						echo WoobookingText::sprintf('MAXIMUM_ORDER_PERCENT', $this->element->payment_name);
					?></label>
				</div>
			</td>
			<td>
				<div id="opt2"<?php echo $display?>>
					<input style="width: 50px;" type="text" name="params[payment_params][percentmax]" value="<?php echo @$this->element->payment_params->percentmax; ?>" />%
				</div>
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="params[payment_params][percent]"><?php
					echo WoobookingText::sprintf('MINIMUM_ORDER_PERCENT', $this->element->payment_name);
				?></label>
			</td>
			<td>
				<input style="width: 50px;" type="text" name="params[payment_params][percent]" value="<?php echo @$this->element->payment_params->percent; ?>" />%
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="params[payment_params][minimumcost]"><?php
					echo WoobookingText::_('MINIMUM_COST');
				?></label>
			</td>
			<td>
				<div id="opt2" style="display:block;">
					<input style="width: 50px;" type="text" name="params[payment_params][minimumcost]" value="<?php echo @$this->element->payment_params->minimumcost; ?>" />
					<?php  echo $this->data['currency']->currency_code. ' ' .$this->data['currency']->currency_symbol; ?>
				</div>
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="params[payment_params][givebackpoints]"><?php
					echo WoobookingText::sprintf('GIVE_BACK_POINTS_IF_CANCELLED', $this->element->payment_name);
				?></label>
			</td>
			<td>
				<?php echo Html::_('select.booleanlist', "params[payment_params][givebackpoints]" , '',@$this->element->payment_params->givebackpoints ); ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="params[payment_params][virtual_coupon]"><?php
					echo WoobookingText::sprintf('USE_VIRTUAL_COUPON', $this->element->payment_name);
				?></label>
			</td>
			<td><?php
				echo Html::_('select.booleanlist', "params[payment_params][virtual_coupon]" , '',@$this->element->payment_params->virtual_coupon );
			?></td>
		</tr>
		<tr>
			<td class="key">
				<label for="params[payment_params][grouppoints_warning_lvl]"><?php
					echo WoobookingText::sprintf('GROUP_POINTS_WARNING_LEVEL', $this->element->payment_name);
				?></label>
			</td>
			<td>
				<input style="width: 50px;" type="text" name="params[payment_params][grouppoints_warning_lvl]" value="<?php echo @$this->element->payment_params->grouppoints_warning_lvl; ?>" /> <?php echo WoobookingText::sprintf( 'POINTS' );?>
			</td>
		</tr>
