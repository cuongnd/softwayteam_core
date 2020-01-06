<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.6.3
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_SOFT_WAY_EXEC') or die('Restricted access');
?><div class="hikashop_alipay_end" id="hikashop_alipay_end">
	<span id="hikashop_alipay_end_message" class="hikashop_alipay_end_message">
		<?php echo WoobookingText::sprintf('PLEASE_WAIT_BEFORE_REDIRECTION_TO_X',$this->payment_name).'<br/>'. WoobookingText::_('CLICK_ON_BUTTON_IF_NOT_REDIRECTED');?>
	</span>
	<span id="hikashop_alipay_end_spinner" class="hikashop_alipay_end_spinner">
		<img src="<?php echo HIKASHOP_IMAGES.'spinner.gif';?>" />
	</span>
	<br/>
	<form id="hikashop_alipay_form" name="hikashop_alipay_form" action="<?php echo $this->payment_params->url; ?>" method="POST">
		<div id="hikashop_alipay_end_image" class="hikashop_alipay_end_image">
			<input id="hikashop_alipay_button" type="submit" class="btn btn-primary" value="<?php echo WoobookingText::_('PAY_NOW');?>" name="" alt="<?php echo WoobookingText::_('PAY_NOW');?>" />
		</div>
		<?php
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration("window.hikashop.ready( function() {document.getElementById('hikashop_alipay_form').submit();});");
			JRequest::setVar('noform',1);
		?>
	</form>
</div>
