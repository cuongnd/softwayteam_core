<?php

class softWayControllerList extends SoftWayController
{
    public function ajax_sorting($list_ordering=array()){
        $model=$this->getModelList();
        return $model->sorting($list_ordering);

    }
}