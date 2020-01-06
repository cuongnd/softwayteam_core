<?php

class softwayControllerList extends softway_controller
{
    public function ajax_sorting($list_ordering=array()){
        $model=$this->getModelList();
        return $model->sorting($list_ordering);

    }
}