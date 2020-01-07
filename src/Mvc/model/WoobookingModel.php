<?php

use SoftWay\CMS\Mvc\model\BaseDatabaseModel;
use SoftWay\CMS\Utilities\Utility;
use SoftWay\CMS\Form\Form;
use SoftWay\CMS\Filesystem\Path;
use SoftWay\CMS\Object\CMSObject;
use SoftWay\CMS\FOFInput\FOFInput;

class WoobookingModel extends BaseDatabaseModel
{
    public $model = "";
    public $modelItem = "";
    public $modelList = "";
    public $state = "";
    public $context = "";
    public $table_name = null;
    public $__state_set = null;
    public static $instance = array();
    public $key_table = "id";
    public $_errors = [];
    public $db = null;
    private $filter_fields=[];

    public function __construct($config = array())
    {
        if (!$this->context) {
            echo "public var context this model cannot null, please define it in class " . get_called_class() . ", please see other model in nebase app";
            die;
        }

        if (!$this->table_name) {
            echo "public var table_name this model cannot null, please define it in class " . get_called_class() . ", please see other model in nebase app";
            die;
        }
        $this->state = new CMSObject();
        $this->_db = Factory::getDBO();
        $this->db = Factory::getDBO();
    }

    public function buildQuery()
    {

    }

    public function getModel($model = "")
    {
        if (!$model) {
            $model = $this->model;
        }
        $Ucfmodel = ucfirst($model);
        $model_path = SOFTWAY_PATH_APP . "/models/$Ucfmodel.php";
        if (file_exists($model_path)) {
            require_once $model_path;
            $model_name = "{$model}Model";
            $model_class = WoobookingModel::getInstance($model);
            $model_class->model = $model;
            return $model_class;
        } else {
            throw new Exception("Error model <b>$model_path</b> not exits, please create first");
        }

    }

    public function getModelItem($model = "")
    {
        if (!$model) {
            $model = $this->modelItem;
        }
        if (!$model) {

            throw new Exception("please defined publish var modelItem in file " . get_called_class());
        }
        $model_path = SOFTWAY_PATH_APP . "/models/$model.php";
        if (file_exists($model_path)) {
            require_once $model_path;
            $model_name = "{$model}Model";
            $model_class = WoobookingModel::getInstance($model);
            $model_class->model = $model;
            return $model_class;
        } else {
            throw new Exception("Error model <b>$model_path</b> not exits, please create first");
        }

    }

    public function getModelList($model = "")
    {
        if (!$model) {
            $model = $this->modelList;
        }
        $model_path = SOFTWAY_PATH_APP . "/models/$model.php";
        if (file_exists($model_path)) {
            require_once $model_path;
            $model_name = "{$model}Model";
            $model_class = WoobookingModel::getInstance($model);
            $model_class->model = $model;
            return $model_class;
        } else {
            throw new Exception("Error model <b>$model_path</b> not exits, please create first");
        }

    }

    /**
     * Method to get model state variables
     *
     * @param string $property Optional parameter name
     * @param mixed $default Optional default value
     *
     * @return  mixed  The property where specified, the state object where omitted
     *
     * @since   3.0
     */
    public function getState($property = null, $default = null)
    {

        if (!$this->__state_set) {
            // Protected method to auto-populate the model state.
            $this->populateState();

            // Set the model state set flag to true.
            $this->__state_set = true;
        }

        return $property === null ? $this->state : $this->state->get($property, $default);
    }

    public function setState($property, $value = null)
    {
        return $this->state->set($property, $value);
    }

    /**
     * Method to auto-populate the model state.
     *
     * This method should only be called once per instantiation and is designed
     * to be called on the first call to the getState() method unless the model
     * configuration flag to ignore the request is set.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param string $ordering An optional ordering field.
     * @param string $direction An optional direction (asc|desc).
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function populateState($ordering = null, $direction = null)
    {
        // If the context is set, assume that stateful lists are used.
        if ($this->context) {
            $app = Factory::getApplication();
            $inputFilter =new FOFInput();
            // Receive & set filters

            if ($filters = $app->getUserStateFromRequest($this->context . '.filter', 'filter', array(), 'array')) {
                foreach ($filters as $name => $value) {
                    // Exclude if blacklisted
                    if (!in_array($name, $this->filterBlacklist)) {
                        $this->setState('filter.' . $name, $value);
                    }
                }
            }

            $limit = 0;

            // Receive & set list options
            if ($list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array')) {




                foreach ($list as $name => $value) {
                    // Exclude if blacklisted
                    if (!in_array($name, $this->listBlacklist)) {
                        // Extra validations
                        switch ($name) {
                            case 'fullordering':
                                $orderingParts = explode(' ', $value);

                                if (count($orderingParts) >= 2) {
                                    // Latest part will be considered the direction
                                    $fullDirection = end($orderingParts);

                                    if (in_array(strtoupper($fullDirection), array('ASC', 'DESC', ''))) {
                                        $this->setState('list.direction', $fullDirection);
                                    } else {
                                        $this->setState('list.direction', $direction);

                                        // Fallback to the default value
                                        $value = $ordering . ' ' . $direction;
                                    }

                                    unset($orderingParts[count($orderingParts) - 1]);

                                    // The rest will be the ordering
                                    $fullOrdering = implode(' ', $orderingParts);

                                    if (in_array($fullOrdering, $this->filter_fields)) {
                                        $this->setState('list.ordering', $fullOrdering);
                                    } else {
                                        $this->setState('list.ordering', $ordering);

                                        // Fallback to the default value
                                        $value = $ordering . ' ' . $direction;
                                    }
                                } else {
                                    $this->setState('list.ordering', $ordering);
                                    $this->setState('list.direction', $direction);

                                    // Fallback to the default value
                                    $value = $ordering . ' ' . $direction;
                                }
                                break;

                            case 'ordering':
                                if (!in_array($value, $this->filter_fields)) {
                                    $value = $ordering;
                                }
                                break;

                            case 'direction':
                                if (!in_array(strtoupper($value), array('ASC', 'DESC', ''))) {
                                    $value = $direction;
                                }
                                break;

                            case 'limit':
                                $limit = $value;
                                $value = $inputFilter->clean($value, 'int');

                                break;

                            case 'select':
                                $explodedValue = explode(',', $value);

                                foreach ($explodedValue as &$field) {
                                    $field = $inputFilter->clean($field, 'cmd');
                                }

                                $value = implode(',', $explodedValue);
                                break;
                        }

                        $this->setState('list.' . $name, $value);
                    }
                }
            } else // Keep B/C for components previous to jform forms for filters
            {
                // Pre-fill the limits
                $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'uint');

                $this->setState('list.limit', $limit);

                // Check if the ordering field is in the whitelist, otherwise use the incoming value.
                $value = $app->getUserStateFromRequest($this->context . '.ordercol', 'filter_order', $ordering);

                if (!in_array($value, $this->filter_fields)) {
                    $value = $ordering;
                    $app->setUserState($this->context . '.ordercol', $value);
                }

                $this->setState('list.ordering', $value);

                // Check if the ordering direction is valid, otherwise use the incoming value.
                $value = $app->getUserStateFromRequest($this->context . '.orderdirn', 'filter_order_Dir', $direction);

                if (!in_array(strtoupper($value), array('ASC', 'DESC', ''))) {
                    $value = $direction;
                    $app->setUserState($this->context . '.orderdirn', $value);
                }

                $this->setState('list.direction', $value);
            }

            // Support old ordering field
            $oldOrdering = $app->input->get('filter_order');

            if (!empty($oldOrdering) && in_array($oldOrdering, $this->filter_fields)) {
                $this->setState('list.ordering', $oldOrdering);
            }

            // Support old direction field
            $oldDirection = $app->input->get('filter_order_Dir');

            if (!empty($oldDirection) && in_array(strtoupper($oldDirection), array('ASC', 'DESC', ''))) {
                $this->setState('list.direction', $oldDirection);
            }

            $value = $app->getUserStateFromRequest($this->context . 'limitstart', 'limitstart', 0, 'int');
            $limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
            //by cuongnd
            //TODO kiem tra lai cai nay
            $limitstart=$value;
            $this->setState('list.start', $limitstart);
        } else {

            $this->setState('list.start', 0);
            $this->setState('list.limit', 0);
        }
        $table = $this->getTable();
        $key = $table->getKeyName();

        // Get the pk of the record from the request.
        $pk = Factory::getInput()->getInt($key);


        $this->setState($this->context . '.id', $pk);
    }


    public static function getInstance($model, $prefix = '', $config = array()):WoobookingModel
    {
        $model = preg_replace('/[^A-Z0-9_\.-]/i', '', $model);
        $model = ucfirst($model);
        $model_path = SOFTWAY_PATH_APP . "/models/$model.php";
        if (file_exists($model_path)) {
            require_once $model_path;
            if (!array_key_exists($model, self::$instance)) {
                $class_name = "{$model}Model";
                self::$instance[$model] = new $class_name();
                self::$instance[$model]->model = $model;
            }

        } else {
            throw new Exception("can not found model:" . Utility::get_short_file_by_path($model_path) . ',please create it');
        }
        return self::$instance[$model];
    }

    protected static function _createFileName($type, $parts = array())
    {
        $filename = '';

        switch ($type) {
            case 'model':
                $filename = strtolower($parts['name']) . '.php';
                break;
        }

        return $filename;
    }

    public static function addIncludePath($path = '', $prefix = 'Model')
    {
        static $paths;

        if (!isset($paths)) {
            $paths = array();
        }

        if (!isset($paths[$prefix])) {
            $paths[$prefix] = array();
        }

        if (!isset($paths[''])) {
            $paths[''] = array();
        }

        if (!empty($path)) {

            foreach ((array)$path as $includePath) {
                if (!in_array($includePath, $paths[$prefix])) {
                    array_unshift($paths[$prefix], Path::clean($includePath));
                }

                if (!in_array($includePath, $paths[''])) {
                    array_unshift($paths[''], Path::clean($includePath));
                }
            }
        }

        return $paths[$prefix];
    }

    public function getName()
    {
        return $this->model;
    }

    public function getHeader()
    {

        $db = Factory::getDBO();
        $fields = $db->getTableColumns(self::getTableName());
        $list = array_keys($fields);
        return $list;


    }

    public function getForm($data = array(), $loadData = true)
    {


        // Get the form.
        $form = $this->loadForm($this->model, array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }
        return $form;
    }


    protected function loadForm($source = null, $options = array(), $clear = false, $xpath = false)
    {
        Form::addFieldPath(__DIR__ . '/../../form/fields');
        Form::addFormPath(SOFTWAY_PATH_APP . '/models/forms');
        Form::addFieldPath(SOFTWAY_PATH_APP . '/models/fields');
        try {
            $form = Form::getInstance($source, $options, false, $xpath);
            if (isset($options['load_data']) && $options['load_data']) {
                // Get the data for the form.
                $data = $this->loadFormData();
            } else {
                $data = array();
            }


            // Load the data into the form after the plugins have operated.
            $form->bind($data);
        } catch (\Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }


        return $form;
    }

    public function delete($id)
    {
        $table = $this->getTable();


        if ($table->delete($id)) {
            return true;
        }
        return false;
    }

    public function save($data)
    {
        $table = $this->getTable();
        if ($table->save($data)) {
            return $table->getProperties();
        }
        return false;
    }

    protected function loadFormData()
    {
        return array();
    }

    public function setError($error)
    {
        $this->_errors[] = $error;
    }

    public function getItem($id = 0)
    {

        $db = Factory::getDBO();

        $query = $db->getQuery(true)
            ->select("*")
            ->from($this->getTableName())
            ->where("{$this->key_table}=" . (int)$id);
        return $db->setQuery($query)->loadObject();
    }

    public function getTableName($table = "")
    {
        if (!$table) {
            $table = $this->table_name;
        }
        return PREFIX_TABLE . "$table";
    }

    public function setTableName($table)
    {
        $this->table_name = $table;
    }

    public function getTable($table = "", $prefix = 'Table', $options = array())
    {
        if (!$table) {
            $table = $this->table_name;
        }
        $UCFtable = ucfirst($table);
        $table_path = SOFT_WAY_CORE_PATH_ROOT . "/admin/sw_apps/softway_core/tables/$UCFtable.php";
        if (file_exists($table_path)) {
            require_once $table_path;
            $table_name = "{$UCFtable}Table";
            $db = Factory::getDBO();
            $table_class = new $table_name($this->getTableName($table), 'id', $db);
            return $table_class;
        } else {
            throw new Exception("Error: table <b>" . Utility::get_short_file_by_path($table_path) . "</b> not exits,plese create it first");
        }
    }

    public function getList()
    {

        $db = Factory::getDBO();
        $query = $db->getQuery(true)
            ->select("*")
            ->from(self::getTableName());
        $limit_per_page=$this->get_limit_per_page();
        return $db->setQuery($query,0,$limit_per_page)->loadObjectList();

    }
    public function get_limit_per_page()
    {
        $appConfig=Factory::getAppConfig();
        return $appConfig->get('limit_per_page',20);
    }

    //them phan service
    public function savelist()
    {

        if ($this->input->post('list')) {
            $this->do_update($this->input->post('list'));
        }
    }

    public function do_update($list, $parent_id = 0, &$m_order = 0)
    {

        foreach ($list as $item) {

            $m_order++;
            $data = array(
                'parent_id' => $parent_id,
                'm_order' => $m_order,
            );
            if ($parent_id != $item['id']) {

                $where = array('id' => $item['id']);
                var_dump($data . ':' . $where);
                $this->db->where($where);
                $this->db->update('nav', $data);
            }
            if (array_key_exists("children", $item)) {
                $this->do_update($item["children"], $item["id"], $m_order);
            }
        }
    }
    //het them
}
