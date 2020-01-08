<?php

namespace SoftWay\CMS\Application;


use Factory;
use SoftWay\CMS\Registry\Registry;
use SoftWay\CMS\Utilities\Utility;

class SWAppHelper
{
    public static function getConfig(){
        echo "<pre>";
        print_r(Utility::printDebugBacktrace(), false);
        echo "</pre>";
        die;
        $db=Factory::getDBO();

        $query=$db->getQuery(true);
        $query->select("*")
            ->from(PREFIX_TABLE."config")
            ->where('id=1')
        ;
        $item=$db->setQuery($query)->loadObject();


        $param=$item->params;
        $reg=new Registry();
        $reg->loadString($param);


        return $reg;
    }
}