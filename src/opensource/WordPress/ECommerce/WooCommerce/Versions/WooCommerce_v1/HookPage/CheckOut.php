<?php


namespace WooBooking\CMS\OpenSource\WordPress\ECommerce\WooCommerce\Versions\WooCommerce_v1\HookPage;

use WC_Customer;
use WoobookingModel;

class CheckOut
{
    public static $instance=null;
    public static function getInstance(){
        if(!self::$instance){
            self::$instance=new self();
        }
        return self::$instance;
    }
    public function woocommerce_order_review(){
        echo "hello woocommerce_order_review";
    }
    public function woocommerce_checkout_order_processed($order_id, $posted_data, $OpenSourceOrder){
        $modelOrder=WoobookingModel::getInstance('order');
        $modelOrderStatus=WoobookingModel::getInstance('OrderStatus');
        $modelCustomer=WoobookingModel::getInstance('Customer');
        $modelRate=WoobookingModel::getInstance('Rate');



        $openSourceOrderData=$OpenSourceOrder->get_data();
        $openSourceCustomerId=$openSourceOrderData['customer_id'];
        $billing=$openSourceOrderData['billing'];

        $customerApp=$modelCustomer->getCustomerByOpenSouceUserEcommerceId($openSourceCustomerId);
        if(!$customerApp->id){
            //create new customer
            $array_customer=array(
                'user_open_source_commerce_id'=>$openSourceCustomerId,
                'first_name'=>$billing['first_name'],
                'last_name'=>$billing['last_name'],
                'company'=>$billing['company'],
                'address_1'=>$billing['address_1'],
                'address_2'=>$billing['address_2'],
                'city'=>$billing['city'],
                'state'=>$billing['state'],
                'postcode'=>$billing['postcode'],
                'country'=>$billing['country'],
                'email'=>$billing['email'],
                'mobile'=>$billing['phone'],
            );
            $customerApp=$modelCustomer->save($array_customer);
        }
        //object customer new is $customerApp
        //get order status from open
        $order_status_name=$openSourceOrderData['status'];
        //
        $orderStatus=$modelOrderStatus->getOrderStatusByName($order_status_name);
        //
        if(!$orderStatus['id']){
            $new_order_status=array(
                'name'=>$order_status_name
            );
            $orderStatus=$modelOrderStatus->save($new_order_status);
        }
        //create data new order
        $order_data=array(
            'open_source_order_id'=>$order_id,
            'order_status_id'=>$orderStatus['id'],
            'customer_id'=>$customerApp->id,
            'discount_total'=>$openSourceOrderData['discount_total'],
            'discount_tax'=>$openSourceOrderData['discount_tax'],
            'shipping_tax'=>$openSourceOrderData['shipping_tax'],
            'cart_tax'=>$openSourceOrderData['cart_tax'],
            'total_tax'=>$openSourceOrderData['total_tax'],
            'total'=>$openSourceOrderData['total'],

        );
        //store database make new order is $order
        $order=$modelOrder->save($order_data);

        $modelBilling=WoobookingModel::getInstance('Billing');
        //save info billing
        //create data new billing
        $dataBilling=array(
            'order_id'=>$order->id,
            'first_name'=>$billing['first_name'],
            'last_name'=>$billing['last_name'],
            'company'=>$billing['company'],
            'address_1'=>$billing['address_1'],
            'address_2'=>$billing['address_2'],
            'city'=>$billing['city'],
            'state'=>$billing['state'],
            'postcode'=>$billing['postcode'],
            'country'=>$billing['country'],
            'email'=>$billing['email'],
            'phone'=>$billing['phone'],
        );
        //store to database
        $modelBilling->save($dataBilling);
        //create info shipping
        //get model shipping
        $modelShipping=WoobookingModel::getInstance('Shipping');
        //get info shipping from opensource
        $shipping=$openSourceOrderData['shipping'];
        //create data new shipping
        $dataShipping=array(
            'order_id'=>$order->id,
            'first_name'=>$shipping['first_name'],
            'last_name'=>$shipping['last_name'],
            'company'=>$shipping['company'],
            'address_1'=>$shipping['address_1'],
            'address_2'=>$shipping['address_2'],
            'city'=>$shipping['city'],
            'state'=>$shipping['state'],
            'postcode'=>$shipping['postcode'],
            'country'=>$shipping['country'],
            'email'=>$shipping['email'],
            'phone'=>$shipping['phone'],
        );
        //store data shipping
        $modelShipping->save($dataShipping);
        //create orderdetail
        //get model order detail
        $modelOrderDetail=WoobookingModel::getInstance('OrderDetail');
        //get product from open source
        $OpenSourceOrderItems=$OpenSourceOrder->get_items();
        //tree node items
        foreach ($OpenSourceOrderItems as $item){
            $OpenSourceProduct=$item->get_data();
            $metas_data=$OpenSourceProduct['meta_data'];
            $data_order_detail=array(
                'order_id'=>$order->id,
                'open_source_link_id'=>$OpenSourceProduct['id'],
                'quantity'=>$OpenSourceProduct['quantity'],
                'subtotal'=>$OpenSourceProduct['subtotal'],
                'total'=>$OpenSourceProduct['total'],
                'total_tax'=>$OpenSourceProduct['total_tax']
            );
            //get attribute rate
            foreach ($metas_data as $meta_data){
                $current_meta_data=$meta_data->get_data();
                $data_order_detail[$current_meta_data['key']]=$current_meta_data['value'];
            }
            $new_order_detail=$modelOrderDetail->save($data_order_detail);

            $modelOrderDetailRate=WoobookingModel::getInstance('OrderDetailRate');

            //store new rate
            foreach ($metas_data as $meta_data){
                $current_meta_data=$meta_data->get_data();

                if($current_meta_data['key']=='rates'){
                    $value_rates=$current_meta_data['value'];


                    foreach ($value_rates as $rate_id){
                       
                        $item_rate=$modelRate->getItem($rate_id);
                        $new_order_detail_rate=[
                            'order_detail_id'=>$new_order_detail->id,
                            'rate_id'=>$rate_id,
                            'start_date'=>$item_rate->from,
                            'end_date'=>$item_rate->to,
                        ];
                        $modelOrderDetailRate->save($new_order_detail_rate);
                    }
                }
            }
        }
    }
}