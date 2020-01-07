<?php

use SoftWay\CMS\Log\Log;
use SoftWay\CMS\Utilities\Utility;

class softway_controller{
    public $model;
    public $modelItem="";
    public $modelList="";
    public static $instance;
    public $view=null;
    /*
     * first arg must layout path, else args is list
     */
    public function loadTemplate($tpl){
        $tmpl_short_path="/views/".$this->view."/tmpl/".$tpl.".php";
        $tmpl_path=SOFTWAY_PATH_APP.$tmpl_short_path;

        if(file_exists($tmpl_path)){
            ob_start();
            include $tmpl_path;
            $content=ob_get_clean();
            echo  $content;
        }else{
            throw new Exception("Error:tpl <b>$tpl</b> not exits, please create it");
        }
    }
    public function ajax_get_json_item($id=0){
        $modelItem=$this->getModelItem();
        return json_encode($modelItem->getItem());
    }
    public function ajax_get_item(){

        if(!$this->view){
            echo "please defined publish var <b>view</b> in file ",get_called_class();
            die;
        }

        return self::view($this->view.".form");
    }
    public function ajax_duplicate(){
        $input=Factory::getInput();

        $id=$input->getInt('id');
        $model=$this->getModel();
        $new_item=$model->duplicate($id);
        return $new_item;

    }
    public function ajax_delete(){
        $input=Factory::getInput();

        $id=$input->getInt('id');
        $model=$this->getModel();
        $new_item=$model->delete($id);
        return $new_item;

    }
    public function ajax_save($data_post =array())
    {
        $input=Factory::getInput();
        if(!count($data_post)){

            $data_post=$input->getArray($_POST);
        }
        $data_post=array_key_exists('data',$data_post)?$data_post['data']:$data_post;
        $data_post= $this->save($data_post);


        return $data_post;
    }
    public static function view($layout){
        list($view,$tpl)=explode(".",$layout);
        $view_path=SOFTWAY_PATH_APP."/views/$view/view.html.php";

        if(file_exists($view_path)){
            require_once $view_path;
            $UFCiew=ucfirst($view);
            $view_name="{$UFCiew}View";
            $view_class=new $view_name();
            $view_class->view=$view;
            return $view_class->display($tpl);
        }else{

            throw new Exception("Error: view <b>$view_path</b> not exists please create it");
        }
    }
    public static function display_block_app($block_id,$layout){
        $blockModel=SoftWayModel::getInstance('block');
        $block=$blockModel->getItem($block_id);
        list($view,$tpl)=explode(".",$layout);
        $view_path=SOFTWAY_PATH_APP."/views/$view/view.html.php";

        if(file_exists($view_path)){
            require_once $view_path;
            $UFCiew=ucfirst($view);
            $view_name="{$UFCiew}View";
            $view_class=new $view_name();
            $view_class->view=$view;
            return $view_class->display_block_app($block,$tpl);
        }else{

            throw new Exception("Error: view <b>$view_path</b> not exists please create it");
        }
    }
    public static function config_block($block_name, $config_layout){
        $block_view_path=SOFTWAY_PATH_APP_FRONT_END."/views/block/view.html.php";
        if(file_exists($block_view_path)){
            require_once $block_view_path;
            $view_class=new BlockView();
            $view_class->view="block";
            return $view_class->display('config');
        }else{

            throw new Exception("Error: view <b>$block_view_path</b> not exists please create it");
        }
    }
    public static function view_block_module($block_id,$block_name){
        $block_view_path=SOFTWAY_PATH_APP_FRONT_END."/views/block/view.html.php";
        if(file_exists($block_view_path)){
            require_once $block_view_path;
            $view_class=new BlockView();
            $view_class->view="block";

            return $view_class->display_block($block_id,$block_name);
        }else{

            throw new Exception("Error: view <b>$block_view_path</b> not exists please create it");
        }
    }
    public function getModel($model=""){
        if(!$model){
            $model=$this->model;
        }
        $Ucfmodel=ucfirst($model);
        $model_path=SOFTWAY_PATH_APP."/models/$Ucfmodel.php";
        $model_name="{$Ucfmodel}Model";
        if(file_exists($model_path)){
            require_once $model_path;
        }
        if(!class_exists($model_name)){
            $model_class=SoftWayModel::getInstance($model);
            $model_class->model=$model;
            return $model_class;
        }else{
            throw new Exception("Error model <b>$model_path</b> not exits, please create first");
        }

    }
    public function getModelItem($model=""){
        if(!$model){
            $model=$this->modelItem;
        }
        $UCModel=ucfirst($model);
        $model_path=SOFTWAY_PATH_APP."/models/$UCModel.php";
        if(file_exists($model_path)){
            require_once $model_path;
            $model_name="{$model}Model";
            $model_class=SoftWayModel::getInstance($model);
            $model_class->model=$model;
            return $model_class;
        }else{
            throw new Exception("Error model <b>$model_path</b> not exits, please create first");
        }

    }
    public function getModelList($model=""){
        if(!$model){
            $model=$this->modelList;
        }
        if(!$model){

            throw new Exception("please defined publish var modelList in file ".get_called_class());
        }
        $model_path=SOFTWAY_PATH_APP."/models/$model.php";
        if(file_exists($model_path)){
            require_once $model_path;
            $model_name="{$model}Model";
            $model_class=SoftWayModel::getInstance($model);
            $model_class->model=$model;
            return $model_class;
        }else{
            throw new Exception("Error model <b>$model</b> not exits, please create first");
        }


    }
    public static function ajax_action_task(){
        $input=Factory::getInput();
        $data = json_decode( file_get_contents('php://input') );
        $task=$input->getString('task','');
        $task=$task?$task:$data->task;
        list($controller,$task)=explode(".",$task);
        
        $file_controller_path=SOFTWAY_PATH_APP."/controllers/".ucfirst($controller).".php";
        $file_short_controller_path=Utility::get_short_file_by_path($file_controller_path);
        $response=new stdClass();
        if(file_exists($file_controller_path)){
            require_once $file_controller_path;
            $class_name=ucfirst($controller)."Controller";
            if(!class_exists($class_name)){
                $response->result="error";
                $response->msg="file $file_short_controller_path must has class $class_name, please check it";
                $response->data=null;
            }else{
                $class_controller=new $class_name();
                $class_controller->setModel($controller);
                if(method_exists ( $class_controller ,  $task ) ){
                    $data=call_user_func(array($class_controller, $task));
                    $doc=Factory::getDocument();
                    ob_start();
                    ?>
                    <script type="text/javascript">
                        less.registerStylesheetsImmediately();
                        console.log("less.sheets",less.sheets);
                        var sheets = [];
                        for(var i = less.sheets.length; i--; )
                            sheets.push(less.sheets[i].href);
                        less.refresh(1);

                        var fnImport = less.tree.Import;
                        less.tree.Import = function(path, imports)
                        {
                            path.value += '?x='+Math.random();
                            fnImport(path, imports);
                        }
                    </script>
                    <?php
                    $script = ob_get_clean();
                    
                    $script = Utility::remove_string_javascript($script);
                    $doc->addScriptDeclaration($script);

                    //$doc->addScript('admin/resources/js/less/less.min.js');
                    $response->scripts=$doc->getScripts();
                    $response->script=$doc->getScript();
                    $response->styleSheets=$doc->getStyleSheets();
                    $response->lessStyleSheets=$doc->getLessStyleSheets();
                    $response->style=$doc->getStyle();

                    if(is_array($data) || $data){
                        $response->result="success";
                        $response->data=$data;
                    }else{

                        $response->result="error";
                        $response->data=$data;
                    }
                }else{
                    $response->result="error";
                    $response->msg="class $class_name in file $file_short_controller_path can not found function(task) $task";
                    $response->data=null;
                }
            }

        }else{
            $response->result="error";
            $response->msg="cannot find file controller $file_short_controller_path not exists, please create it first";
            $response->data=null;
        }
        return json_encode($response);


    }
    public static function action_task(){
        $input=Factory::getInput();
        $data = json_decode( file_get_contents('php://input') );
        $task=$input->getString('task','');
        $task=$task?$task:$data->task;
        $app=Factory::getApplication();
        list($controller,$task)=explode(".",$task);
        
        $file_controller_path=SOFTWAY_PATH_APP."/controllers/".ucfirst($controller).".php";
        $file_short_controller_path=Utility::get_short_file_by_path($file_controller_path);
        $response=new stdClass();
        if(file_exists($file_controller_path)){
            require_once $file_controller_path;
            $class_name=ucfirst($controller)."Controller";
            if(!class_exists($class_name)){
                $app->enqueueMessage("file $file_short_controller_path must has class $class_name, please check it");
            }else{
                $class_controller=self::getInstance($controller);
                $class_controller->setModel($controller);
                if(method_exists ( $class_controller ,  $task ) ){
                    return call_user_func(array($class_controller, $task));
                }else{
                    $app->enqueueMessage("class $class_name in file $file_short_controller_path can not found function(task) $task");
                }
            }
        }else{
            $app->enqueueMessage("cannot find file controller $file_short_controller_path not exists, please create it first");
        }
    }

    /**
     * Method to get a singleton controller instance.
     *
     * @param $controller
     * @param array $config An array of optional constructor options.
     *
     * @return  \JControllerLegacy
     *
     * @since   3.0
     */
    public static function getInstance($controller, $config = array())
    {
        if (is_object(self::$instance[$controller]))
        {
            return self::$instance[$controller];
        }
        
        $file_controller_path=SOFTWAY_PATH_APP."/controllers/".ucfirst($controller).".php";
        $file_short_controller_path=Utility::get_short_file_by_path($file_controller_path);
        $response=new stdClass();
        if(file_exists($file_controller_path)){
            require_once $file_controller_path;
            $class_name=ucfirst($controller)."Controller";
            if(!class_exists($class_name)){
                $app->enqueueMessage("file $file_short_controller_path must has class $class_name, please check it");
            }else{
                self::$instance[$controller]=new $class_name();
            }
        }else{
            $app->enqueueMessage("cannot find file controller $file_short_controller_path not exists, please create it first");
        }

        return self::$instance[$controller];
    }


    public function setModel($model){
        $this->model=$model;
    }
    public function save($data=array()){

        $input=Factory::getInput();
        if(empty($data))
        {
            $data=$input->getArray($_POST)['data'];
        }


        if(empty($data))
        {
            $data=$input->getArray($_POST);
        }
        $model=SoftWayModel::getInstance($this->model);


        $data= $model->save($data);
        return $data;


    }
    public function save_v2($data=array()){
        $input=Factory::getInput();
        if(empty($data))
        {
            $data=$input->getData();
        }


        $model=$this->getModel();
        $data= $model->save($data);
        return $data;


    }
    public function delete($id=0){

        $input=Factory::getInput();
        if(!$id)
        {
            $id=$input->getInt('id',0);
        }
        $model=$this->getModel();
        return $model->delete($id);



    }
    public function duplicate($id=0){

        $input=Factory::getInput();
        if(!$id)
        {
            $id=$input->getInt('id',0);
        }
        $model=$this->getModel();
        return $model->duplicate($id);



    }
    public static function getFrontendLink($view_layout, $items_var=array()){

        list($view,$layout)=explode(".",$view_layout);
        $openSource=Factory::getOpenSource();
        $key_soft_way=$openSource->getKeySoftWay();
        $http_list_var=[];
        if(is_array($items_var)){
            foreach ($items_var as $key=> $value){
                $http_list_var[]="$key=$value";
            }
        }else{
            $http_list_var[]=  $items_var;
        }


        $link=Factory::getRootUrl()."sellercenter/softwaycore-$view-$layout/?".implode("&",$http_list_var);
        return $link;
    }

    public static function frontendGoToLink($view_layout, $items_var=array()){
        $root_url=Factory::getRootUrl();
        list($view,$layout)=explode(".",$view_layout);
        $openSource=Factory::getOpenSource();
        $key_soft_way=$openSource->getKeySoftWay();
        $http_list_var=[];
        if(is_array($items_var)){
            foreach ($items_var as $key=> $value){
                $http_list_var[]="$key=$value";
            }
        }else{
            $http_list_var[]=  $items_var;
        }
        $url="softwaycore-$view-$layout/?".implode("&",$http_list_var);
        $html = '<html><head>';
        $html .= '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
        $html .= '<script>document.location.href=' . json_encode(str_replace("'", '&apos;', $root_url.$url)) . ';</script>';
        $html .= '</head><body></body></html>';
        echo $html;

    }

    public function redirect($url){
        $root_url=Factory::getRootUrl();

        $html = '<html><head>';
        $html .= '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
        $html .= '<script>document.location.href=' . json_encode(str_replace("'", '&apos;', $root_url.'/'.$url)) . ';</script>';
        $html .= '</head><body></body></html>';
        echo $html;
    }
    public function redirectInSoftWayPage($page){
        $openSource=Factory::getOpenSource();
        $page=$openSource->get_stander_page_front_end($page);
        $root_url=Factory::getRootUrl();
        $html = '<html><head>';
        $html .= '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
        $html .= '<script>document.location.href=' . json_encode(str_replace("'", '&apos;', $root_url.'/sellercenter/'.$page)) . ';</script>';
        $html .= '</head><body></body></html>';
        echo $html;
    }
}