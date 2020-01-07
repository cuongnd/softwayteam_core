<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_SOFT_WAY_EXEC') or die('Restricted access');
?><?php
if(!empty($this->createdCardRegister)) {
?>
<form action="<?php echo $this->createdCardRegister->CardRegistrationURL; ?>" method="post">
	<input type="hidden" name="data" value="<?php echo $this->createdCardRegister->PreregistrationData; ?>" />
	<input type="hidden" name="accessKeyRef" value="<?php echo $this->createdCardRegister->AccessKey; ?>" />
	<input type="hidden" name="returnURL" value="<?php echo $this->return_url; ?>" />

	<label for="cardNumber"><?php echo SoftWayText::_('CARD_NUMBER'); ?></label>
	<input type="text" autocomplete="off" name="cardNumber" value="" />
	<div class="clear"></div>

	<label for="cardExpirationDate"><?php echo SoftWayText::_('EXPIRATION_DATE'); ?></label>
	<input type="text" autocomplete="off" name="cardExpirationDate" value="" placeholder="MMYY"/>
	<div class="clear"></div>

	<label for="cardCvx"><?php echo SoftWayText::_('CVV'); ?></label>
	<input type="text" autocomplete="off" name="cardCvx" value="" />
	<div class="clear"></div>

	<input type="submit" value="Pay" />
</form>
<?php
}
