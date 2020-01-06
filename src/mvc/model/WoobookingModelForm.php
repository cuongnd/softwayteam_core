<?php


use WooBooking\CMS\Form\Form;
use WooBooking\CMS\Registry\Registry;


class WoobookingModelForm extends WoobookingModel
{
    public $model="";
    public $__state_set=false;
    public $key_table="id";
    public $_errors=[];
    public function __construct($config = array())
    {
        parent::__construct($config = array());
    }

    public function getHeader(){

        $db=Factory::getDBO();
        $fields=$db->getTableColumns(self::getTableName());
        $list= array_keys($fields);
        return $list;


    }


    public function getForm($data = array(), $loadData = true)
    {


        // Get the form.
        $form = $this->loadForm($this->model, array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form))
        {
            return false;
        }
        return $form;
    }


    protected function loadForm($source = null, $options = array(), $clear = false, $xpath = false)
    {

        Form::addFieldPath(__DIR__ . '/../../form/fields');
        Form::addFormPath(WOOBOOKING_PATH_COMPONENT . '/models/forms');
        Form::addFieldPath(WOOBOOKING_PATH_COMPONENT . '/models/fields');
        try
        {
            $form = Form::getInstance($source, $options, false, $xpath);
            if (isset($options['load_data']) && $options['load_data'])
            {
                // Get the data for the form.
                $data = $this->loadFormData();
            }
            else
            {
                $data = array();
            }


            // Load the data into the form after the plugins have operated.
            $form->bind($data);
        }
        catch (\Exception $e)
        {
            echo $e->getMessage();
            die;
            $this->setError($e->getMessage());

            return false;
        }


        return $form;
    }
    public function delete($id){
        $table=$this->getTable();
        if($table->delete($id))
        {
            return true;
        }
        return false;
    }
    public function save($data=array()){
        $table=$this->getTable();
        $key = $table->getKeyName();
        $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->context . '.'.$key);
        $isNew = true;
        if ($pk > 0)
        {
            $table->load($pk);
            $isNew = false;
        }


        if($table->save($data))
        {
            $this->setState($this->context . '.'.$key,$table->{$key});
            return (object)$table->getProperties();
        }

        return false;
    }
    public function duplicate($id){
        $table=$this->getTable();
        $table->load($id);
        $table->{$this->key_table}=0;
        if($table->store())
        {
            return (object)$table->getProperties();
        }
        return false;
    }
    protected function loadFormData()
    {
        $id=$this->getState($this->context.'.'.$this->key_table);

        $data = $this->getItem($id);
        return $data;
    }

    public function setError($error)
    {
        $this->_errors[] = $error;
    }

    public function buildQuery()
    {
        $db=Factory::getDBO();
        $query=$db->getQuery(true);
        $query->select('*')
            ->from($this->getTableName())
            ;
        $id=$this->getState($this->context . '.id');
        if($id){
            $query->where('id='.(int)$id);
        }
        return $query;
    }

    public function getItem($id=0){
        $table=$this->getTable();
        $key = $table->getKeyName();
        $input=Factory::getInput();
        if(!$id){
            $id=$input->getInt($key,0);
        }
        $db=Factory::getDBO();
        $query=$db->getQuery(true);
        $query->select('*')
            ->from($this->getTableName())
            ->where($key.'='.(int)$id)
        ;
        $item=$this->db->setQuery($query)->loadObject();
        return $item;
    }

}
