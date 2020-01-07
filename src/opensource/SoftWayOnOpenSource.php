<?php
namespace SoftWay\CMS\OpenSource;
use SoftWay\CMS\OpenSource\WordPress\SoftWayOnWordpress;
class      SoftWayOnOpenSource
{
    public static $instance=null;
    public  $open_source=null;
    public static function getInstance(){

        if (!isset(self::$instance))
        {
            $current_open_source="wordpress";
            if($current_open_source=="wordpress"){
                self::$instance=  SoftWayOnWordpress::getInstance();
            }
        }

        return self::$instance;
    }

    public function __construct()
    {

    }
    public function getSession(){
        $this->open_source->getSession();
    }

}