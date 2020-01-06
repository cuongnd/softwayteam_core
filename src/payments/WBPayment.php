<?php

use WooBooking\CMS\Utilities\Utility;

class WBPayment
{
    private static $instance;
    var $type = 'payment';
    var $debug = false;
    var $object_payment = null;
    var $accepted_currencies = array();
    var $doc_form = 'generic';
    var $payment_params;
    var $features = array(
        'authorize_capture' => false,
        'recurring' => false,
        'refund' => false
    );
    protected $currency;
    /**
     * @var int
     */
    private $currency_id;

    function __construct($config) {
        $this->payment_params=$config->params->get('payment_params');
        $this->object_payment=$config;
        $this->debug=$config->debug;
    }
    public static function getInstance($type,$config){
        $app=Factory::getApplication();
        if (is_object(self::$instance[$type]))
        {
            return self::$instance[$type];
        }
        
        $file_payment_path=__DIR__."/$type/".$type.".php";

        $file_short_payment_path=Utility::get_short_file_by_path($file_payment_path);
        $response=new stdClass();
        if(file_exists($file_payment_path)){
            require_once $file_payment_path;
            $class_name="WBPayment".ucfirst($type);
            if(!class_exists($class_name)){
                $app->enqueueMessage("file $file_short_payment_path must has class $class_name, please check it");
            }else{
                self::$instance[$type]=new $class_name($config);
            }
        }else{
            $app->enqueueMessage("cannot find file controller $file_short_payment_path not exists, please create it first");
        }

        return self::$instance[$type];
    }
    function onPaymentDisplay(&$order, &$methods, &$usable_methods)
    {
        $app = JFactory::getApplication();
        if (empty($methods) || empty($this->name))
            return true;
        $currencyClass = hikashop_get('class.currency');
        if (!empty($order->total)) {
            $null = null;
            $currency_id = intval(@$order->total->prices[0]->price_currency_id);
            $currency = $currencyClass->getCurrencies($currency_id, $null);
            if (!empty($currency) && !empty($this->accepted_currencies) && !in_array(@$currency[$currency_id]->currency_code, $this->accepted_currencies))
                return true;
            $this->currency = $currency;
            $this->currency_id = $currency_id;
        }
        $this->currencyClass = $currencyClass;
        $shippingClass = hikashop_get('class.shipping');
        $volumeHelper = hikashop_get('helper.volume');
        $weightHelper = hikashop_get('helper.weight');
        $zone_id = hikashop_getZone('payment');
        foreach ($methods as $method) {
            if ($method->payment_type != $this->name || !$method->enabled || !$method->payment_published)
                continue;
            $payment_deposit_level_selected = $app->input->getString("payment_deposit_level_$method->payment_id");
            if ($method->payment_params->payment_deposit == "1" && $payment_deposit_level_selected === "partial_payment") {
                $payment_deposit_level_selected = $app->input->getString("payment_deposit_level_$method->payment_id");
                $method->payment_deposit_level_selected = $payment_deposit_level_selected;
                $payment_deposit_type = $method->payment_params->payment_deposit_type;
                if ($payment_deposit_type == "amount") {
                    $method->payment_deposit_value = round($method->payment_params->payment_deposit_amount, -3);
                } else {
                    $payment_deposit_percent = $method->payment_params->payment_deposit_percent;
                    $method->payment_deposit_value = round(($payment_deposit_percent * @$order->full_total->prices[0]->price_value_with_tax) / 100, -3);
                }
            }
            if (method_exists($this, 'needCC')) {
                $this->needCC($method);
            } else if (!empty($this->ask_cc)) {
                $method->ask_cc = true;
                if (!empty($this->ask_owner))
                    $method->ask_owner = true;
                if (!empty($method->payment_params->ask_ccv))
                    $method->ask_ccv = true;
            }
            $price = null;
            if (@$method->payment_params->payment_price_use_tax) {
                if (isset($order->order_full_price))
                    $price = $order->order_full_price;
                if (isset($order->total->prices[0]->price_value_with_tax))
                    $price = $order->total->prices[0]->price_value_with_tax;
                if (isset($order->full_total->prices[0]->price_value_with_tax))
                    $price = $order->full_total->prices[0]->price_value_with_tax;
                if (isset($order->full_total->prices[0]->price_value_without_payment_with_tax))
                    $price = $order->full_total->prices[0]->price_value_without_payment_with_tax;
            } else {
                if (isset($order->order_full_price))
                    $price = $order->order_full_price;
                if (isset($order->total->prices[0]->price_value))
                    $price = $order->total->prices[0]->price_value;
                if (isset($order->full_total->prices[0]->price_value))
                    $price = $order->full_total->prices[0]->price_value;
                if (isset($order->full_total->prices[0]->price_value_without_payment))
                    $price = $order->full_total->prices[0]->price_value_without_payment;
            }
            if (!empty($method->payment_params->payment_min_price) && hikashop_toFloat($method->payment_params->payment_min_price) > $price) {
                $method->errors['min_price'] = (hikashop_toFloat($method->payment_params->payment_min_price) - $price);
                continue;
            }
            if (!empty($method->payment_params->payment_max_price) && hikashop_toFloat($method->payment_params->payment_max_price) < $price) {
                $method->errors['max_price'] = ($price - hikashop_toFloat($method->payment_params->payment_max_price));
                continue;
            }
            if (!empty($method->payment_params->payment_max_volume) && bccomp((float)@$method->payment_params->payment_max_volume, 0, 3)) {
                $method->payment_params->payment_max_volume_orig = $method->payment_params->payment_max_volume;
                $method->payment_params->payment_max_volume = $volumeHelper->convert($method->payment_params->payment_max_volume, @$method->payment_params->payment_size_unit);
                if ($method->payment_params->payment_max_volume < $order->volume) {
                    $method->errors['max_volume'] = ($method->payment_params->payment_max_volume - $order->volume);
                    continue;
                }
            }
            if (!empty($method->payment_params->payment_min_volume) && bccomp((float)@$method->payment_params->payment_min_volume, 0, 3)) {
                $method->payment_params->payment_min_volume_orig = $method->payment_params->payment_min_volume;
                $method->payment_params->payment_min_volume = $volumeHelper->convert($method->payment_params->payment_min_volume, @$method->payment_params->payment_size_unit);
                if ($method->payment_params->payment_min_volume > $order->volume) {
                    $method->errors['min_volume'] = ($order->volume - $method->payment_params->payment_min_volume);
                    continue;
                }
            }
            if (!empty($method->payment_params->payment_max_weight) && bccomp((float)@$method->payment_params->payment_max_weight, 0, 3)) {
                $method->payment_params->payment_max_weight_orig = $method->payment_params->payment_max_weight;
                $method->payment_params->payment_max_weight = $weightHelper->convert($method->payment_params->payment_max_weight, @$method->payment_params->payment_weight_unit);
                if ($method->payment_params->payment_max_weight < $order->weight) {
                    $method->errors['max_weight'] = ($method->payment_params->payment_max_weight - $order->weight);
                    continue;
                }
            }
            if (!empty($method->payment_params->payment_min_weight) && bccomp((float)@$method->payment_params->payment_min_weight, 0, 3)) {
                $method->payment_params->payment_min_weight_orig = $method->payment_params->payment_min_weight;
                $method->payment_params->payment_min_weight = $weightHelper->convert($method->payment_params->payment_min_weight, @$method->payment_params->payment_weight_unit);
                if ($method->payment_params->payment_min_weight > $order->weight) {
                    $method->errors['min_weight'] = ($order->weight - $method->payment_params->payment_min_weight);
                    continue;
                }
            }
            if (!empty($method->payment_params->payment_max_quantity) && (int)$method->payment_params->payment_max_quantity) {
                if ($method->payment_params->payment_max_quantity < $order->total_quantity) {
                    $method->errors['max_quantity'] = ($method->payment_params->payment_max_quantity - $order->total_quantity);
                    continue;
                }
            }
            if (!empty($method->payment_params->payment_min_quantity) && (int)$method->payment_params->payment_min_quantity) {
                if ($method->payment_params->payment_min_quantity > $order->total_quantity) {
                    $method->errors['min_quantity'] = ($order->total_quantity - $method->payment_params->payment_min_quantity);
                    continue;
                }
            }
            $method->features = $this->features;
            if (!$this->checkPaymentDisplay($method, $order))
                continue;
            if (!empty($order->paymentOptions) && !empty($order->paymentOptions['recurring']) && empty($method->features['recurring']))
                continue;
            if (!empty($order->paymentOptions) && !empty($order->paymentOptions['term']) && empty($method->features['authorize_capture']))
                continue;
            if (!empty($order->paymentOptions) && !empty($order->paymentOptions['refund']) && empty($method->features['refund']))
                continue;
            if ((int)$method->payment_ordering > 0 && !isset($usable_methods[(int)$method->payment_ordering]))
                $usable_methods[(int)$method->payment_ordering] = $method;
            else
                $usable_methods[] = $method;

        }
        return true;
    }

    function onPaymentSave(&$cart, &$rates, &$payment_id)
    {
        $usable = array();
        $this->onPaymentDisplay($cart, $rates, $usable);
        $payment_id = (int)$payment_id;
        foreach ($usable as $usable_method) {
            if ($usable_method->payment_id == $payment_id)
                return $usable_method;
        }
        return false;
    }

    function onPaymentConfiguration(&$element)
    {
        $this->pluginConfiguration($element);
        if (empty($element) || empty($element->payment_type)) {
            $element = new stdClass();
            $element->payment_type = $this->pluginName;
            $element->payment_params = new stdClass();
            $this->getPaymentDefaultValues($element);
        }
        $this->order_statuses = hikashop_get('type.categorysub');
        $this->order_statuses->type = 'status';
        $this->currency = hikashop_get('type.currency');
        $this->weight = hikashop_get('type.weight');
        $this->volume = hikashop_get('type.volume');
    }

    function onPaymentConfigurationSave(&$element)
    {
        if (empty($this->pluginConfig))
            return true;
        $formData = JRequest::getVar('data', array(), '', 'array', JREQUEST_ALLOWRAW);
        if (!isset($formData['payment']['payment_params']))
            return true;
        foreach ($this->pluginConfig as $key => $config) {
            if ($config[1] == 'textarea' || $config[1] == 'big-textarea') {
                $element->payment_params->$key = @$formData['payment']['payment_params'][$key];
            }
        }
        return true;
    }

    function onBeforeOrderCreate(&$order, &$do)
    {
        $app = JFactory::getApplication();
        if ($app->isAdmin())
            return true;
        if (empty($order->order_payment_method) || $order->order_payment_method != $this->name)
            return true;
        if (!empty($order->order_type) && $order->order_type != 'sale')
            return true;
        $this->loadOrderData($order);
        $this->loadPaymentParams($order);
        if (empty($this->payment_params)) {
            $do = false;
            return true;
        }
    }
    public function showPage($page){
        $name=$this->name;
       require __DIR__."/$name/{$name}_end.php";

    }
    function onAfterOrderConfirm(&$order, &$methods, $method_id)
    {
        $this->payment = $methods[$method_id];
        $this->payment_params =& $this->payment->payment_params;
        $this->payment_name = $this->payment->payment_name;
        $this->loadOrderData($order);
        $this->order = $order;
    }

    function onPaymentNotification(&$statuses)
    {
    }

    function onOrderPaymentCapture(&$order, $total)
    {
        return false;
    }

    function onOrderAuthorizationCancel(&$order)
    {
        return false;
    }

    function onOrderAuthorizationRenew(&$order)
    {
        return false;
    }

    function onOrderPaymentRefund(&$order, $total)
    {
        return false;
    }

    function getOrder($order_id)
    {
        $ret = null;
        if (empty($order_id))
            return $ret;
        $orderClass = hikashop_get('class.order');
        $ret = $orderClass->get($order_id);
        return $ret;
    }

    function modifyOrder(&$order_id, $order_status, $history = null, $email = null, $payment_params = null)
    {
        if (is_object($order_id)) {
            $order =& $order_id;
        } else {
            $order = new stdClass();
            $order->order_id = $order_id;
        }
        if ($order_status !== null)
            $order->order_status = $order_status;
        $history_notified = 0;
        $history_amount = '';
        $history_data = '';
        $history_type = '';
        if (!empty($history)) {
            if ($history === true) {
                $history_notified = 1;
            } else if (is_array($history)) {
                $history_notified = (int)@$history['notified'];
                $history_amount = @$history['amount'];
                $history_data = @$history['data'];
                $history_type = @$history['type'];
            } else {
                $history_notified = (int)@$history->notified;
                $history_amount = @$history->amount;
                $history_data = @$history->data;
                $history_type = @$history->type;
            }
        }
        $order->history = new stdClass();
        $order->history->history_reason = WoobookingText::sprintf('AUTOMATIC_PAYMENT_NOTIFICATION');
        $order->history->history_notified = $history_notified;
        $order->history->history_payment_method = $this->name;
        $order->history->history_type = 'payment';
        if (!empty($history_amount))
            $order->history->history_amount = $history_amount;
        if (!empty($history_data))
            $order->history->history_data = $history_data;
        if (!empty($history_type))
            $order->history->history_type = $history_type;
        if ($payment_params !== null) {
            if (isset($order->order_payment_params)) {
                foreach ($payment_params as $k => $v) {
                    $order->order_payment_params->$k = $v;
                }
            } else {
                $order->order_payment_params = $payment_params;
            }
        }
        if (!is_object($order_id) && $order_id !== false) {
            $orderClass = hikashop_get('class.order');
            $orderClass->save($order);
        }
        $mailer = JFactory::getMailer();
        $config =& hikashop_config();
        $recipients = trim($config->get('payment_notification_email', ''));
        if (empty($email) || empty($recipients))
            return;
        $sender = array(
            $config->get('from_email'),
            $config->get('from_name')
        );
        $mailer->setSender($sender);
        $mailer->addRecipient(explode(',', $recipients));
        $payment_status = $order_status;
        $mail_status = hikashop_orderStatus($order_status);
        $order_number = '';
        global $Itemid;
        $this->url_itemid = empty($Itemid) ? '' : '&Itemid=' . $Itemid;
        if (is_object($order_id)) {
            $subject = WoobookingText::sprintf('PAYMENT_NOTIFICATION', $this->name, $payment_status);
            $url = HIKASHOP_LIVE . 'administrator/index.php?option=com_hikashop&ctrl=order&task=listing' . $this->url_itemid;
            if (isset($order->order_id))
                $url = HIKASHOP_LIVE . 'administrator/index.php?option=com_hikashop&ctrl=order&task=edit&order_id=' . $order->order_id . $this->url_itemid;
            if (isset($order->order_number))
                $order_number = $order->order_number;
        } elseif ($order_id !== false) {
            $dbOrder = $orderClass->get($order_id);
            $order_number = $dbOrder->order_number;
            $subject = WoobookingText::sprintf('PAYMENT_NOTIFICATION_FOR_ORDER', $this->name, $payment_status, $order_number);
            $url = HIKASHOP_LIVE . 'administrator/index.php?option=com_hikashop&ctrl=order&task=edit&order_id=' . $order_id . $this->url_itemid;
        }
        $order_text = '';
        if (is_string($email))
            $order_text = "\r\n\r\n" . $email;
        $body = str_replace('<br/>', "\r\n", WoobookingText::sprintf('PAYMENT_NOTIFICATION_STATUS', $this->name, $payment_status)) . ' ' .
            WoobookingText::sprintf('ORDER_STATUS_CHANGED', $mail_status) .
            "\r\n" . WoobookingText::sprintf('NOTIFICATION_OF_ORDER_ON_WEBSITE', $order_number, HIKASHOP_LIVE) .
            "\r\n" . str_replace('<br/>', "\r\n", WoobookingText::sprintf('ACCESS_ORDER_WITH_LINK', $url)) . $order_text;
        if (is_object($email)) {
            if (!empty($email->subject))
                $subject = $email->subject;
            if (!empty($email->body))
                $body = $email->body;
        }
        $mailer->setSubject($subject);
        $mailer->setBody($body);
        $mailer->Send();
    }

    function loadOrderData(&$order)
    {
        $this->app = JFactory::getApplication();
        $lang = JFactory::getLanguage();
        $currencyClass = hikashop_get('class.currency');
        $cartClass = hikashop_get('class.cart');
        $this->currency = 0;
        if (!empty($order->order_currency_id)) {
            $currencies = null;
            $currencies = $currencyClass->getCurrencies($order->order_currency_id, $currencies);
            $this->currency = $currencies[$order->order_currency_id];
        }
        hikashop_loadUser(true, true);
        $this->user = hikashop_loadUser(true);
        $this->locale = strtolower(substr($lang->get('tag'), 0, 2));
        global $Itemid;
        $this->url_itemid = empty($Itemid) ? '' : '&Itemid=' . $Itemid;
        $billing_address = $this->app->getUserState(HIKASHOP_COMPONENT . '.billing_address');
        if (!empty($billing_address))
            $cartClass->loadAddress($order->cart, $billing_address, 'object', 'billing');
        $shipping_address = $this->app->getUserState(HIKASHOP_COMPONENT . '.shipping_address');
        if (!empty($shipping_address))
            $cartClass->loadAddress($order->cart, $shipping_address, 'object', 'shipping');
    }

    function loadPaymentParams(&$order)
    {
        $payment_id = @$order->order_payment_id;
        $this->payment_params = null;
        if (!empty($order->order_payment_method) && $order->order_payment_method == $this->name && !empty($payment_id) && $this->pluginParams($payment_id))
            $this->payment_params =& $this->plugin_params;
    }

    function ccLoad($ccv = true)
    {
        if (!isset($this->app))
            $this->app = JFactory::getApplication();
        $this->cc_number = $this->app->getUserState(HIKASHOP_COMPONENT . '.cc_number');
        if (!empty($this->cc_number)) $this->cc_number = base64_decode($this->cc_number);
        $this->cc_month = $this->app->getUserState(HIKASHOP_COMPONENT . '.cc_month');
        if (!empty($this->cc_month)) $this->cc_month = base64_decode($this->cc_month);
        $this->cc_year = $this->app->getUserState(HIKASHOP_COMPONENT . '.cc_year');
        if (!empty($this->cc_year)) $this->cc_year = base64_decode($this->cc_year);
        $this->cc_type = $this->app->getUserState(HIKASHOP_COMPONENT . '.cc_type');
        if (!empty($this->cc_type)) {
            $this->cc_type = base64_decode($this->cc_type);
        }
        $this->cc_owner = $this->app->getUserState(HIKASHOP_COMPONENT . '.cc_owner');
        if (!empty($this->cc_owner)) {
            $this->cc_owner = base64_decode($this->cc_owner);
        }
        $this->cc_CCV = '';
        if ($ccv) {
            $this->cc_CCV = $this->app->getUserState(HIKASHOP_COMPONENT . '.cc_CCV');
            if (!empty($this->cc_CCV)) $this->cc_CCV = base64_decode($this->cc_CCV);
        }
    }

    function ccClear()
    {
        if (!isset($this->app))
            $this->app = JFactory::getApplication();
        $this->app->setUserState(HIKASHOP_COMPONENT . '.cc_number', '');
        $this->app->setUserState(HIKASHOP_COMPONENT . '.cc_month', '');
        $this->app->setUserState(HIKASHOP_COMPONENT . '.cc_year', '');
        $this->app->setUserState(HIKASHOP_COMPONENT . '.cc_type', '');
        $this->app->setUserState(HIKASHOP_COMPONENT . '.cc_owner', '');
        $this->app->setUserState(HIKASHOP_COMPONENT . '.cc_CCV', '');
        $this->app->setUserState(HIKASHOP_COMPONENT . '.cc_valid', 0);
    }

    function cronCheck()
    {
        if (empty($this->name))
            return false;
        $pluginsClass = hikashop_get('class.plugins');
        $type = 'hikashop';
        if ($this->type == 'payment')
            $type = 'hikashoppayment';
        if ($this->type == 'shipping')
            $type = 'hikashopshipping';
        $plugin = $pluginsClass->getByName($type, $this->name);
        if (empty($plugin))
            return false;
        if (empty($plugin->params['period']))
            $plugin->params['period'] = 7200; // 2 hours
        if (!empty($plugin->params['last_cron_update']) && ((int)$plugin->params['last_cron_update'] + (int)$plugin->params['period']) > time())
            return false;
        $plugin->params['last_cron_update'] = time();
        $pluginsClass->save($plugin);
        return true;
    }

    function renewalOrdersAuthorizations(&$messages)
    {
        $db = JFactory::getDBO();
        $date = hikashop_getDate(time(), '%Y/%m/%d');
        $search = hikashop_getEscaped('s:18:"payment_auth_renew";s:10:"' . $date . '";');
        $query = 'SELECT * FROM ' . hikashop_table('order') .
            ' WHERE order_type = \'sale\' AND order_payment_method = ' . $db->Quote($this->name) . ' AND order_payment_params LIKE \'%' . $search . '%\'' .
            ' ORDER BY order_payment_id';
        $db->setQuery($query);
        $orders = $db->loadObjectList();
        if (!empty($orders)) {
            $cpt = 0;
            foreach ($orders as $order) {
                $order->order_payment_params = hikashop_unserialize($order->order_payment_params);
                $ret = $this->onOrderAuthorizationRenew($order);
                if ($ret) {
                    $order_payment_params = serialize($order->order_payment_params);
                    $query = 'UPDATE ' . hikashop_table('order') . ' SET order_payment_params = ' . $db->quote($order_payment_params) . ' WHERE order_id = ' . (int)$order->order_id;
                    $db->setQuery($query);
                    $db->query();
                    $cpt++;
                }
                unset($order_payment_params);
                unset($order->order_payment_params);
                unset($order);
            }
            if ($cpt > 0)
                $messages[] = '[' . ucfirst($this->name) . '] ' . WoobookingText::_sprintf('X_ORDERS_AUTHORIZATION_RENEW', $cpt);
        }
    }

    function writeToLog($data = null)
    {
        $dbg = ($data === null) ? ob_get_clean() : $data;
        if (!empty($dbg)) {
            $dbg = '-- ' . date('m.d.y H:i:s') . ' --' . (!empty($this->name) ? ('[' . $this->name . ']') : '') . "\r\n" . $dbg;
            
            $config = hikashop_config();
            $file = $config->get('payment_log_file', '');
            $file = rtrim(JPath::clean(html_entity_decode($file)), DS . ' ');
            if (!preg_match('#^([A-Z]:)?/.*#', $file) && (!$file[0] == '/' || !file_exists($file)))
                $file = JPath::clean(HIKASHOP_ROOT . DS . trim($file, DS . ' '));
            if (!empty($file) && defined('FILE_APPEND')) {
                if (!file_exists(dirname($file))) {
                    jimport('joomla.filesystem.folder');
                    JFolder::create(dirname($file));
                }
                file_put_contents($file, $dbg, FILE_APPEND);
            }
        }
        if ($data === null)
            ob_start();
    }

    function getPaymentDefaultValues(&$element)
    {
    }

    function checkPaymentDisplay(&$method, &$order)
    {
        return true;
    }
}