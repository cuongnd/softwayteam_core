<?php
/**
 * @package    HikaShop for Joomla!
 * @version    2.6.3
 * @author    hikashop.com
 * @copyright    (C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$currencyHelper = hikashop_get('class.currency');
$currency_id = hikashop_getCurrency();
$payment_deposit=$this->payment->payment_params->payment_deposit;
$order_deposit_level_selected = $this->order->cart->order_deposit_level_selected;
$payment_deposit_amount = $this->method->payment_params->payment_deposit_amount;
$order_payment_deposit_value = $this->order->cart->order_payment_deposit_value;
$order_payment_deposit_value_format = $currencyHelper->format($order_payment_deposit_value,$currency_id);
$pay_else=$this->order->cart->order_full_price-$order_payment_deposit_value;
$pay_else_format = $currencyHelper->format($pay_else,$currency_id);
defined('_WOO_BOOKING_EXEC') or die('Restricted access');
?>
    <p><?php echo WoobookingText::_('ORDER_IS_COMPLETE') ?></p>
    <div class="hikashop_banktransfer_end" id="hikashop_banktransfer_end">
        <div class="hikashop_banktransfer_end_message" id="hikashop_banktransfer_end_message">
            <p><?php echo WoobookingText::sprintf('ORDER_CREATED', $this->order->order_number); ?></p>
            <?php if ($payment_deposit == 1 && $order_deposit_level_selected == "partial_payment") { ?>
                <p><?php echo WoobookingText::sprintf('PLEASE_TRANSFERT_MONEY_DEPOSIT', $order_payment_deposit_value_format); ?></p>
                <p><?php echo WoobookingText::sprintf('BANK_TRANSFER_CONTENT'); ?>:</p>
                <p><b><?php echo WoobookingText::sprintf('BANK_TRANSFER_FOR_ORDER_NUMBER', $this->order->order_number); ?></b></p>
                <p><?php echo $this->information ?></p>
                <p><b><?php echo WoobookingText::sprintf('THE_AMOUNT_YOU_OWE_WE_WILL_COLLECT_UPON_DELIVERY_TO_YOU', $pay_else_format); ?></b></p>
            <?php } else { ?>
                <p><?php echo WoobookingText::sprintf('PLEASE_TRANSFERT_MONEY', $this->amount); ?></p>
                <p><?php echo WoobookingText::sprintf('BANK_TRANSFER_CONTENT'); ?>:</p>
                <p><b><?php echo WoobookingText::sprintf('BANK_TRANSFER_FOR_ORDER_NUMBER', $this->order->order_number); ?></b></p>
                <p><?php echo $this->information ?></p>
            <?php } ?>
            <p><a target="_blank" href="/index.php?option=com_hikashop&ctrl=order&task=show_oder_number&cid[]=<?php echo $this->order->order_number ?>"><?php echo WoobookingText::_('Nhấn vào đây để xem chi tiết đơn hàng của bạn') ?></a></p>
            <p><b><?php echo WoobookingText::_('THANK_YOU_FOR_PURCHASE'); ?></b></p>
        </div>
    </div>
<?php
if (!empty($this->return_url)) {
    $doc = JFactory::getDocument();
    $doc->addScriptDeclaration("window.hikashop.ready(function(){window.location='" . $this->return_url . "'});");
}
?>
<p></p>
<p></p>
<p style="text-align: center"><a href="/index.php" class="btn btn-primary"><?php echo WoobookingText::_('CLICK_HERE_BACK_TO_HOME_PAGE') ?></a></p>
