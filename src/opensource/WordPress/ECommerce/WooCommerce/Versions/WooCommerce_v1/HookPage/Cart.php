<?php


namespace WooBooking\CMS\OpenSource\WordPress\ECommerce\WooCommerce\Versions\WooCommerce_v1\HookPage;



class Cart
{
    public static $instance=null;
    public static function getInstance(){
        if(!self::$instance){
            self::$instance=new self();
        }
        return self::$instance;
    }
    public function woocommerce_before_cart_contents(){



    }
    public function woocommerce_cart_contents(){
       echo "hello woocommerce_cart_contents";

    }
    public function woocommerce_after_cart_contents(){
       echo "hello woocommerce_after_cart_contents";

    }

}