<?php


use SoftWay\CMS\Object\CMSObject;

class SoftWayView extends CMSObject
{
    public $view="";
    public $list="";
    public $header="";
    public $item="";
    public $filterForm;
    public $_defaultModel;
    public $form="";
    protected $_escape = 'htmlspecialchars';
    public function getModel($model=""){
        $model=$model?$model:$this->view;
        $model=ucfirst($model);
        $model_path=WOOBOOKING_PATH_COMPONENT."/models/$model.php";
        if(file_exists($model_path)){
            $model_name="{$model}Model";
            $model_class=WoobookingModel::getInstance($model);
            $model_class->model=$model;
            return $model_class;
        }else{
            throw new Exception("Error: model <b>$model_path</b> not exists, please create first");
        }

    }
    public function get($property, $default = null)
    {
        // If $model is null we use the default model
        if ($default === null)
        {
            $model = $this->_defaultModel;
        }
        else
        {
            $model = strtolower($default);
        }

        // First check to make sure the model requested exists
        if (isset($this->_models[$model]))
        {
            // Model exists, let's build the method name
            $method = 'get' . ucfirst($property);

            // Does the method exist?
            if (method_exists($this->_models[$model], $method))
            {
                // The method exists, let's call it and return what we get
                $result = $this->_models[$model]->$method();

                return $result;
            }
        }

        // Degrade to \JObject::get
        $result = parent::get($property, $default);

        return $result;
    }


    public function escape($var)
    {
        if (in_array($this->_escape, array('htmlspecialchars', 'htmlentities')))
        {
            return call_user_func($this->_escape, $var, ENT_COMPAT, $this->_charset);
        }

        return call_user_func($this->_escape, $var);
    }

    public static function goToLink($view_layout, $items_var=array()){

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


        $link=Factory::getRootUrl()."sellercenter/wb_$view/?layout=$layout".implode("&",$http_list_var);
        return $link;
    }
    public function redirect($url){
        $root_url=Factory::getRootUrl();

        $html = '<html><head>';
        $html .= '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
        $html .= '<script>document.location.href=' . json_encode(str_replace("'", '&apos;', $root_url.'/'.$url)) . ';</script>';
        $html .= '</head><body></body></html>';
        echo $html;
    }

    public static function frontendGoToLink($view_layout, $items_var=array()){

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


        $link=Factory::getRootUrl()."softwaycore-$view-$layout/?".implode("&",$http_list_var);
        return $link;
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

    public static function goToTaskLink($view,$task, $items_var=array()){


        $http_list_var=[];
        if(is_array($items_var)){
            foreach ($items_var as $key=> $value){
                $http_list_var[]="$key=$value";
            }
        }else{
            $http_list_var[]=  $items_var;
        }


        $link=Factory::getRootUrl()."sellercenter/wb_$view/?task=$view.$task".(is_array($items_var)&&count($items_var)?'&':null).implode("&",$http_list_var);
        return $link;
    }
    public function loadTemplate($tpl){
        $tmpl_short_path="/views/".$this->view."/tmpl/".$tpl.".php";

        $tmpl_path=WOOBOOKING_PATH_COMPONENT.$tmpl_short_path;

        if(file_exists($tmpl_path)){
            ob_start();
            require $tmpl_path;
            $content=ob_get_clean();
            echo  $content;
        }else{
            throw new Exception("Error:tpl <b>$tpl</b> not exits, please create it");
        }
    }
    public function loadSharedTemplate($template){
        list($template,$layout)=explode(".",$template);
        $tmpl_short_path="/shared/".$template."/$layout.php";

        $tmpl_path=WOOBOOKING_PATH_COMPONENT.$tmpl_short_path;

        if(file_exists($tmpl_path)){
            ob_start();
            require $tmpl_path;
            $content=ob_get_clean();
            echo  $content;
        }else{
            throw new Exception("Error:tpl <b>$tmpl_short_path</b> not exits, please create it");
        }
    }
    public function loadTemplateFromOtherView($view,$tpl){
        $tmpl_short_path="/views/".$view."/tmpl/".$tpl.".php";
        $tmpl_path=WOOBOOKING_PATH_COMPONENT.$tmpl_short_path;

        if(file_exists($tmpl_path)){
            ob_start();
            require $tmpl_path;
            $content=ob_get_clean();
            echo  $content;
        }else{
            throw new Exception("Error:tpl <b>$tpl</b> not exits, please create it");
        }
    }
    public function display($tpl){

        $tmpl_short_path="/views/".$this->view."/tmpl/".$tpl.".php";
        $tmpl_path=WOOBOOKING_PATH_COMPONENT.$tmpl_short_path;
        $open_source=Factory::getOpenSource();
        $appConfig=Factory::getAppConfig();
        $debug="";
        if(!$open_source->is_rest_api() && ($appConfig->get('debug_dev')==1 || $appConfig->get('debug_end_user') )) {
            ob_start();
            ?>
            <div class="wrapper-soft-way-debug">
                <h3>Debug</h3>
                <?php if($appConfig->get('debug_dev')==1){ ?>
                    <div class="dev-debug">
                        <h4>Dev debug</h4>
                        <div class="panel-group" id="dev-accordion" role="tablist" aria-multiselectable="true">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="dev-headingOne">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#dev-collapseOne"
                                           aria-expanded="true" aria-controls="dev-collapseOne">
                                            title debbug
                                        </a>
                                    </h4>
                                </div>
                                <div id="dev-collapseOne" class="panel-collapse collapse in" role="tabpanel"
                                     aria-labelledby="dev-headingOne">
                                    <div class="panel-body">
                                       content debug
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if($appConfig->get('debug_end_user')==1){ ?>
                    <div class="user-debug">
                        <h4>user debug</h4>
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="user-debug-headingOne">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#user-debug-accordion" href="#user-debug-collapseOne"
                                           aria-expanded="true" aria-controls="user-debug-collapseOne">
                                            title debug
                                        </a>
                                    </h4>
                                </div>
                                <div id="user-debug-collapseOne" class="panel-collapse collapse in" role="tabpanel"
                                     aria-labelledby="user-debug-headingOne">
                                    <div class="panel-body">
                                       content debug
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <?php
            $debug=ob_get_clean();
        }

        if(file_exists($tmpl_path)){
            ob_start();
            include $tmpl_path;
            $content=ob_get_clean();
            if(!trim($content)){
                $content="<div class='wrapper-soft-way'><p>layout <b>$tmpl_short_path</b> empty please add content to it</p>$debug</div>";
            }
            $content="<div class=\"wrapper-soft-way\">$content $debug</div>";
            $doc=Factory::getDocument();


            $headDocument=$doc->loadRenderer('head');
            $headDocument->render('head');
            return $content;
        }else{
            throw new Exception("Error:tpl <b>$tpl</b> not exits, please create it in view <b>".$this->view."</b>");
        }
    }
    public function display_block($block_id,$tpl){

        $tmpl_short_path="/views/".$this->view."/tmpl/".$tpl.".php";
        $tmpl_path=WOOBOOKING_PATH_COMPONENT.$tmpl_short_path;
        $open_source=Factory::getOpenSource();
        $debug="";
        if(!$open_source->is_rest_api()) {
            ob_start();
            ?>
            <div class="wrapper-soft-way-debug">
                <h3>Debug</h3>
                <div class="dev-debug">
                    <h4>Dev debug</h4>
                    <div class="panel-group" id="dev-accordion" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="dev-headingOne">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#dev-collapseOne"
                                       aria-expanded="true" aria-controls="dev-collapseOne">
                                        Collapsible Group Item #1
                                    </a>
                                </h4>
                            </div>
                            <div id="dev-collapseOne" class="panel-collapse collapse in" role="tabpanel"
                                 aria-labelledby="dev-headingOne">
                                <div class="panel-body">
                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry
                                    richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor
                                    brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt
                                    aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et.
                                    Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente
                                    ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer
                                    farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them
                                    accusamus labore sustainable VHS.
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="dev-headingTwo">
                                <h4 class="panel-title">
                                    <a class="dev-collapsed" role="button" data-toggle="collapse" data-parent="#dev-accordion"
                                       href="#dev-collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        Collapsible Group Item #2
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel"
                                 aria-labelledby="headingTwo">
                                <div class="panel-body">
                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry
                                    richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor
                                    brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt
                                    aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et.
                                    Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente
                                    ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer
                                    farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them
                                    accusamus labore sustainable VHS.
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="dev-headingThree">
                                <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#dev-accordion"
                                       href="#dev-collapseThree" aria-expanded="false" aria-controls="dev-collapseThree">
                                        Collapsible Group Item #3
                                    </a>
                                </h4>
                            </div>
                            <div id="dev-collapseThree" class="panel-collapse collapse" role="tabpanel"
                                 aria-labelledby="dev-headingThree">
                                <div class="panel-body">
                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry
                                    richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor
                                    brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt
                                    aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et.
                                    Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente
                                    ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer
                                    farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them
                                    accusamus labore sustainable VHS.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="user-debug">
                    <h4>user debug</h4>
                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="user-debug-headingOne">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#user-debug-accordion" href="#user-debug-collapseOne"
                                       aria-expanded="true" aria-controls="user-debug-collapseOne">
                                        Collapsible Group Item #1
                                    </a>
                                </h4>
                            </div>
                            <div id="user-debug-collapseOne" class="panel-collapse collapse in" role="tabpanel"
                                 aria-labelledby="user-debug-headingOne">
                                <div class="panel-body">
                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry
                                    richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor
                                    brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt
                                    aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et.
                                    Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente
                                    ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer
                                    farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them
                                    accusamus labore sustainable VHS.
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="user-debug-headingTwo">
                                <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#user-debug-accordion"
                                       href="#user-debug-collapseTwo" aria-expanded="false" aria-controls="user-debug-collapseTwo">
                                        Collapsible Group Item #2
                                    </a>
                                </h4>
                            </div>
                            <div id="user-debug-collapseTwo" class="panel-collapse collapse" role="tabpanel"
                                 aria-labelledby="user-debug-headingTwo">
                                <div class="panel-body">
                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry
                                    richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor
                                    brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt
                                    aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et.
                                    Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente
                                    ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer
                                    farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them
                                    accusamus labore sustainable VHS.
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="user-debug-headingThree">
                                <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#user-debug-accordion"
                                       href="#user-debug-collapseThree" aria-expanded="false" aria-controls="user-debug-collapseThree">
                                        Collapsible Group Item #3
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseThree" class="panel-collapse collapse" role="tabpanel"
                                 aria-labelledby="user-debug-headingThree">
                                <div class="panel-body">
                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry
                                    richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor
                                    brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt
                                    aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et.
                                    Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente
                                    ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer
                                    farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them
                                    accusamus labore sustainable VHS.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $debug=ob_get_clean();
        }

        if(file_exists($tmpl_path)){
            ob_start();
            include $tmpl_path;
            $content=ob_get_clean();
            if(!trim($content)){
                $content="<div class='wrapper-soft-way'><p>layout <b>$tmpl_short_path</b> empty please add content to it</p>$debug</div>";
            }
            $content="<div class=\"wrapper-soft-way\">$content $debug</div>";
            $doc=Factory::getDocument();


            $headDocument=$doc->loadRenderer('head');
            $headDocument->render('head');
            return $content;
        }else{
            throw new Exception("Error:tpl <b>$tpl</b> not exits, please create it in view <b>".$this->view."</b>");
        }
    }
    public function display_block_app($block,$tpl){

        $tmpl_short_path="/views/".$this->view."/tmpl/".$tpl.".php";
        $tmpl_path=WOOBOOKING_PATH_COMPONENT.$tmpl_short_path;
        $open_source=Factory::getOpenSource();
        $debug="";
        if(!$open_source->is_rest_api()) {
            ob_start();
            ?>
            <div class="wrapper-soft-way-debug">
                <h3>Debug</h3>
                <div class="dev-debug">
                    <h4>Dev debug</h4>
                    <div class="panel-group" id="dev-accordion" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="dev-headingOne">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#dev-collapseOne"
                                       aria-expanded="true" aria-controls="dev-collapseOne">
                                        Collapsible Group Item #1
                                    </a>
                                </h4>
                            </div>
                            <div id="dev-collapseOne" class="panel-collapse collapse in" role="tabpanel"
                                 aria-labelledby="dev-headingOne">
                                <div class="panel-body">
                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry
                                    richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor
                                    brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt
                                    aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et.
                                    Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente
                                    ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer
                                    farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them
                                    accusamus labore sustainable VHS.
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="dev-headingTwo">
                                <h4 class="panel-title">
                                    <a class="dev-collapsed" role="button" data-toggle="collapse" data-parent="#dev-accordion"
                                       href="#dev-collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        Collapsible Group Item #2
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel"
                                 aria-labelledby="headingTwo">
                                <div class="panel-body">
                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry
                                    richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor
                                    brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt
                                    aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et.
                                    Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente
                                    ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer
                                    farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them
                                    accusamus labore sustainable VHS.
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="dev-headingThree">
                                <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#dev-accordion"
                                       href="#dev-collapseThree" aria-expanded="false" aria-controls="dev-collapseThree">
                                        Collapsible Group Item #3
                                    </a>
                                </h4>
                            </div>
                            <div id="dev-collapseThree" class="panel-collapse collapse" role="tabpanel"
                                 aria-labelledby="dev-headingThree">
                                <div class="panel-body">
                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry
                                    richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor
                                    brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt
                                    aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et.
                                    Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente
                                    ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer
                                    farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them
                                    accusamus labore sustainable VHS.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="user-debug">
                    <h4>user debug</h4>
                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="user-debug-headingOne">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#user-debug-accordion" href="#user-debug-collapseOne"
                                       aria-expanded="true" aria-controls="user-debug-collapseOne">
                                        Collapsible Group Item #1
                                    </a>
                                </h4>
                            </div>
                            <div id="user-debug-collapseOne" class="panel-collapse collapse in" role="tabpanel"
                                 aria-labelledby="user-debug-headingOne">
                                <div class="panel-body">
                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry
                                    richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor
                                    brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt
                                    aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et.
                                    Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente
                                    ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer
                                    farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them
                                    accusamus labore sustainable VHS.
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="user-debug-headingTwo">
                                <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#user-debug-accordion"
                                       href="#user-debug-collapseTwo" aria-expanded="false" aria-controls="user-debug-collapseTwo">
                                        Collapsible Group Item #2
                                    </a>
                                </h4>
                            </div>
                            <div id="user-debug-collapseTwo" class="panel-collapse collapse" role="tabpanel"
                                 aria-labelledby="user-debug-headingTwo">
                                <div class="panel-body">
                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry
                                    richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor
                                    brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt
                                    aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et.
                                    Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente
                                    ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer
                                    farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them
                                    accusamus labore sustainable VHS.
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="user-debug-headingThree">
                                <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#user-debug-accordion"
                                       href="#user-debug-collapseThree" aria-expanded="false" aria-controls="user-debug-collapseThree">
                                        Collapsible Group Item #3
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseThree" class="panel-collapse collapse" role="tabpanel"
                                 aria-labelledby="user-debug-headingThree">
                                <div class="panel-body">
                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry
                                    richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor
                                    brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt
                                    aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et.
                                    Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente
                                    ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer
                                    farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them
                                    accusamus labore sustainable VHS.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $debug=ob_get_clean();
        }

        if(file_exists($tmpl_path)){
            ob_start();
            include $tmpl_path;
            $content=ob_get_clean();
            if(!trim($content)){
                $content="<div class='wrapper-soft-way'><p>layout <b>$tmpl_short_path</b> empty please add content to it</p>$debug</div>";
            }
            $content="<div class=\"wrapper-soft-way\">$content $debug</div>";
            $doc=Factory::getDocument();


            $headDocument=$doc->loadRenderer('head');
            $headDocument->render('head');
            return $content;
        }else{
            throw new Exception("Error:tpl <b>$tpl</b> not exits, please create it in view <b>".$this->view."</b>");
        }
    }
    public function displayList(){
        if(!$this->list){
            $model=$this->getModel();
            $this->list=$model->getList();
            $this->header=$model->getHeader();
        }
        $this->view="default";
        echo self::display("list");

    }
    public function displayForm(){
        if(!$this->form){
            $model=$this->getModel();
            $this->form=$model->getForm();
        }
        $this->view="default";
        echo self::display("form");
    }
}