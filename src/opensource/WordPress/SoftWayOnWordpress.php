<?php


namespace SoftWay\CMS\OpenSource\WordPress;


use Exception;
use Factory;
use SoftWay\CMS\Filesystem\File;
use SoftWay\CMS\Form\Form;
use SoftWay\CMS\Html\HtmlFrontend;
use SoftWay\CMS\OpenSource\SoftWayOnOpenSource;
use SoftWay\CMS\Filesystem\Folder;
use SoftWay\CMS\OpenSource\WordPress\ECommerce\ECommerce;
use SoftWay\CMS;
use softway_controller;
use SoftWay\CMS\Html\Html;
use SoftWay\CMS\Utilities\Utility;
use WoobookingModel;
use WoobookingText;
use BlockController;





class SoftWayOnWordpress
{
    public static $instance = null;
    public static $items_submenus = null;
    public static $key_soft_way = "softwaycore";
    public static $version = "1.0";
    public static $prefix_link = "wb_";
    public static $namespace = "softway_api/1.0";
    private static $list_environment=array();
    public $view = "";
    public $ecommerce = null;
    public $scripts = array();
    public $script = array();
    public $plugin_name = 'softwaycore';

    public static function getInstance($new = false)
    {
        if (!is_object(self::$instance)) {
            self::$instance = new SoftWayOnWordpress();
            self::$instance->run();
        }

        return self::$instance;
    }

    private static function get_prefix_link (){

        return self::$prefix_link;
    }
    private static function get_true_menu_of_soft_way($menu)
    {
        return str_replace(self::$prefix_link,"",$menu);
    }

    public function __return_false()
    {
        return false;
    }
    public function getKeySoftWay(){
        return self::$key_soft_way;
    }
    function react2wp_woocommerce_hide_product_price($price)
    {
        return '';
    }

    public function my_action()
    {
        $input = Factory::getInput();
        $data = $input->getData();
        echo "sdfsdfds";
        die;
        $modelBooking = WoobookingModel::getInstance('booking');
        $modelBooking->add_to_cart($data);

        ?>
        <script type="text/javascript">
            window.location.href = "http://localhost/softway2/cart/";
        </script>
        <?php

    }
    public function render_content($content){
        echo esc_html($content);
    }

    public function initOpenSoftWayWooPanelBackend(){



        $app=Factory::getApplication();
        $root_url = self::get_root_url();
        Factory::setRootUrl($root_url);
        $user=Factory::getUser();


        $listMenuWooPanel = self::getListMenuWooPanel();
        foreach ($listMenuWooPanel as $menu) {
            add_filter("softway_dashboard_{$menu}_endpoint",array($this,"softway_dashboard_softway_endpoint"));
        }

        Factory::setRootUrlPlugin($root_url . "/wp-content/plugins/softwaycore/");


        if ($app->getClient() == 1 && !in_array($this->view, $listMenuWooPanel)) {

            return;
        }

        if ($app->getClient() == 1) {
            add_action('softway_enqueue_scripts', array($this, 'softway_enqueue_scripts'), 99999, 1);
        } else {


        }

        add_action('wp_print_scripts', array($this,'softway_dashboard_softway_frontend_shapeSpace_print_scripts'));
        $prefix_link=self::$prefix_link;
        //hook api
        add_action('rest_api_init', array($this, 'softway_register_rest_route'));


    }

    public  function softway_dashboard_softway_endpoint(){

        if(!self::checkInstalled()){
            self::goToPopupInstall();
        }
        Html::_('jquery.tooltip');
        Html::_('jquery.bootstrap');
        $root_url = self::get_root_url();
        $input=Factory::getInput();
        $data=$input->getData();
        $task=array_key_exists('task',$data)?$data['task']:null;
        $layout=array_key_exists('layout',$data)?$data['layout']:null;
        $layout=$layout?$layout:"list";

        if($task){

            echo softway_controller::action_task();
        }else {


            $menu = $this->get_current_page();
            $menu = self::get_true_menu_of_soft_way($menu);
            $file_controller_path = WOOBOOKING_PATH_COMPONENT . "/controllers/" . ucfirst($menu) . ".php";

            $file_controller_short_path = Utility::get_short_file_by_path($file_controller_path);
            if (file_exists($file_controller_path)) {
                require_once $file_controller_path;
                $class_name = ucfirst($menu) . "Controller";

                if (class_exists($class_name)) {
                    $class_controller = new $class_name();
                    echo $class_controller->view("$menu.$layout");
                } else {
                    echo "Class $class_name not exit in file $file_controller_short_path, please create this class";
                }
            } else {

                echo "File controller not found,please create file $file_controller_short_path";
            }
        }

    }
    //add script
    public function add_script_footer($scripts=array()){
        $this->scripts=$scripts;
        add_action('wp_footer', array($this, 'wp_hook_add_script_footer'));
        add_action('admin_footer', array($this, 'wp_hook_add_script_footer'));
    }
    public function add_script_content_footer($script){
        $this->script=$script;
        add_action('wp_footer', array($this, 'wp_hook_add_script_content_footer'));
        add_action('admin_footer', array($this, 'wp_hook_add_script_content_footer'));
    }
    public function getSession(){

        if(!session_id()) {
            session_start();
        }
        $wp_session = $_SESSION;
        return $wp_session;
    }

    public function getECommerceOrderDetail($order_id)
    {
        $order = wc_get_order( $order_id );
        return $order;
    }

    public function initOpenSoftWayWordpressFrontend(){

        $root_url = self::get_root_url();
        $input = Factory::getInput();
        Factory::setRootUrl($root_url);
        Factory::setRootUrlPlugin($root_url . "/wp-content/plugins/softwaycore/");

        add_action('wp_print_scripts', array($this,'frontend_shapeSpace_print_scripts'));
        /**
         * Plugin Name: Test Plugin
         * Author: John Doe
         * Version: 1.0.0
         */




        $task = $input->getString('task', '');
        add_action('wp_enqueue_scripts', array($this, 'softway_enqueue_scripts'), 99999, 1);

        //trying remove add to cart and price
        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
        //add_filter( 'woocommerce_is_purchasable',array($this,'__return_false'));

        add_filter('woocommerce_get_price_html', array($this, 'react2wp_woocommerce_hide_product_price'));


        add_action('wp_login', array($this,'wp_login'));
        add_action('wp_logout', array($this,'wp_logout'));

        //end remove add to cart and price

        add_action('woocommerce_after_single_product_summary', array($this, 'woocommerce_after_single_product_summary'), 10, 0);


        // add action when booking order
        add_action('woocommerce_checkout_create_order', array($this, 'softway_checkout_create_order'), 20, 2);

        $list_view=self::get_list_layout_view_frontend();


        foreach ($list_view as $key=> $view){
            $a_key=self::$key_soft_way."-".$key;
            add_shortcode( $a_key, array($this,'soft_way_render_by_tag_func') );
        }
        $list_view=self::get_list_layout_block_frontend();

        foreach ($list_view as $key=> $view){
            $a_key=self::$key_soft_way."-block-".$key;
            add_shortcode( $a_key, array($this,'soft_way_render_block_by_tag_func') );
        }


        if (!$task) {

        } else {

            list($controller, $task) = explode(".", $task);
            $file_controller_path = WOOBOOKING_PATH_COMPONENT . "/controllers/" . ucfirst($controller) . ".php";
            $file_controller_short_path = Utility::get_short_file_by_path($file_controller_path);
            $file_short_controller_path = Utility::get_short_file_by_path($file_controller_path);
            require_once $file_controller_path;
            $class_name = ucfirst($controller) . "Controller";
            if (file_exists($file_controller_path)) {
                if (class_exists($class_name)) {
                    $class_controller = new $class_name();
                    if (method_exists($class_controller, $task)) {
                        //not return
                        call_user_func(array($class_controller, $task));
                    }
                } else {
                    echo "class $class_name in file $file_short_controller_path can not found function(task) $task";
                }

            } else {
                echo "class $class_name not exit in file $file_controller_short_path, please create this class";
            }
        }
        //hook api
        add_action('rest_api_init', array($this, 'softway_register_rest_route'));


        //TODO làm đăng ký khi người dùng active plugin

        //register_deactivation_hook( __FILE__,  array( $this, 'pluginprefix_deactivation' )  );
        //register_activation_hook( __FILE__, 'pluginprefix_deactivation' );

        //dang ky router


    }

    public static  function wp_login($user_login) {
        $user = get_user_by('login',$user_login);
        $userModel=WoobookingModel::getInstance('user');
        $open_source_user_id=$user->__get('id');
        $data=$user->to_array();
        $user=$userModel->getUserByOpenSourceUserId($open_source_user_id);
        if(!$user){
            $date_user=array(
                'open_source_user_id'=>$open_source_user_id,
                'first_name'=>$data['user_nicename'],
                'last_name'=>"",
                'email'=>$data['user_email'],
                'created'=>$data['user_registered'],
                'published'=>$data['user_status'],
            );
            $user=$userModel->save($date_user);
        }
        $session=Factory::getSession();
        $session->set('user',$user);
    }
    public static  function wp_logout() {

        $session=Factory::getSession();
        $session->set('user',null);
    }

    public  static function get_stander_page_front_end($page ) {
        return self::getKeySoftWay()."-$page";
    }
    function bl_new_demo_route_callback( ) {
        return "Congrats! Your demo callback is fully functional. Now make it do something fancy";
    }

    public static function checkInstalled(){
        $app=Factory::getApplication();
        $db=Factory::getDBO();
        $list_database_table=$db->setQuery("SHOW TABLES LIKE ".$db->quote("softwaycore%"))->loadColumn();
        $json_table=File::read(EASY_BK_APPOINTMENT_PATH_ROOT."/install/tables.json");
        $json_table=json_decode($json_table);
        $installed=true;
        foreach ($json_table as $table){
            if(!in_array($table,$list_database_table)){
                $installed=false;
                break;
            }
        }
        return $installed;
    }
    public static function is_backend_wordpress(){
        return is_admin();
    }
    public function run()
    {


        $this->view = self::get_current_page();
        $app = Factory::getApplication();
        $input=Factory::getInput();
        add_filter('softway_query_var_filter', array($this, 'db_appointments'), 20, 1);
        add_filter('softway_navigation_items', array($this, 'softway_add_appointment'), 10, 1);
        if ($app->getClient() == 1) {
            if(self::is_backend_wordpress()){
                $this->initWordpressBackend();
            }else{
                $this->initOpenSoftWayWooPanelBackend();
            }
        }else{
            $this->initOpenSoftWayWordpressFrontend();
            $this->ecommerce=ECommerce::getInstance();
        }



    }
    function start_session() {
        if(!session_id()) {
            session_start();
        }

    }
    function getEcommerce( ) {
        return $this->ecommerce;
    }
    function softway_block_category( $categories, $post ) {

        return array_merge(
            $categories,
            array(
                array(
                    'slug' => 'softwaycore-block',
                    'title' => __( 'Woo booking block', 'softwaycore-block' ),
                ),
            )
        );
    }
    function add_script_admin_wordpress() {
        $doc=Factory::getDocument();
        echo '<script type="text/javascript" src="'.Factory::getRootUrlPlugin() .'admin/resources/js/less/less.min.js"></script>';
        //echo '<script type="text/javascript" src="'.Factory::getRootUrlPlugin() .'lib/SoftWay/opensource/WordPress/blocks.build.js"></script>';
    }
    public  function initWordpressBackend(){


        $root_url = self::get_root_url();
        Factory::setRootUrl($root_url);
        $input = Factory::getInput();
        $doc=Factory::getDocument();

        add_action('admin_head', array($this,'admin_wordpress_shapeSpace_print_scripts'));
        $doc->addScript('admin/sw_apps/softway_core/assets/js/soft_way_debug.js');


        Html::_('jquery.loading_js');
        $doc->addScript('admin/resources/js/drawer-master/js/hy-drawer.js');
        $doc->addScript('admin/resources/js/less/less.min.js');
        $doc->addScript('admin/resources/js/jquery-validation/dist/jquery.validate.js');
        $doc->addScript('admin/resources/js/jquery-confirm-master/dist/jquery-confirm.min.js');
        $doc->addScript('admin/resources/js/Bootstrap-Loading/src/waitingfor.js');
        $doc->addScript('admin/resources/js/jquery.form/jquery.form.js');
        $doc->addScript('admin/resources/js/form-serializeObject/jquery.serializeObject.js');
        $doc->addScript('admin/resources/js/form-serializeObject/jquery.serializeToJSON.js');
        $doc->addScript('admin/sw_apps/softway_core/assets/js/main_script.js');
        $doc->addLessStyleSheet('admin/sw_apps/softway_core/assets/less/main_style.less');
        $doc->addStyleSheet('admin/resources/js/drawer-master/css/style.css');
        Html::_('jquery.tooltip');
        Html::_('jquery.bootstrap');

        $doc->addStyleSheet('admin/resources/js/drawer-master/css/style.css');
        $doc->addStyleSheet('admin/resources/js/jquery-confirm-master/dist/jquery-confirm.min.css');
        $doc->addScript('admin/resources/js/autoNumeric/autoNumeric.js');
        $doc->addStyleSheet('admin/resources/js/fontawesome-free-5.11.2/css/all.min.css');

        if(!self::is_rest_api()) {
            Html::_('jquery.less');
        }
        Html::_('jquery.fontawesome');
        Html::_('jquery.confirm');
        Html::_('jquery.serialize_object');
        Html::_('jquery.bootstrap');
        $doc->addLessStyleSheet('sw_apps/softway_core/assets/less/main_style_backend_wordpress.less');
        Factory::setRootUrlPlugin($root_url . "/wp-content/plugins/softwaycore/");
        //$list_view=self::get_list_layout_view_frontend();
        if ( !function_exists( 'wp_add_inline_script' ) ) {
            require_once ABSPATH . WPINC . '/functions.wp-scripts.php';
        }


        // inline script via wp_print_scripts

        add_action('wp_print_scripts', array($this,'shapeSpace_print_scripts'));




        add_action('admin_footer', array($this,'add_script_admin_wordpress'));
        add_filter( 'block_categories', array($this,'softway_block_category'), 10, 2);


        /* wp_update_nav_menu_item(23, 0, array('menu-item-title' => 'About',
             'menu-item-object' => 'page',
             'menu-item-object-id' => get_page_by_path('about')->ID,
             'menu-item-type' => 'post_type',
             'menu-item-status' => 'publish'));*/

        // Registering the block
        /* foreach ($list_view as $key=> $view) {
             register_block_type("softwaycore/$key", array(
                 'render_callback' => [$this, 'render_last_posts'],
             ));
         }*/
        add_action('admin_init', array($this, 'add_nav_menu_meta_boxes'));
        //add admin menu
        add_action('admin_menu', array($this,'softway_plugin_setup_menu'));

        // [bartag foo="foo-value"]




        add_action( 'vc_before_init', array($this,'your_name_integrateWithVC') );
        if(function_exists("vc_add_shortcode_param"))
        {
            vc_add_shortcode_param( 'soft_way_block_type', array($this,'soft_way_block_type_settings_field') );
        }


        //vc_add_shortcode_param('my_param', 'my_param_settings_field', plugins_url('test.js', __FILE__));




    }
    function softway_plugin_setup_menu(){
        $list_view_admin=self::get_list_view_for_woo_panel();
        $first_view=array_shift($list_view_admin);
        $first_view=(object)$first_view;
        $menu_slug=str_replace('_','-',$first_view->menu_slug);
        add_menu_page( 'Woobooking', 'SoftWay', 'manage_options', 'softwaycore-plugin',array($this,'softway_page') );
        foreach ($list_view_admin as $key=> $view) {
            $view=(object)$view;
            add_submenu_page( 'softwaycore-plugin', $view->label,  $view->label, 'manage_options', $view->menu_slug, array($this,'softway_page'));
        }


    }
    function softway_page(){
        $input=Factory::getInput();
        $page=$input->getString('page','');
        if(!self::checkInstalled()){
            self::goToPopupInstall();
        }
        Html::_('jquery.tooltip');
        Html::_('jquery.bootstrap');
        $root_url = self::get_root_url();
        $input=Factory::getInput();
        $data=$input->getData();
        $task=array_key_exists('task',$data)?$data['task']:null;
        $layout=array_key_exists('layout',$data)?$data['layout']:null;
        $layout=$layout?$layout:"list";

        if($task){

            echo softway_controller::action_task();
        }else {
            $menu = self::get_true_menu_of_soft_way($page);
            $file_controller_path = WOOBOOKING_PATH_COMPONENT . "/controllers/" . ucfirst($menu) . ".php";
            $file_controller_short_path = Utility::get_short_file_by_path($file_controller_path);
            if (file_exists($file_controller_path)) {
                require_once $file_controller_path;
                $class_name = ucfirst($menu) . "Controller";

                if (class_exists($class_name)) {
                    $class_controller = new $class_name();
                    echo $class_controller->view("$menu.$layout");
                } else {
                    echo "Class $class_name not exit in file $file_controller_short_path, please create this class";
                }
            } else {

                echo "File controller not found,please create file $file_controller_short_path";
            }
        }
    }
    function soft_way_block_type_settings_field( $settings, $value ) {
        ob_start();
        ?>
        <div data-type="<?php  esc_attr( $settings['type'] ) ?>" class="soft-way-block-edit-content"   >
            <div class="row">
                <div class="col-md-12">
                    <div class="block-content" style="text-align: center">
                        <svg version="1.1" id="L2" style="width: 100px;height: 100px;" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                             viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
<circle fill="none" stroke="#a00" stroke-width="4" stroke-miterlimit="10" cx="50" cy="50" r="48"/>
                            <line fill="none" stroke-linecap="round" stroke="#a00" stroke-width="4" stroke-miterlimit="10" x1="50" y1="50" x2="85" y2="50.5">
                                <animateTransform
                                        attributeName="transform"
                                        dur="2s"
                                        type="rotate"
                                        from="0 50 50"
                                        to="360 50 50"
                                        repeatCount="indefinite" />
                            </line>
                            <line fill="none" stroke-linecap="round" stroke="#a00" stroke-width="4" stroke-miterlimit="10" x1="50" y1="50" x2="49.5" y2="74">
                                <animateTransform
                                        attributeName="transform"
                                        dur="15s"
                                        type="rotate"
                                        from="0 50 50"
                                        to="360 50 50"
                                        repeatCount="indefinite" />
                            </line>
</svg>


                    </div>
                </div>
            </div>

            <input name="<?php echo  esc_attr( $settings["param_name"] )  ?>"
                   class="wpb_vc_param_value wpb-textinput  <?php echo esc_attr( $settings['param_name'] ) ?> <?php  esc_attr( $settings['type'] ) ?>_field"
                   type="hidden" value="<?php echo esc_attr( $value ) ?>" />
            <script type="text/javascript">
                $('.soft-way-block-edit-content').render_block_config({
                    id:"<?php echo $value ?>",
                    block_setting:<?php echo json_encode($settings) ?>
                });
            </script>
        </div>
        <?php
        return ob_get_clean();
    }


    function softway_dashboard_softway_frontend_shapeSpace_print_scripts() {
        $root_url = self::get_root_url();
        ?>
        <script type="text/javascript">
            root_url = "<?php echo $root_url ?>";
            current_url = "<?php echo $root_url.'sellercenter/'.$this->view ?>";
            root_url_plugin = "<?php echo $root_url ?>/wp-content/plugins/softwaycore/";
            api_task = "/wp-json/<?php echo self::$namespace . self::get_api_task() ?>";
        </script>
        <?php
    }
    function frontend_shapeSpace_print_scripts() {
        $root_url = self::get_root_url();
        ?>
        <script type="text/javascript">
            root_url = "<?php echo $root_url ?>";
            root_url_plugin = "<?php echo $root_url ?>/wp-content/plugins/softwaycore/";
            api_task = "/wp-json/<?php echo self::$namespace . self::get_api_task() ?>";
        </script>
        <?php
    }
    function admin_wordpress_shapeSpace_print_scripts() {
        $root_url = self::get_root_url();
        ?>
        <script type="text/javascript">
            root_url = "<?php echo $root_url ?>";
            current_url = "<?php echo $root_url ?>";
            root_url_plugin = "<?php echo $root_url ?>/wp-content/plugins/softwaycore/";
            api_task = "/wp-json/<?php echo self::$namespace . self::get_api_task() ?>";
        </script>
        <?php
    }
    protected  static  function  get_list_layout_view_frontend() {
        $views_path=WOOBOOKING_PATH_COMPONENT_FRONT_END."/views";

        $list_view=array();
        $folders=Folder::folders($views_path);


        foreach ($folders as $view){
            $view_path=$views_path."/$view";
            if(!Folder::exists($view_path."/tmpl"))
                continue;
            $files=Folder::files($view_path."/tmpl",".xml");


            foreach ($files as $file){

                $xmlFile = pathinfo($file);
                $filename=$xmlFile['filename'];
                $file_path=$view_path."/tmpl/$file";
                $title="";
                $xml = simplexml_load_file($file_path);

                try {
                    $title=@(string)($xml->layout->attributes())['title'];
                }catch(Exception $e) {
                    echo "please check file tructor xml";
                    die;
                }
                $title=WoobookingText::_($title);
                if(!$title){
                    continue;
                }
                $list_view["$view-$filename"]=array(
                    "title"=>$title
                );


            }
        }
        return $list_view;
    }
    protected  static  function  get_list_layout_block_frontend() {
        $blocks_path=EASY_BK_APPOINTMENT_PATH_ROOT."/blocks";

        $list_block=array();
        $folders=Folder::folders($blocks_path);


        foreach ($folders as $block){
            $block_path=$blocks_path."/$block";
            $file_config_block=str_replace("block_","",$block);
            $file=$block_path."/$file_config_block.xml";
            if(!File::exists($file))
                continue;
            $xml = simplexml_load_file($file);

            try {
                $title=(string)($xml->layout->attributes())['title'];
            }catch(Exception $e) {
                echo "please check file tructor xml";
                die;
            }
            $title=WoobookingText::_($title);
            $list_block["$file_config_block"]=array(
                "title"=>$title
            );
        }
        return $list_block;
    }
    protected  static  function  get_list_view_backend() {
        $views_path=WOOBOOKING_PATH_COMPONENT."/views";
        $list_view=array();
        $folders=CMS\Filesystem\Folder::folders($views_path);
        foreach ($folders as $view){
            $view_path=$views_path."/$view";

            if(!Folder::exists($view_path."/tmpl"))
                continue;
            $files=Folder::files($view_path."/tmpl",".xml");
            foreach ($files as $file){

                $xmlFile = pathinfo($file);
                $filename=$xmlFile['filename'];
                $file_path=$view_path."/tmpl/$file";
                $title="";
                $xml = simplexml_load_file($file_path);
                try {
                    $title=(string)($xml->layout->attributes())['title'];
                }catch(Exception $e) {
                    echo "please check file tructor xml";
                    die;
                }
                $title=WoobookingText::_($title);
                $list_view["$view-$filename"]=array(
                    "title"=>$title
                );


            }
        }
        return $list_view;
    }
    function shapeSpace_print_scripts() {
        $list_view=self::get_list_layout_view_frontend();
        $root_url = self::get_root_url();
        ?>

        <script type="text/javascript">
            root_url = "<?php echo $root_url ?>";
            root_url_plugin = "<?php echo $root_url ?>/wp-content/plugins/softwaycore/";
            api_task = "/wp-json/<?php echo self::$namespace . self::get_api_task() ?>";
            list_view=<?php echo json_encode($list_view) ?>
        </script>

        <?php

    }
    function soft_way_render_by_tag_func( $atts,$content, $a_view ) {
        if($a_view !="softwaycore-install-form"  && !self::checkInstalled()){
            self::goToPopupInstall();
        }

        $input=Factory::getInput();
        $type=null;
        if(is_array($atts) && $id=reset($atts)){
            list($package,$view,$layout)=explode("-",$a_view);
            echo   softway_controller::display_block_app($id,"$view.$layout");
        }else{
            list($package,$view,$layout)=explode("-",$a_view);
            echo   softway_controller::view("$view.$layout");
        }
    }
    function goToPopupInstall( ) {
        $root_url=Factory::getRootUrl();
        $html = '<html><head>';
        $html .= '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
        $html .= '<script>document.location.href=' . json_encode(str_replace("'", '&apos;', $root_url.'/softwaycore-install-form')) . ';</script>';
        $html .= '</head><body></body></html>';
        echo $html;
    }
    function soft_way_render_block_by_tag_func( $atts,$content, $a_view ) {
        if(!self::checkInstalled()){
            self::goToPopupInstall();
        }
        require_once WOOBOOKING_PATH_COMPONENT_FRONT_END."/controllers/Block.php";
        $input=Factory::getInput();
        if(is_array($atts) && $id=reset($atts)){

            list($package,$block,$block_name)=explode("-",$a_view);
            echo   BlockController::view_block_module($id,$block_name);
        }
        return false;
    }

    function your_name_integrateWithVC() {
        $list_view=self::get_list_layout_view_frontend();
        foreach ($list_view as $key=> $value){
            $a_key=self::$key_soft_way."-".$key;
            vc_map( array(
                "name" => __( $value['title'], "my-text-domain" ),
                "base" => $a_key,
                "class" => "",
                'admin_enqueue_js' => array(plugins_url('render_view_config.js', __FILE__)),
                "category" => __( "Woo Booking", "my-text-domain"),
                "params" => array(
                    array(
                        "type" => "soft_way_block_type",
                        "holder" => "div",
                        "class" => "",
                        "param_name" => $a_key,
                        "value" => '',
                    ),
                )
            ) );

        }

        $list_layout_block=self::get_list_layout_block_frontend();
        foreach ($list_layout_block as $key=> $value){
            $a_key=self::$key_soft_way."-block-".$key;
            vc_map( array(
                "name" => __( "Block ".$value['title'], "my-text-domain" ),
                "base" => $a_key,
                "class" => "",
                'admin_enqueue_js' => array(plugins_url('render_block_config.js', __FILE__)),
                "category" => __( "Woo Booking block", "my-text-domain"),
                "params" => array(
                    array(
                        "type" => "soft_way_block_type",
                        "holder" => "div",
                        "class" => "",
                        "param_name" => $a_key,
                        "value" => '',
                    ),
                )
            ) );

        }
    }


    function render_last_posts( $attributes, $content ) {
        $input=Factory::getInput();
        $open_source_client_id=$attributes['open_source_client_id'];
        $modelBlock=WoobookingModel::getInstance('block');
        $block=$modelBlock->getItem($open_source_client_id);
        $params=$block->params;

        $type=$block->type;
        $data_param=$params->toArray();
        foreach ($data_param as $key=>$value){
            $input->set($key,$value);
        }
        if(!$type){

        }else {

            list($view, $layout) = explode("-", $type);
            echo softway_controller::view("$view.$layout");
        }


    }
    public static function pluginprefix_activation(){

        $list_page=SoftWayOnWordpress::get_list_layout_view_frontend();
        $key_soft_way=self::$key_soft_way;
        foreach ($list_page as $k => $page) {
            $key_page="$key_soft_way-$k";
            // Create post object
            $my_post = array(
                'post_name'     => $key_page,
                'post_title'    => $page['title'],
                'post_content'  => "[$key_page]",
                'post_status'   => "publish",
                'post_author'   => get_current_user_id(),
                'post_type'     => "page",
            );
            $page_check = get_page_by_path($key_page);


            if(!isset($page_check->ID)){

                wp_insert_post( $my_post, '' );
            }

        }
        return true;
    }

    public function add_nav_menu_meta_boxes() {
        add_meta_box(
            'wl_login_nav_link',
            __('Woo booking menu item'),
            array( $this, 'nav_menu_link'),
            'nav-menus',
            'side',
            'low'
        );
    }

    public function nav_menu_link() {?>
        <?php
        $list_page=self::get_list_layout_view_frontend();
        $key_soft_way=self::$key_soft_way;
        ?>
        <div id="posttype-wl-login" class="posttypediv">
            <div id="tabs-panel-wishlist-login" class="tabs-panel tabs-panel-active">
                <ul id ="wishlist-login-checklist" class="categorychecklist form-no-clear">
                    <?php foreach ($list_page as $key=>$page){ ?>
                        <li>
                            <label class="menu-item-title">
                                <input type="checkbox" class="menu-item-checkbox" name="menu-item[-1][menu-item-object-id]" value="-1"> <?php echo $page['title'] ?>
                            </label>
                            <input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]" value="custom">
                            <input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]" value="<?php echo $page['title'] ?>">
                            <input type="hidden" class="menu-item-url" name="menu-item[-1][menu-item-url]" value="<?php bloginfo('wpurl'); ?>/<?php echo "$key_soft_way-$key" ?>">
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <p class="button-controls">
        			<span class="list-controls">
        				<a href="/wordpress/wp-admin/nav-menus.php?page-tab=all&amp;selectall=1#posttype-page" class="select-all">Select All</a>
        			</span>
                <span class="add-to-menu">
        				<input type="submit" class="button-secondary submit-add-to-menu right" value="Add to Menu" name="add-post-type-menu-item" id="submit-posttype-wl-login">
        				<span class="spinner"></span>
        			</span>
            </p>
        </div>
    <?php }



    /**
     * CALLBACK
     *
     * Render callback for the dynamic block.
     *
     * Instead of rendering from the block's save(), this callback will render the front-end
     *
     * @since    1.0.0
     * @param $att Attributes from the JS block
     * @return string Rendered HTML
     */
    public function block_dynamic_render_cb ( $att ) {
        // Coming from RichText, each line is an array's element
        $sum = $att['number1'][0] + $att['number2'][0];
        $html = "<h1>$sum</h1>";
        return $html;
    }


    public static function is_rest_api()
    {
        $request_uri = $_SERVER['REQUEST_URI'];
        if ((strpos($request_uri, 'wp-json/') !== false) || (strpos($request_uri, 'wc-ajax') !== false) ) {
            return true;
        }
        return false;
    }

    function get_current_page()
    {
        $request_uri = $_SERVER['REQUEST_URI'];
        $view = "";
        $listMenu = self::getListMenuWooPanel();
        if (self::is_rest_api()) {
            foreach ($listMenu as $menu) {
                if (strpos($request_uri, self::$namespace . "/$menu") !== false) {
                    $view = $menu;
                    break;
                }
            }
        } else {
            foreach ($listMenu as $menu) {
                if (strpos($request_uri, 'sellercenter/' . $menu) !== false) {
                    $view = $menu;
                    break;
                }
            }
        }


        return $view;

    }


    public function woocommerce_after_single_product_summary()
    {

        $product = wc_get_product();
        $id = $product->get_id();
        $input = Factory::getInput();
        $app = Factory::getApplication();

        $file_controller_path = WOOBOOKING_PATH_COMPONENT . "/controllers/Booking.php";
        require_once $file_controller_path;
        $class_name = "BookingController";
        $class_controller = new $class_name();
        $input->set('open_source_link_id', $id);
        echo $class_controller->view("booking.training");

    }
    //TODO chú ý sửa cái này trước khi đẩy live
    public static function set_environments($list_environment){
        self::$list_environment=$list_environment;
    }
    public function get_root_url()
    {
        $config =Factory::getConfig();
        $list_environment=self::$list_environment;

        $uri=Factory::getUri();
        $live_site=$config->get('live_site',"");
        $current_running=$uri->toString(array('scheme', 'user', 'pass', 'host', 'port','path'));

        $root_url="";
        foreach ($list_environment as $environment) {
            if ($current_running == "http://demo9.cmsmart.net") {
                return $live_site;
            } elseif (strpos($current_running, $environment) !== false) {

                $root_url = $environment;
                break;
            }
        }
        return $root_url;
    }

    public static function get_api_task()
    {
        $prefix_link=self::get_prefix_link();
        $app = Factory::getApplication();
        if ($app->getClient() == 1) {
            return "/{$prefix_link}db_appointments/task";
        } else {
            return "/nbsoftway/task";
        }

    }

    function softway_register_rest_route()
    {
        $view = self::get_current_page();
        //root/wp-json/softway_api/1.0/db_appointments/task //post
        register_rest_route(
            self::$namespace,
            self::get_api_task(),
            array(
                'methods' => 'POST',
                'callback' => array('softway_controller', 'ajax_action_task'),
            )
        );


        $listMenu = self::getListMenuWooPanel();
        foreach ($listMenu as $menu) {
            $menu=self::get_true_menu_of_soft_way($menu);
            $file_api_path = WOOBOOKING_PATH_COMPONENT . "/api/Api{$menu}.php";
            if (file_exists($file_api_path)) {
                require_once $file_api_path;
                $class_name = "Api{$menu}";
                new $class_name();
            }

        }


    }

    //for softwaycore admin
    function softway_enqueue_scripts()
    {
        $app = Factory::getApplication();
        $doc = Factory::getDocument();
        wp_enqueue_media();
        $doc->addScript('admin/sw_apps/softway_core/assets/js/soft_way_debug.js');

        if ($app->getClient() == 1) {

            Html::_('jquery.loading_js');
            $doc->addScript('admin/resources/js/drawer-master/js/hy-drawer.js');
            $doc->addScript('admin/resources/js/less/less.min.js');
            $doc->addScript('admin/resources/js/jquery-validation/dist/jquery.validate.js');
            $doc->addScript('admin/resources/js/jquery-confirm-master/dist/jquery-confirm.min.js');
            $doc->addScript('admin/resources/js/Bootstrap-Loading/src/waitingfor.js');
            $doc->addScript('admin/resources/js/jquery.form/jquery.form.js');
            $doc->addScript('admin/resources/js/form-serializeObject/jquery.serializeObject.js');
            $doc->addScript('admin/resources/js/form-serializeObject/jquery.serializeToJSON.js');
            $doc->addScript('admin/sw_apps/softway_core/assets/js/main_script.js');
            $doc->addLessStyleSheet('admin/sw_apps/softway_core/assets/less/main_style.less');
            $doc->addStyleSheet('admin/resources/js/drawer-master/css/style.css');
            Html::_('jquery.tooltip');
            Html::_('jquery.bootstrap');

            $doc->addStyleSheet('admin/resources/js/drawer-master/css/style.css');
            $doc->addStyleSheet('admin/resources/js/jquery-confirm-master/dist/jquery-confirm.min.css');
            $doc->addScript('admin/resources/js/autoNumeric/autoNumeric.js');
            $doc->addStyleSheet('admin/resources/js/fontawesome-free-5.11.2/css/all.min.css');
        } else {
            HtmlFrontend::_('jquery.loading_js');
            $doc->addScript('resources/js/less/less.min.js');
            $doc->addScript('resources/js/autoNumeric/autoNumeric.js');
            $doc->addScript('resources/js/Bootstrap-Loading/src/waitingfor.js');

            HtmlFrontend::_('jquery.bootstrap');

            $doc->addScript('sw_apps/softway_core/assets/js/main_script.js');
            $doc->addLessStyleSheet('sw_apps/softway_core/assets/less/main_style.less');
            $doc->addStyleSheet('resources/js/fontawesome-free-5.11.2/css/all.min.css');

        }
    }
    public static function get_list_view_for_woo_panel(){
        if(empty(static::$items_submenus)){
            $list_menu_by_xml=self::get_list_view_xml();
            $confingModel=WoobookingModel::getInstance('config');
            $list_view=$confingModel->get_list_view_publish();
            $items_submenus=array();
            $index=21;


            foreach ($list_menu_by_xml as $view){
                if( $view->is_system || in_array($view->menu_slug,$list_view)) {
                    $items_submenus[] = array(
                        'id' => self::$prefix_link . $view->id,
                        'menu_slug' => self::$prefix_link . $view->menu_slug,
                        'label' => $view->label,
                        'page_title' => $view->page_title,
                        'capability' => $view->capability,
                        'icon' => $view->icon,
                    );

                    $index++;
                }

            }
            self::$items_submenus=$items_submenus;
        }


        return self::$items_submenus;
    }




    //TODO sẽ phải định  nghĩ lại menu
    public static function getListMenuWooPanel()
    {
        $list_view_admin=self::get_list_view_for_woo_panel();


        $list_menu=array();
        foreach ($list_view_admin as $view){
            $list_menu[]=$view['id'];
        }

        return $list_menu;
    }
    public function wp_hook_add_script_footer(){
        foreach ($this->scripts as $src => $attribs) {
            if(strpos($src,'http')!==false){
                echo '<script type="text/javascript" src="'.$src.'"></script>';
            }else
                echo '<script type="text/javascript" src="'.Factory::getRootUrlPlugin() .$src.'"></script>';
        }

    }
    public function wp_hook_add_script_content_footer($script){
        foreach ($this->script as $attribs=>$content ) {
            ?>
            <script type="text/javascript">
                <?php echo $content ?>
            </script>
            <?php
        }

    }

    //Hiển thị các page my-plugin1
    function db_appointments($arr_query)
    {
        $arr_query_new = self::getListMenuWooPanel();
        $arr_query = array_merge($arr_query, $arr_query_new);
        return $arr_query;
    }
    public static $list_menu_by_xml=array();
    public static function get_list_view_xml(){

        if(empty(self::$list_menu_by_xml)){
            $file_xml_path_app=WOOBOOKING_PATH_ADMIN_COMPONENT1."/views.xml";
            $xml = simplexml_load_file($file_xml_path_app);


            $list_menu_by_xml=array();
            foreach ($xml->view as $view){
                $list_menu_by_xml[]=(object)array(
                    'id' => (string)($view->attributes())['id'],
                    'menu_slug' => (string)($view->attributes())['menu_slug'],
                    'label' => (string)($view->attributes())['label'],
                    'page_title' => (string)($view->attributes())['page_title'],
                    'capability' => (string)($view->attributes())['capability'],
                    'icon' => (string)($view->attributes())['icon'],
                    'class' => (string)($view->attributes())['class'],
                    'is_system' => (boolean)($view->attributes())['is_system'],

                );


            }
            self::$list_menu_by_xml=$list_menu_by_xml;
        }
        return self::$list_menu_by_xml;
    }
    function softway_add_appointment($output_menus)
    {


        global $softway_submenus;
        $list_item = array();
        foreach ($output_menus as $item) {
            $list_item[] = $item;
        }
        $output_menus = $list_item;


        $softway_submenus['db_appointments'] = self::get_list_view_for_woo_panel();


        $db_appointments = array(
            'id' => self::$prefix_link.'db_appointments',
            'menu_slug' => self::$prefix_link.'db_appointments',
            'menu_title' => __('Woobooking'),
            'capability' => '',
            'page_title' => '',
            'icon' => 'flaticon-line-graph',
            'classes' => '',
            'submenu' => $softway_submenus['db_appointments']
        );

        array_splice($output_menus, 2, 0, array($db_appointments));




        return $output_menus;

    }
}
