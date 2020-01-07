<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.6.3
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_SOFT_WAY_EXEC') or die('Restricted access');
?><div class="hikashop_check_end" id="hikashop_check_end">
	<span class="hikashop_check_end_message" id="hikashop_check_end_message">
		<?php
		echo SoftWayText::_('ORDER_IS_COMPLETE').'<br/>'.
		SoftWayText::sprintf('PLEASE_SEND_CHECK',$this->amount).'<br/>'.
		$this->information.'<br/>'.
		SoftWayText::sprintf('INCLUDE_ORDER_NUMBER_TO_CHECK',$this->order_number).'<br/>'.
		SoftWayText::_('THANK_YOU_FOR_PURCHASE');?>
	</span>
</div>
<?php
if(!empty($this->payment_params->return_url)){
	$doc = JFactory::getDocument();
	$doc->addScriptDeclaration("window.hikashop.ready( function() {window.location='".$this->payment_params->return_url."'});");
}
