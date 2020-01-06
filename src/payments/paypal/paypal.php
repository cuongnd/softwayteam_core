<?php
/**
 * @package    HikaShop for Joomla!
 * @version    2.6.3
 * @author    hikashop.com
 * @copyright    (C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_WOO_BOOKING_EXEC') or die('Restricted access');
?><?php

class WBPaymentPaypal extends WBPayment
{
    var $accepted_currencies = array(
        'AUD', 'BRL', 'CAD', 'EUR', 'GBP', 'JPY', 'USD', 'NZD', 'CHF', 'HKD', 'SGD', 'SEK',
        'DKK', 'PLN', 'NOK', 'HUF', 'CZK', 'MXN', 'MYR', 'PHP', 'TWD', 'THB', 'ILS', 'TRY',
        'RUB'
    );

    var $multiple = true;
    var $debug = false;
    var $name = 'paypal';
    var $doc_form = 'paypal';
    /**
     * @var array
     */
    public $vars;

    function __construct($config)
    {
        parent::__construct($config);
    }

    function onBeforeOrderCreate(&$order, &$do)
    {
        if (parent::onBeforeOrderCreate($order, $do) === true)
            return true;

        if ((empty($this->payment_params->email) || empty($this->payment_params->url)) && $this->plugin_data->payment_id == $order->order_payment_id) {
            $this->app->enqueueMessage('Please check your &quot;PayPal&quot; plugin configuration');
            $do = false;
        }
    }

    function onAfterOrderConfirm(&$order,&$methods=null, $method_id=0)
    {

        if ($this->currency->currency_locale['int_frac_digits'] > 2)
            $this->currency->currency_locale['int_frac_digits'] = 2;
        $app = Factory::getApplication();
        $notify_url = woobooking_controller::getFrontendLink("payment.notify","order_id=".$order->id);
        $return_url = woobooking_controller::getFrontendLink("payment.return","order_id=".$order->id);
        $cancel_url = woobooking_controller::getFrontendLink("payment.cancel","order_id=".$order->id);

        $tax_total = '';
        $discount_total = '';
        $debug = @$this->payment_params->debug;
        if (!isset($this->payment_params->no_shipping))
            $this->payment_params->no_shipping = 1;
        if (!empty($this->payment_params->rm))
            $this->payment_params->rm = 2;

        $vars = array(
            'cmd' => '_ext-enter',
            'redirect_cmd' => '_cart',
            'upload' => '1',
            'business' => $this->payment_params->email,
            'receiver_email' => $this->payment_params->email,
            'invoice' => $order->order_id,
            'currency_code' => $this->currency->currency_code,
            'return' => $return_url,
            'notify_url' => $notify_url,
            'cancel_return' => $cancel_url,
            'undefined_quantity' => '0',
            'test_ipn' => $debug,
            'shipping' => '0',
            'no_shipping' => $this->payment_params->no_shipping,
            'no_note' => !@$this->payment_params->notes,
            'charset' => 'utf-8',
            'rm' => (int)@$this->payment_params->rm,
            'bn' => 'HikariSoftware_Cart_WPS'
        );

        $list_order_detail=$order->list_order_detail;
        $i=1;
        foreach ($list_order_detail as $order_detail){
            $vars["item_name_$i"]=$order_detail->id;
            $vars["amount_$i"]=$order_detail->total;
            $vars["quantity_$i"]=$order_detail->quantity;
        }

        if (!empty($this->payment_params->cpp_header_image)) {
            $vars['cpp_header_image'] = $this->payment_params->cpp_header_image;
        }
        if (empty($this->payment_params->url))
            $this->payment_params->url = 'https://www.paypal.com/cgi-bin/webscr';
        $debug=$this->debug;
        if($debug==1){
            $this->payment_params->url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        }

        $vars['item_name']=$order->id;
        $vars['amount']=$order->total;
        $vars['currency_code']="USD";
        $this->vars = $vars;

        return $this->showPage('end');
    }

    function onPaymentNotification(&$statuses)
    {

        $vars = array();
        $data = array();
        $filter = JFilterInput::getInstance();
        foreach ($_REQUEST as $key => $value) {
            $key = $filter->clean($key);
            if (preg_match('#^[0-9a-z_-]{1,30}$#i', $key) && !preg_match('#^cmd$#i', $key)) {
                $value = JRequest::getString($key);
                $vars[$key] = $value;
                $data[] = $key . '=' . urlencode($value);
            }
        }
        $data = implode('&', $data) . '&cmd=_notify-validate';

        $dbOrder = $this->getOrder((int)@$vars['invoice']);
        $this->loadPaymentParams($dbOrder);
        if (empty($this->payment_params))
            return false;
        $this->loadOrderData($dbOrder);

        if (!$this->payment_params->notification)
            return false;

        if ($this->payment_params->debug)
            echo print_r($vars, true) . "\r\n\r\n";

        if (empty($dbOrder)) {
            echo 'Could not load any order for your notification ' . @$vars['invoice'];
            return false;
        }

        if ($this->payment_params->debug) {
            echo print_r($dbOrder, true) . "\r\n\r\n";
        }

        $order_id = $dbOrder->order_id;

        $url = HIKASHOP_LIVE . 'administrator/index.php?option=com_hikashop&ctrl=order&task=edit&order_id=' . $order_id;
        $order_text = "\r\n" . WoobookingText::sprintf('NOTIFICATION_OF_ORDER_ON_WEBSITE', $dbOrder->order_number, HIKASHOP_LIVE);
        $order_text .= "\r\n" . str_replace('<br/>', "\r\n", WoobookingText::sprintf('ACCESS_ORDER_WITH_LINK', $url));

        if (!empty($this->payment_params->ips)) {
            $ip = hikashop_getIP();
            $ips = str_replace(array('.', '*', ','), array('\.', '[0-9]+', '|'), $this->payment_params->ips);
            if (!preg_match('#(' . implode('|', $ips) . ')#', $ip)) {
                $email = new stdClass();
                $email->subject = WoobookingText::sprintf('NOTIFICATION_REFUSED_FOR_THE_ORDER', 'Paypal') . ' ' . WoobookingText::sprintf('IP_NOT_VALID', $dbOrder->order_number);
                $email->body = str_replace('<br/>', "\r\n", WoobookingText::sprintf('NOTIFICATION_REFUSED_FROM_IP', 'Paypal', $ip, implode("\r\n", $this->payment_params->ips))) . "\r\n\r\n" . WoobookingText::sprintf('CHECK_DOCUMENTATION', HIKASHOP_HELPURL . 'payment-paypal-error#ip') . $order_text;
                $action = false;
                $this->modifyOrder($action, null, null, $email);

                JError::raiseError(403, WoobookingText::_('Access Forbidden'));
                return false;
            }
        }

        if (empty($this->payment_params->url))
            $this->payment_params->url = 'https://www.paypal.com/cgi-bin/webscr';
        $url = parse_url($this->payment_params->url);
        if (!isset($url['query']))
            $url['query'] = '';

        if (!isset($url['port'])) {
            if (!empty($url['scheme']) && in_array($url['scheme'], array('https', 'ssl'))) {
                $url['port'] = 443;
            } else {
                $url['port'] = 80;
            }
        }

        if (!empty($url['scheme']) && in_array($url['scheme'], array('https', 'ssl'))) {
            $url['host_socket'] = 'ssl://' . $url['host'];
        } else {
            $url['host_socket'] = $url['host'];
        }

        if ($this->payment_params->debug)
            echo print_r($url, true) . "\r\n\r\n";

        $fp = fsockopen($url['host_socket'], $url['port'], $errno, $errstr, 30);
        if (!$fp) {
            $email = new stdClass();
            $email->subject = WoobookingText::sprintf('NOTIFICATION_REFUSED_FOR_THE_ORDER', 'Paypal') . ' ' . WoobookingText::sprintf('PAYPAL_CONNECTION_FAILED', $dbOrder->order_number);
            $email->body = str_replace('<br/>', "\r\n", WoobookingText::sprintf('NOTIFICATION_REFUSED_NO_CONNECTION', 'Paypal')) . "\r\n\r\n" . WoobookingText::sprintf('CHECK_DOCUMENTATION', HIKASHOP_HELPURL . 'payment-paypal-error#connection') . $order_text;
            $action = false;
            $this->modifyOrder($action, null, null, $email);

            JError::raiseError(403, WoobookingText::_('Access Forbidden'));
            return false;
        }

        $uri = $url['path'] . ($url['query'] != '' ? '?' . $url['query'] : '');
        $header = 'POST ' . $uri . ' HTTP/1.1' . "\r\n" .
            'User-Agent: PHP/' . phpversion() . "\r\n" .
            'Referer: ' . hikashop_currentURL() . "\r\n" .
            'Server: ' . $_SERVER['SERVER_SOFTWARE'] . "\r\n" .
            'Host: ' . $url['host'] . "\r\n" .
            'Content-Type: application/x-www-form-urlencoded' . "\r\n" .
            'Content-Length: ' . strlen($data) . "\r\n" .
            'Accept: */' . '*' . "\r\n" .
            'Connection: close' . "\r\n\r\n";

        fwrite($fp, $header . $data);
        $response = '';
        while (!feof($fp)) {
            $response .= fgets($fp, 1024);
        }
        fclose($fp);

        if ($this->payment_params->debug) {
            echo print_r($header, true) . "\r\n\r\n";
            echo print_r($data, true) . "\r\n\r\n";
            echo print_r($response, true) . "\r\n\r\n";
        }

        $response = substr($response, strpos($response, "\r\n\r\n") + strlen("\r\n\r\n"));

        $verified = preg_match('#VERIFIED#i', $response);
        if (!$verified) {
            $email = new stdClass();
            if (preg_match('#INVALID#i', $response)) {
                $email->subject = WoobookingText::sprintf('NOTIFICATION_REFUSED_FOR_THE_ORDER', 'Paypal') . 'invalid transaction';
                $email->body = WoobookingText::sprintf("Hello,\r\n A paypal notification was refused because it could not be verified by the paypal server") . "\r\n\r\n" . WoobookingText::sprintf('CHECK_DOCUMENTATION', HIKASHOP_HELPURL . 'payment-paypal-error#invalidtnx') . $order_text;
                if ($this->payment_params->debug)
                    echo 'invalid transaction' . "\n\n\n";
            } else {
                $email->subject = WoobookingText::sprintf('NOTIFICATION_REFUSED_FOR_THE_ORDER', 'Paypal') . 'invalid response';
                $email->body = WoobookingText::sprintf("Hello,\r\n A paypal notification was refused because the response from the paypal server was invalid") . "\r\n\r\n" . WoobookingText::sprintf('CHECK_DOCUMENTATION', HIKASHOP_HELPURL . 'payment-paypal-error#invalidresponse') . $order_text;

                if ($this->payment_params->debug)
                    echo 'invalid response' . "\n\n\n";
            }
            $action = false;
            $this->modifyOrder($action, null, null, $email);
            return false;
        }

        $completed = preg_match('#Completed#i', $vars['payment_status']);
        $pending = preg_match('#Pending#i', $vars['payment_status']);
        if (!$completed && !$pending) {
            $email = new stdClass();
            $email->subject = WoobookingText::sprintf('PAYMENT_NOTIFICATION_FOR_ORDER', 'Paypal', $vars['payment_status'], $dbOrder->order_number);
            $email->body = str_replace('<br/>', "\r\n", WoobookingText::sprintf('PAYMENT_NOTIFICATION_STATUS', 'Paypal', $vars['payment_status'])) . ' ' . WoobookingText::_('STATUS_NOT_CHANGED') . "\r\n\r\n" . WoobookingText::sprintf('CHECK_DOCUMENTATION', HIKASHOP_HELPURL . 'payment-paypal-error#status') . $order_text;
            $action = false;
            $this->modifyOrder($action, null, null, $email);

            if ($this->payment_params->debug)
                echo 'payment ' . $vars['payment_status'] . "\r\n\r\n";
            return false;
        }

        echo 'PayPal transaction id: ' . $vars['txn_id'] . "\r\n\r\n";

        $history = new stdClass();
        $history->notified = 0;
        $history->amount = @$vars['mc_gross'] . @$vars['mc_currency'];
        $history->data = ob_get_clean();

        $price_check = round($dbOrder->order_full_price, (int)$this->currency->currency_locale['int_frac_digits']);
        if ($price_check != @$vars['mc_gross'] || $this->currency->currency_code != @$vars['mc_currency']) {
            $email = new stdClass();
            $email->subject = WoobookingText::sprintf('NOTIFICATION_REFUSED_FOR_THE_ORDER', 'Paypal') . WoobookingText::_('INVALID_AMOUNT');
            $email->body = str_replace('<br/>', "\r\n", WoobookingText::sprintf('AMOUNT_RECEIVED_DIFFERENT_FROM_ORDER', 'Paypal', $history->amount, $price_check . $this->currency->currency_code)) . "\r\n\r\n" . WoobookingText::sprintf('CHECK_DOCUMENTATION', HIKASHOP_HELPURL . 'payment-paypal-error#amount') . $order_text;

            $this->modifyOrder($order_id, $this->payment_params->invalid_status, $history, $email);
            return false;
        }
        if (strtolower(@$vars['receiver_email']) != strtolower($this->payment_params->email) && strtolower(@$vars['business']) != strtolower($this->payment_params->email)) {
            $email = new stdClass();
            $email->subject = WoobookingText::sprintf('NOTIFICATION_REFUSED_FOR_THE_ORDER', 'Paypal') . 'wrong receiver';
            $email->body = str_replace('<br/>', "\r\n", 'The money was sent to the wrong PayPal account, likely due to the customer trying to cheat.' . "\r\n" .
                'Notification receiver: ' . @$vars['receiver_email'] . "\r\n" .
                'Notification business: ' . @$vars['business'] . "\r\n" .
                'Your paypal address: ' . $this->payment_params->email . "\r\n" .
                $order_text);

            $this->modifyOrder($order_id, $this->payment_params->invalid_status, $history, $email);
            return false;
        }

        if ($completed) {
            $order_status = $this->payment_params->verified_status;
        } else {
            $order_status = $this->payment_params->pending_status;
            $order_text = WoobookingText::sprintf('CHECK_DOCUMENTATION', HIKASHOP_HELPURL . 'payment-paypal-error#pending') . "\r\n\r\n" . $order_text;
        }
        if ($dbOrder->order_status == $order_status)
            return true;

        $config =& hikashop_config();
        if ($config->get('order_confirmed_status', 'confirmed') == $order_status) {
            $history->notified = 1;
        }

        $email = new stdClass();
        $email->subject = WoobookingText::sprintf('PAYMENT_NOTIFICATION_FOR_ORDER', 'Paypal', $vars['payment_status'], $dbOrder->order_number);
        $email->body = str_replace('<br/>', "\r\n", WoobookingText::sprintf('PAYMENT_NOTIFICATION_STATUS', 'Paypal', $vars['payment_status'])) . ' ' . WoobookingText::sprintf('ORDER_STATUS_CHANGED', $order_status) . "\r\n\r\n" . $order_text;

        $this->modifyOrder($order_id, $order_status, $history, $email);
        return true;
    }

    function onPaymentConfiguration(&$element)
    {
        $subtask = JRequest::getCmd('subtask', '');
        if ($subtask == 'ips') {
            $ips = null;
            echo implode(',', $this->_getIPList($ips));
            exit;
        }

        parent::onPaymentConfiguration($element);
        $this->address = hikashop_get('type.address');

        if (empty($element->payment_params->email)) {
            $app = JFactory::getApplication();
            $lang = JFactory::getLanguage();
            $locale = strtolower(substr($lang->get('tag'), 0, 2));
            $app->enqueueMessage(WoobookingText::sprintf('ENTER_INFO_REGISTER_IF_NEEDED', 'PayPal', WoobookingText::_('HIKA_EMAIL'), 'PayPal', 'https://www.paypal.com/' . $locale . '/mrb/pal=SXL9FKNKGAEM8'));
        }
    }

    function onPaymentConfigurationSave(&$element)
    {
        if (!empty($element->payment_params->ips))
            $element->payment_params->ips = explode(',', $element->payment_params->ips);

        if (strpos($element->payment_params->url, 'https://') === false) {
            $app = JFactory::getApplication();
            $app->enqueueMessage('The URL must start with https://');
            return false;
        }

        return true;
    }

    function getPaymentDefaultValues(&$element)
    {
        $element->payment_name = 'PayPal';
        $element->payment_description = 'You can pay by credit card or paypal using this payment method';
        $element->payment_images = 'MasterCard,VISA,Credit_card,PayPal';

        $element->payment_params->url = 'https://www.paypal.com/cgi-bin/webscr';
        $element->payment_params->notification = 1;
        $element->payment_params->ips = '';
        $element->payment_params->details = 0;
        $element->payment_params->invalid_status = 'cancelled';
        $element->payment_params->pending_status = 'created';
        $element->payment_params->verified_status = 'confirmed';
        $element->payment_params->address_override = 1;
    }

    function _getIPList(&$ipList)
    {
        $hosts = array(
            'www.paypal.com',
            'notify.paypal.com',
            'ipn.sandbox.paypal.com'
        );

        $ipList = array();
        foreach ($hosts as $host) {
            $ips = gethostbynamel($host);
            if (!empty($ips)) {
                if (empty($ipList))
                    $ipList = $ips;
                else
                    $ipList = array_merge($ipList, $ips);
            }
        }

        if (empty($ipList))
            return $ipList;

        $newList = array();
        foreach ($ipList as $k => $ip) {
            $ipParts = explode('.', $ip);
            if (count($ipParts) == 4) {
                array_pop($ipParts);
                $ip = implode('.', $ipParts) . '.*';
            }
            if (!in_array($ip, $newList))
                $newList[] = $ip;
        }
        return $newList;
    }
}
