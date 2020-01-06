<?php
use WooBooking\CMS\Form\Form;
use WooBooking\CMS\Pagination\Pagination;

class WoobookingModelList extends  WoobookingModel
{
    public $filterFormName="";
    public $context="";

    public $table_name="";
    public $query;

    public function buildQuery()
    {

        $query=$this->db->getQuery(true);
        $query->select("a.*")
            ->from($this->getTableName().' AS a')

            ;
        $query->order($this->getState('list.ordering', 'a.ordering') . ' ' . $this->getState('list.direction', 'ASC'));

        return $query;
    }
    public function getListQuery()
    {

        $query=$this->db->getQuery(true);
        $query->select("a.*")
            ->from($this->getTableName().' AS a')

            ;
        $query->order($this->getState('list.ordering', 'a.ordering') . ' ' . $this->getState('list.direction', 'ASC'));

        return $query;
    }
    /**
     * Method to get the total number of items for the data set.
     *
     * @return  integer  The total number of items available in the data set.
     *
     * @since   1.6
     */
    public function getTotal()
    {
        return  (int) $this->_getListCount($this->_getListQuery());
    }
    public function _getListQuery()
    {
        $this->query = $this->getListQuery();
        return $this->query;
    }

    public function getStart()
    {
        $start = $this->getState('list.start');
        if ($start > 0)
        {
            $limit = $this->getState('list.limit');
            $total = $this->getTotal();

            if ($start > $total - $limit)
            {
                $start = max(0, (int) (ceil($total / $limit) - 1) * $limit);
            }
        }


        return $start;
    }


    protected function _getList($query, $limitstart = 0, $limit = 0)
    {
        $this->db->setQuery($query, $limitstart, $limit);

        return $this->db->loadObjectList();
    }

    public function getPagination()
    {
        $limit = (int) $this->getState('list.limit') - (int) $this->getState('list.links');
        $limit = 20;
       return new Pagination($this->getTotal(), $this->getStart(), $limit);
    }

    public function getList()
    {
        $db=Factory::getDBO();
        $query=$this->getListQuery();
        $limit_per_page=$this->get_limit_per_page();
        
        return $db->setQuery($query, $this->getStart(),$limit_per_page)->loadObjectList();
    }
    public function sorting($list){
        $table=$this->getTable();
        $key=$table->getKeyName();
        $modelItem=$this->getModelItem();
        foreach ($list as $item){
            $item=(object)$item;
            $item_sorting=[
                $key=>$item->$key,
                'ordering'=>$item->ordering
            ];
            if(!$modelItem->save($item_sorting)){
                return false;
            }
        }
        return true;


    }
    public function getFilterForm($data = array(), $loadData = true)
    {
        
        Form::addFormPath(WOOBOOKING_PATH_COMPONENT."/models/forms");
        $form = null;

        // Try to locate the filter form automatically. Example: ContentModelArticles => "filter_articles"
        if (empty($this->filterFormName))
        {
            $classNameParts = explode('Model', get_called_class());



            if (count($classNameParts) == 2)
            {
                $this->filterFormName = 'filter_' . strtolower($classNameParts[0]);

            }
        }

        if (!empty($this->filterFormName))
        {
            // Get the form.


            $form = $this->loadForm('filter_'.$this->context , $this->filterFormName, array('control' => '', 'load_data' => $loadData));
        }

        return $form;
    }


}
