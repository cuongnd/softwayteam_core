<?php

namespace WooBooking\CMS\Application;


use WooBooking\CMS\Registry\Registry;

class NBAppHelper
{
    public static function getConfig(){
        $db=\Factory::getDBO();
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