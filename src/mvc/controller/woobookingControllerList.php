<?php

class woobookingControllerList extends woobooking_controller
{
    public function ajax_sorting($list_ordering=array()){
        $model=$this->getModelList();
        return $model->sorting($list_ordering);

    }
}