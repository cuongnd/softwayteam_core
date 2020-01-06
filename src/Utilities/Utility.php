<?php
/**
 * softwaycore! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace SoftWay\CMS\Utilities;

use SoftWay\CMS\Filesystem\File;
use SoftWay\CMS\Crypt\Crypt;
use DOMDocument;
use Factory;
use JDatabaseDriver;
use Log;
use ReflectionClass;
use stdClass;

defined('_SOFT_WAY_EXEC') or die;

/**
 * JUtility is a utility functions class
 *
 * @since  1.7.0
 */
class Utility
{
	/**
	 * Method to extract key/value pairs out of a string with XML style attributes
	 *
	 * @param   string  $string  String containing XML style attributes
	 *
	 * @return  array  Key/Value pairs for the attributes
	 *
	 * @since   1.7.0
	 */
	public static function parseAttributes($string)
	{
		$attr = array();
		$retarray = array();

		// Let's grab all the key/value pairs using a regular expression
		preg_match_all('/([\w:-]+)[\s]?=[\s]?"([^"]*)"/i', $string, $attr);

		if (is_array($attr))
		{
			$numPairs = count($attr[1]);

			for ($i = 0; $i < $numPairs; $i++)
			{
				$retarray[$attr[1][$i]] = $attr[2][$i];
			}
		}

		return $retarray;
	}
    public static function get_short_file_by_path($file_path){

        return str_replace(WOOBOOKING_PATH_ROOT.'/','',$file_path);
    }
    public static function  get_path_file($file_path){
        return str_replace(WOOBOOKING_PATH_ROOT,"",$file_path);
    }
    public static function redirect($url){
        $root_url=Factory::getRootUrl();
        $html = '<html><head>';
        $html .= '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
        $html .= '<script>document.location.href=' . json_encode(str_replace("'", '&apos;',$url)) . ';</script>';
        $html .= '</head><body></body></html>';
        echo $html;
    }
    public static function getFrontendGoToLink($view_layout, $items_var=array()){

        list($view,$layout)=explode(".",$view_layout);
        $openSource=Factory::getOpenSource();
        $key_woo_booking=$openSource->getKeySoftWay();
        $http_list_var=[];
        if(is_array($items_var)){
            foreach ($items_var as $key=> $value){
                $http_list_var[]="$key=$value";
            }
        }else{
            $http_list_var[]=  $items_var;
        }
        $http_string_var=implode("&",$http_list_var);
        $link=Factory::getRootUrl()."softwaycore-$view-$layout/".($http_string_var!=""?"?$http_string_var":'');
        return $link;
    }

	/**
	 * Method to get the maximum allowed file size for the HTTP uploads based on the active PHP configuration
	 *
	 * @param   mixed  $custom  A custom upper limit, if the PHP settings are all above this then this will be used
	 *
	 * @return  integer  Size in number of bytes
	 *
	 * @since   3.7.0
	 */
	public static function getMaxUploadSize($custom = null)
	{
		if ($custom)
		{
			$custom = Html::_('number.bytes', $custom, '');

			if ($custom > 0)
			{
				$sizes[] = $custom;
			}
		}

		/*
		 * Read INI settings which affects upload size limits
		 * and Convert each into number of bytes so that we can compare
		 */
		$sizes[] = Html::_('number.bytes', ini_get('post_max_size'), '');
		$sizes[] = Html::_('number.bytes', ini_get('upload_max_filesize'), '');

		// The minimum of these is the limiting factor
		return min($sizes);
	}
	public static function clean($string) {
        return preg_replace('/[^a-zA-Z0-9_ -]/s','',$string);
    }
	public static function clean_all_space_to_single_space($string) {
        return preg_replace('/\s+/', ' ', $string);
    }
    public static function remove_string_javascript($str)
    {
        preg_match_all('/<script type=\"text\/javascript">(.*?)<\/script>/s', $str, $estimates);
        return $estimates[1][0];
    }
    public static function remove_string_style_sheet($str)
    {
        preg_match_all('/<style type=\"text\/css">(.*?)<\/style>/s', $str, $estimates);
        return $estimates[1][0];
    }
        public static function getFullName($first_name, $last_name)
    {
        return "$first_name $last_name";
    }

    public static function printDebugBacktrace($title = 'Debug Backtrace:')
    {
        
        $stacks = debug_backtrace();
        ob_start();
        ?>
        <hr/>
        <h3><?php echo $title ?></h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>File</th>
                    <th>Line</th>
                    <th>Class</th>
                    <th>type</th>
                    <th>Function</th>
                    <th>Line content</th>
                    <th>args</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stacks as $_stack) { ?>
                <?php
                $line_content="";
                $args=$_stack['args'];
                $line=$_stack['line'];
                 if (!isset($_stack['file']))
                     $file_path_short = '[PHP Kernel]';
                 else
                     {
                         $file_full_path=$_stack["file"];
                         $file_path_short=self::get_short_file_by_path($file_full_path);
                         $line_content=File::readLine($file_full_path,$line);


                     }

                if (!isset($_stack['line']))
                    $_stack['line'] = '';
                ?>
                <tr>
                    <td><?php echo $file_path_short ?></td>
                    <td><?php echo $_stack['line'] ?></td>
                    <td><?php echo $_stack['class'] ?></td>
                    <td><?php echo $_stack['type'] ?></td>
                    <td><?php echo $_stack['function'] ?></td>
                    <td><?php echo  $line_content; ?></td>
                    <td><pre><?php  print_r($args,false); ?></pre></td>
                </tr>
                <?php } ?>
            </tbody>
        <?php
        $content=ob_get_clean();
        return $content;
    }
    public static function assign_type_var(&$item,$table){
        $db=Factory::getDbo();
        $fields=$db->getTableColumns($table);
        $int=['int','int unsigned','smallint unsigned','tinyint','smallint','bigint'];
        $float=['decimal','float','decimal, unsigned','decimal,'];
        foreach ($fields as $field=>$type){
            if(isset($item->$field) && in_array($type,$int)){
                $item->$field=(int)$item->$field;
            }elseif(isset($item->$field) && in_array($type,$float)){
                $item->$field=(float)$item->$field;
            }
        }
        return $item;

    }
    public static function assign_type_var_and_remove_out_site_key_table($item,$table){
        $db=Factory::getDbo();
        $fields=$db->getTableColumns($table);
        $int=['int','int unsigned','smallint unsigned','tinyint','smallint','bigint'];
        $float=['decimal','float','decimal, unsigned','decimal,'];
        foreach ($fields as $field=>$type){
            if(isset($item->$field) && in_array($type,$int)){
                $item->$field=(int)$item->$field;
            }elseif(isset($item->$field) && in_array($type,$float)){
                $item->$field=(float)$item->$field;
            }
        }
        foreach ($item as $key=>&$value){
            if(!isset($fields[$key])){
                unset($item->$key);
            }
        }
        return $item;

    }
    public static function remove_out_site_key_table(&$item,$table){
        $db=Factory::getDbo();
        $fields=$db->getTableColumns($table);
        foreach ($item as $key=>$value){
            if(!array_key_exists($key,$fields)){
                unset($item->$key);
            }
        }
        return $item;
    }
    public static function add_javascript_language($list_key){
        $list_language = array();
        foreach ($list_key as $key){
            $list_language[$key] = WoobookingText::_($key);
        }
        $doc = Factory::getDocument();
        ob_start();
        ?>
        <script type="text/javascript">
            var temp_lang=<?php echo json_encode($list_language)?>;
            for (var key in temp_lang) {
                // skip loop if the property is from prototype
                if (!temp_lang.hasOwnProperty(key)) continue;
                list_language[key]=temp_lang[key];
            }
        </script>
        <?php
        $js_content = ob_get_clean();
        $js_content = WoobookingUtility::remove_string_javascript($js_content);
        $doc->addScriptDeclaration($js_content);
    }
    public static function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
    public static function random_string_by_time(){
	    //Generate a timestamp using mt_rand.
        $timestamp = mt_rand(1, time());

        //Format that timestamp into a readable date string.
        $randomDate = date("d M Y", $timestamp);

        //Print it out.
        return $randomDate;
    }
    // function to geocode address, it will return false if unable to geocode address
    function geocode($address){

        // url encode the address
        $address = urlencode($address);

        // google map geocode api url
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyBE32zhVjZxVoUiuXrXhsFRea2bF9r6WcU";
        // get the json response
        $resp_json = file_get_contents($url);

        // decode the json
        $resp = json_decode($resp_json, true);

        // response status will be 'OK', if able to geocode given address
        if($resp['status']=='OK'){

            // get the important data
            $lati = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
            $longi = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";
            $formatted_address = isset($resp['results'][0]['formatted_address']) ? $resp['results'][0]['formatted_address'] : "";

            // verify if data is complete
            if($lati && $longi && $formatted_address){

                // put the data in the array
                $data_arr = array();

                array_push(
                    $data_arr,
                    $lati,
                    $longi,
                    $formatted_address
                );

                return $data_arr;

            }else{
                return false;
            }

        }

        else{
            echo "<strong>ERROR: {$resp['status']}</strong>";
            return false;
        }
    }
    public static function remove_string_between($string, $start, $end){
        $remove_string=self::get_string_between($string, $start, $end);
        $string=str_replace($start.$remove_string.$end,'',$string);
        return $string;
    }


    public static function getQuery($query){
        $config=Factory::getConfig();
        return str_replace('#__',$config->get('dbprefix'),$query);
    }
    static function  getCurl($link = '', $curlopt_ssl_verifypeer = false, $curlopt_ssl_verifyhost = false, $curlopt_encoding = 'gzip', $curlopt_returntransfer = true)
    {
        if ($link == '')
            return;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $curlopt_ssl_verifypeer);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $curlopt_ssl_verifyhost);
        curl_setopt($ch, CURLOPT_ENCODING, $curlopt_encoding);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $curlopt_returntransfer);
        $result = curl_exec($ch);
        return $result;
    }
    static function  getCurlChat($link = '', $curlopt_ssl_verifypeer = false, $curlopt_ssl_verifyhost = false, $curlopt_encoding = 'gzip', $curlopt_returntransfer = true)
    {
        if ($link == '')
            return;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $curlopt_ssl_verifypeer);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $curlopt_ssl_verifyhost);
        curl_setopt($ch, CURLOPT_ENCODING, $curlopt_encoding);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_URL, $link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $curlopt_returntransfer);
        $result = curl_exec($ch);
        return $result;
    }
    public static function get_user_debug(){
        require_once WOOBOOKING_PATH_ROOT.DS."components/com_tools/helpers/tools.php";
        $session=Factory::getSession();
        $key_user_debug=ToolsHelpersTools::USER_DEBUG;
        $user_debug=$session->get($key_user_debug,0);
        $input=Factory::getApplication()->input;
        $debug=$input->getString('dg','');
        if($debug==1){
            return true;
        }
        return $user_debug;
    }
    public static function get_data_cache_by_query($group,JDatabaseDriver $db,$function){
        //call_user_func_array(array($foo, "bar"), array("three", "four"));
        $query=$db->getQuery();
        $cache = Factory::getCache($group,'callback');
        if($cache->setCaching())
        {
            $data=$cache->get(array($db,$function),null, md5($query), false);
        }else{
            $data=call_user_func_array(array($db, $function));
        }
        return $data;
    }
    public static function highlightQuery($query)
    {
        $query=self::getQuery($query);
        $newlineKeywords = '#\b(FROM|LEFT|INNER|OUTER|WHERE|SET|VALUES|ORDER|GROUP|HAVING|LIMIT|ON|AND|CASE)\b#i';
        $query = htmlspecialchars($query, ENT_QUOTES);
        $query = preg_replace($newlineKeywords, '<br />&#160;&#160;\\0', $query);
        $regex = array(
            // Tables are identified by the prefix.
            '/(=)/' => '<b class="dbg-operator">$1</b>',
            // All uppercase words have a special meaning.
            '/(?<!\w|>)([A-Z_]{2,})(?!\w)/x' => '<span class="dbg-command">$1</span>',
            // Tables are identified by the prefix.
            '/(' . Factory::getDbo()->getPrefix() . '[a-z_0-9]+)/' => '<span class="dbg-table">$1</span>'
        );
        $query = preg_replace(array_keys($regex), array_values($regex), $query);
        $query = str_replace('*', '<b style="color: red;">*</b>', $query);
        return $query;
    }
    public static function is_call_from_app(){
        $input=Factory::getApplication()->input;
        $get_page_config_app=$input->getInt('get_page_config_app',0);
        return $get_page_config_app==1;
    }
    /**
     * @return mixed|string
     */
    public static function getOS() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $os_platform  = "Unknown OS Platform";
        $os_array     = array(
            '/windows nt 10/i'      =>  'Windows 10',
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile'
        );
        foreach ($os_array as $regex => $value)
            if (preg_match($regex, $user_agent))
                $os_platform = $value;
        return $os_platform;
    }
    /**
     * @return mixed|string
     */
    public static function  getBrowser() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $browser        = "Unknown Browser";
        $browser_array = array(
            '/msie/i'      => 'Internet Explorer',
            '/firefox/i'   => 'Firefox',
            '/safari/i'    => 'Safari',
            '/chrome/i'    => 'Chrome',
            '/edge/i'      => 'Edge',
            '/opera/i'     => 'Opera',
            '/netscape/i'  => 'Netscape',
            '/maxthon/i'   => 'Maxthon',
            '/konqueror/i' => 'Konqueror',
            '/mobile/i'    => 'Handheld Browser'
        );
        foreach ($browser_array as $regex => $value)
            if (preg_match($regex, $user_agent))
                $browser = $value;
        return $browser;
    }
    public static function write_compress_js($file_js, $compress_file)
    {
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/Minify.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/JS.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/Exception.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/path-converter-master/src/ConverterInterface.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/path-converter-master/src/Converter.php';
        $minifier = new Minify\JS(WOOBOOKING_PATH_ROOT . DS . $file_js);
        $js_compress_content=$minifier->minify();
        JFile::write(WOOBOOKING_PATH_ROOT . DS . $compress_file, $js_compress_content);
    }
    public static function closure_compiler_js_by_url_js($js_file, $compress_file)
    {
        return;
        $data = array('apikey' => 'b4b8w3PnSid7pt7p3c42fX3ruKGXP4h28KA6NZtpHXw8Q',
            'url' => JUri::root().$js_file);
        $curl = curl_init("https://api.dotmaui.com/client/1.0/jsmin/");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($curl);
        curl_close($curl);
        $output1=strtolower($output);
        if (strpos($output1, 'api quota exceeded') !== false) {
            echo JUri::root().$js_file;
            echo "</br>";
            echo JUri::root().$compress_file;
            echo "</br>";
            echo $output;
            echo "</br>";
        }else{
            echo JUri::root().$js_file;
            echo "</br>";
            echo JUri::root().$compress_file;
            echo "</br>";
            echo "----ok-----";
            echo substr($output,0,150);
            echo "</br>";
            JFile::write(WOOBOOKING_PATH_ROOT.DS.$compress_file, $output);
        }
        echo "<hr/>";
    }
    public static function write_compress_css($file_css, $compress_css_file)
    {
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/Minify.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/Exception.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/Exceptions/BasicException.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/Exceptions/FileImportException.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/CSS.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/Exception.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/path-converter-master/src/ConverterInterface.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/path-converter-master/src/Converter.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/path-converter-master/src/NoConverter.php';
        $minifier = new Minify\CSS($file_css);
        JFile::write( $compress_css_file, $minifier->minify());
    }
    public static function write_compress_css_by_content($css_content, $compress_css_file)
    {
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/Minify.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/Exception.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/Exceptions/BasicException.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/Exceptions/IOException.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/Exceptions/BasicException.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/Exceptions/FileImportException.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/CSS.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/minify-master/src/Exception.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/path-converter-master/src/ConverterInterface.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/path-converter-master/src/Converter.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/path-converter-master/src/NoConverter.php';
        $minifier = new Minify\CSS();
        $minifier->add($css_content);
        $css_content=$minifier->minify();
        JFile::write( $compress_css_file, $css_content);
    }

    public static function gen_random_string($length = 8)
    {
        $salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $base = strlen($salt);
        $makepass = '';
        /*
         * Start with a cryptographic strength random string, then convert it to
         * a string with the numeric base of the salt.
         * Shift the base conversion on each character so the character
         * distribution is even, and randomize the start shift so it's not
         * predictable.
         */
        $random = Crypt::genRandomBytes($length + 1);
        $shift = ord($random[0]);
        for ($i = 1; $i <= $length; ++$i) {
            $makepass .= $salt[($shift + ord($random[$i])) % $base];
            $shift += ord($random[$i]);
        }
        return $makepass;
    }
    public static function gen_random_integer($length = 8)
    {
        $salt = "0123456789";
        $base = strlen($salt);
        $makepass = '';
        /*
         * Start with a cryptographic strength random string, then convert it to
         * a string with the numeric base of the salt.
         * Shift the base conversion on each character so the character
         * distribution is even, and randomize the start shift so it's not
         * predictable.
         */
        $random = Crypt::genRandomBytes($length + 1);
        $shift = ord($random[0]);
        for ($i = 1; $i <= $length; ++$i) {
            $makepass .= $salt[($shift + ord($random[$i])) % $base];
            $shift += ord($random[$i]);
        }
        return $makepass;
    }
    public static function write_log_time($title)
    {
        $end_end = microtime(true);
        $total_time = number_format($end_end - TIME_START, 3);
        Log::add("$title:$total_time second");
    }

    public static function html_minify($data)
    {
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/minify-2.x/min/lib/Minify/CommentPreserver.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/minify-2.x/min/lib/Minify/HTML.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/minify-2.x/min/lib/Minify/CSS.php';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/minifyjscss/minify-2.x/min/lib/JSMin.php';
        $buffer = Minify_HTML::minify($data, array(
            'cssMinifier' => array('Minify_CSS', 'minify'),
            'jsMinifier' => array('JSMin', 'minify')
        ));
        return $buffer;
    }
    public static function html_to_obj($html,$style)
    {
        $dom = new DOMDocument();
        $html  = mb_convert_encoding($html , 'HTML-ENTITIES', 'UTF-8'); // require mb_string
        $dom->loadHTML($html);
        return WoobookingUtility::element_to_obj($dom->documentElement,$style,"");
    }
    function element_to_obj($element,$style,$class_path) {
        $debug=WoobookingUtility::get_debug();
        $obj = array( "tag" => $element->tagName );
        foreach ($element->attributes as $attribute) {
            if(strtolower($attribute->name)=="class"){
                $obj["class_name"] = $attribute->value;
            }else{
                $obj[$attribute->name] = $attribute->value;
            }
        }
        if($obj["class_name"])
        {
            $array_class_name=explode(" ",$obj["class_name"]);
            $current_class=$obj['tag'].".".implode('.',$array_class_name);
        }else{
            $current_class=$obj['tag'];
        }
        $class_path.=$class_path?" ".$current_class:$current_class;
        //echo"<br/>";
        //echo"------------------".$obj['tag']."---------------------";
        //echo"<br/>";
        //echo"-------class path-----------".$class_path."---------------------";
        //echo"<br/>";
        //echo"---------------------------------------";
        //echo"<br/>";
        $check_apply_class=function($function_call_back,&$apply_direct_class_path,$key_path,$class_path,$level=0){
            //echo"<br/>";
            //echo"-------level-----------".$level."---------------------";
            //echo"<br/>";
            //echo"<br/>";
            //echo"-------key_path-----------".$key_path."---------------------";
            //echo"<br/>";
            //echo"<br/>";
            //echo"-------class_path-----------".$class_path."---------------------";
            //echo"<br/>";
            $array_key_path=explode(" ",$key_path);
            $array_class_path=explode(" ",$class_path);
            //echo"<br/>";
            //echo"-------array_class_path--------------------------------";
            //echo"<pre>";
            //print_r($array_class_path,false);
            //echo"</pre>";
            //echo"<br/>";
            //echo"-------------------kiem tra phan tu dau tien cua key_path |".reset($array_key_path)."|($key_path) xem co o trong class_path |".$class_path."| hay khong ---------------";
            //echo"<br/>";
            //echo"<br/>";
            //echo"-------------------neu co thi lay ra mot duong dan moi va key moi---------------";
            //echo"<br/>";
            $first_item_array_key_path=reset($array_key_path);
            $first_item_array_key_path=explode(".",$first_item_array_key_path);
            foreach($first_item_array_key_path as $key=> $item_array_first_key_path){
                if(!$first_item_array_key_path[$key]){
                    unset($first_item_array_key_path[$key]);
                }
            }
            $first_item_array_key_path=array_values($first_item_array_key_path);
            $include_tag=substr(reset($array_key_path),0,1)!=".";
            for($k=0;$k<count($first_item_array_key_path);$k++){
                if($k==0 && !$include_tag){
                    $first_item_array_key_path[$k] = "." . $first_item_array_key_path[$k];
                }
                if($k>0){
                    $first_item_array_key_path[$k] = "." . $first_item_array_key_path[$k];
                }
            }
            //echo"-------phan tu dau tien trong key_path--------------------------------";
            //echo"<br/>";
            //echo"-------first_item_array_key_path--------------------------------";
            //echo"<pre>";
            //print_r($first_item_array_key_path,false);
            //echo"</pre>";
            //echo"<br/>";
            //echo"-------end array_class_path--------------------------------";
            //echo"<br/>";
            //echo"-------bat dau tinh toan--------------------------------";
            $index_of_array_class_path=0;
            $check_first=false;
            for($i=0;$i<count($array_class_path);$i++){
                //echo"<br/>";
                //echo"-------vong ---".$i."-----------------------------";
                //echo"<br/>";
                $item_class_path=$array_class_path[$i];
                $array_item_class_path=explode(".",$item_class_path);
                foreach($array_item_class_path as $key=> $item_array_item_class_path){
                    if(!$array_item_class_path[$key]){
                        unset($array_item_class_path[$key]);
                    }
                }
                $array_item_class_path=array_values($array_item_class_path);
                $a_include_tag=substr($item_class_path,0,1)!=".";
                for($j=0;$j<count($array_item_class_path);$j++){
                    if($j==0 && !$a_include_tag){
                        $array_item_class_path[$j] = "." . $array_item_class_path[$j];
                    }
                    if($j>0){
                        $array_item_class_path[$j] = "." . $array_item_class_path[$j];
                    }
                }
                //echo"<br/>";
                //echo"-------phan tu thu ".$i." cua class_path ---".$item_class_path."-----------------------------";
                //echo"<br/>";
                $is_found=true;
                for($m=0;$m<count($first_item_array_key_path);$m++){
                    $current_first_item_array_key_path=$first_item_array_key_path[$m];
                    //echo"<br/>";
                    //echo"-------first_item_array_key_path--- index --$m--------------$current_first_item_array_key_path-------------";
                    //echo"<br/>";
                    //echo"-------array_item_class_path---------------------------";
                    //echo"<pre>";
                    //print_r($array_item_class_path,false);
                    //echo"</pre>";
                    //echo"<br/>";
                    if(!in_array($current_first_item_array_key_path,$array_item_class_path))
                    {
                        $is_found=false;
                        //echo"-------khong ----$current_first_item_array_key_path-----trong--------".implode(".",$array_item_class_path)."----------";
                        break;
                    }else{
                        //echo"-------co ----$current_first_item_array_key_path-----trong--------".implode(".",$array_item_class_path)."----------";
                    }
                }
                if($is_found){
                    //echo"<br/>";
                    //echo"-------kiem tra duoc---item_array_key_path----|".$array_key_path[$j]."|-------------------------";
                    //echo"<br/>";
                    //echo"-------co trong --item_class_path--|".$item_class_path."|($class_path)-------------------------";
                    //echo"<br/>";
                    $index_of_array_class_path=$i;
                    $check_first=true;
                    break;
                }
                //echo"-------end item_array_key_path--------------------------------";
                //echo"<br/>";
                //echo"-------end level-----------".$level."---------------------";
                //echo"<br/>";
            }
            $class_path1=array_slice($array_class_path,$index_of_array_class_path+1);
            $key_path1=array_slice($array_key_path,1);
            //echo"<pre>";
            //echo"<br/>";
            //echo"-------key_path1---------------------";
            //echo"<br/>";
            //print_r($key_path1,false);
            //echo"<br/>";
            //echo"-------class_path1-------------------";
            //echo"<br/>";
            //print_r($class_path1,false);
            //echo"</pre>";
            if($check_first && (empty($key_path1)|| empty($class_path1) )){
                if(empty($key_path1)){
                    if(empty($class_path1)){
                        //echo"<br/>";
                        //echo"-------co ton tai va la class app dung truc tiep----$key_path-------trong ------$class_path------dung tinh toan va ap dung key_path nay---------";
                        //echo"<br/>";
                        $apply_direct_class_path=true;
                    }else{
                        //echo"<br/>";
                        //echo"-------co ton tai----$key_path-------trong ------$class_path------dung tinh toan va ap dung key_path nay---------";
                        //echo"<br/>";
                        $apply_direct_class_path=false;
                    }
                    return true;
                }
                if(empty($class_path1)){
                    //echo"<br/>";
                    //echo"-------co ton tai----$key_path-------trong ------$class_path------dung tinh toan va khong apdung---------do---class_path1---bi trong---";
                    //echo"<br/>";
                    return false;
                }
                return true;
            }elseif($check_first && !empty($class_path1)){
                $class_path1=implode(" ",$class_path1);
                $key_path1=implode(" ",$key_path1);
                $level1=$level+1;
                //echo"-------co ton tai----$key_path-------trong ------$class_path------nhung van phai tinh toan---------";
                return $function_call_back($function_call_back,$apply_direct_class_path,$key_path1,$class_path1,$level1);
            }else{
                //echo"-------khong ton tai----$key_path-------trong ------$class_path------dung tinh toan---------";
                return false;
            }
        };
        $list_apply_class=array();
        $list_apply_direct_class_path=array();
        foreach($style as $key_path=> $item_style){
            $apply_direct_class_path=false;
            if($check_apply_class($check_apply_class,$apply_direct_class_path,$key_path,$class_path)){
                $list_apply_class[]=$key_path;
                if($apply_direct_class_path){
                    $list_apply_direct_class_path[]=$key_path;
                }
            }
            //echo"<hr/>";
        }
        usort($list_apply_class, function ($item1, $item2) {
            $key1 =preg_split('/\s+/', $item1);
            $key2 = preg_split('/\s+/', $item2);
            return count($key1) >= count($key2);
        });
        $obj["apply_class"]=$list_apply_class;
        $obj["apply_direct_class_path"]=$list_apply_direct_class_path;
        //echo"<br/>";
        //echo"-------end class path-----------".$class_path."---------------------";
        //echo"<br/>";
        foreach ($element->childNodes as $subElement) {
            if ($subElement->nodeType == XML_TEXT_NODE) {
                $obj["html"] = $subElement->wholeText;
            }
            else {
                $obj["children"][] = WoobookingUtility::element_to_obj($subElement,$style,$class_path);
            }
        }
        return $obj;
    }
    public static function less_to_obj($style)
    {
        require_once WOOBOOKING_PATH_ROOT.DS.'libraries/less.php_1.7.0.10/less.php/Less.php';
        require_once WOOBOOKING_PATH_ROOT.DS.'libraries/CSS-Parser-master/parser.php';
        $less_parser = new Less_Parser();
        $less_parser->parse( $style );
        $css = $less_parser->getCss();
        $css_parser = new CssParser();
        $css_parser->load_string($css);
        $css_parser->parse();
        require_once WOOBOOKING_PATH_ROOT.DS.'libraries/softwaycore/html/style.php';
        $list_style=$css_parser->parsed['main'];
        $list_style=JArrayHelper::toObject($list_style);
        foreach($list_style as $key=>&$sub_list_style){
            $current_items=new stdClass();
            $list_function_apply=array();
            foreach($sub_list_style as $key1=>&$style){
                $a_key1=str_replace("-","_",$key1);
                if(method_exists ( "style" , $a_key1))
                {
                    $item_function=new stdClass();
                    $item_function->func=$a_key1;
                    $item_function->param1=$style;
                    $list_function_apply[]=$item_function;
                }
                $current_items->{$a_key1}=$style;
            }
            $sub_list_style=$current_items;
            foreach($list_function_apply as $item_func){
                call_user_func_array("style::$item_func->func", array(&$sub_list_style,$item_func->param1));
            }
            $key1 = preg_split('/\,+/', trim($key));
            if(count($key1)>1){
                foreach($key1 as $_key1){
                    $list_style->{trim($_key1)}=$sub_list_style;
                }
            }
        }
        return $list_style;
    }
    public static function get_os_layout()
    {
        $list_screen=array(
            xxxs=>200,
            xxs=>410,
            xs=>480,
            sm=>768,
            md=>992,
            lg=>1200
        );
        $user_os        = WoobookingUtility::getOS();
        $user_browser   = WoobookingUtility::getBrowser();
        if(
            $user_os=="iPhone"
            ||$user_os=="iPod"
            ||$user_os=="iPad"
            ||$user_os=="Android"
            ||$user_os=="BlackBerry"
            ||$user_os=="Mobile"
        ){
            return "android";
        }else{
            return "browser";
        }
    }
    /**
     * @return bool
     */
    public static function isMobileBrowser()
    {
        $list_screen=array(
            xxxs=>200,
            xxs=>410,
            xs=>480,
            sm=>768,
            md=>992,
            lg=>1200
        );
        $user_os        = WoobookingUtility::getOS();
        $user_browser   = WoobookingUtility::getBrowser();
        if(
            $user_os=="iPhone"
            ||$user_os=="iPod"
            ||$user_os=="iPad"
            ||$user_os=="Android"
            ||$user_os=="BlackBerry"
            ||$user_os=="Mobile"
        ){
            return true;
        }else{
            return false;
        }
        $device_details = "<strong>Browser: </strong>".$user_browser."<br /><strong>Operating System: </strong>".$user_os."";
    }
    function compress($source, $destination, $quality)
    {
        $info = getimagesize($source);
        if ($info['mime'] == 'image/jpeg')
            $image = imagecreatefromjpeg($source);
        elseif ($info['mime'] == 'image/gif')
            $image = imagecreatefromgif($source);
        elseif ($info['mime'] == 'image/png')
            $image = imagecreatefrompng($source);
        imagejpeg($image, $destination, $quality);
        return $destination;
    }
    public static function create_thumb($source, $width = 600, $height = 250)
    {
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/softwaycore/image-master/src/Image.php';
        $source_info = pathinfo($source);
        $image = new Image();
        $temp_image_path = "/tmp/" . $source_info['basename'];
        $image->loadFile($source);
        $image->crop($width, $height);
        $image->toFile(WOOBOOKING_PATH_ROOT . $temp_image_path);
        return $temp_image_path;
    }
    public static function resize_image($source, $width = 600, $height = 250)
    {
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/softwaycore/image-master/src/Image.php';
        $source_info = pathinfo($source);
        $image = new Image();
        $temp_image_path = "/tmp/" . $source_info['basename'];
        $image->loadFile($source);
        $image->resize($width, $height, true, Image::SCALE_FILL);
        $image->toFile(WOOBOOKING_PATH_ROOT . $temp_image_path);
        return $temp_image_path;
    }
    public static function createThumbs_image($source, $sizes = array('300x300', '64x64', '250x125'))
    {
        $source = WOOBOOKING_PATH_ROOT . DS . 'images/com_hikashop/upload/thumbnail_25x25/thoitrang_phukien-1336232718.png';
        require_once WOOBOOKING_PATH_ROOT . DS . 'libraries/softwaycore/image-master/src/Image.php';
        $source_info = pathinfo($source);
        $image = new Image();
        $temp_image_path = "/tmp/" . $source_info['basename'];
        $image->loadFile($source);
        $image->createThumbs($sizes, Image::SCALE_FILL);
        $image->toFile(WOOBOOKING_PATH_ROOT . $temp_image_path, IMAGETYPE_PNG, array('options' => 0));
        return $temp_image_path;
    }
    public static function get_debug()
    {
        $input=Factory::getApplication()->input;
        $debug=$input->getString('dg','');
        $session=Factory::getSession();
        if($debug=='0' || $debug=='1'){
            $session->set('dg',$debug);
        }
        $debug=$session->get('dg','');
        if($debug=='1')
        {
            return true;
        }else{
            return false;
        }
    }
    public  static function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    public  static function lazyLoadContent($class="") {
        ob_start();
        ?>
        <div class="animated-background <?php echo $class ?>  ">
            <div class="background-masker header-top"></div>
            <div class="background-masker header-left"></div>
            <div class="background-masker header-right"></div>
            <div class="background-masker header-bottom"></div>
            <div class="background-masker subheader-left"></div>
            <div class="background-masker subheader-right"></div>
            <div class="background-masker subheader-bottom"></div>
            <div class="background-masker content-top"></div>
            <div class="background-masker content-first-end"></div>
            <div class="background-masker content-second-line"></div>
            <div class="background-masker content-second-end"></div>
            <div class="background-masker content-third-line"></div>
            <div class="background-masker content-third-end"></div>
        </div>

        <?php
        $content=ob_get_clean();
        return $content;
    }

    public function get_class_icon_font()
    {
        
        $iconFiles = array(
            'templates/sprflat/assets/less/icons.less'
        );
        $content = '';
        foreach ($iconFiles as $file) {
            $content .= JFile::read(WOOBOOKING_PATH_ROOT . '/' . $file);
        }
        $icon_class = array();
        $requestString = '/(.*?).(\(|\'|)(.*?)(:before(.*?){)/';
        preg_match_all($requestString, $content, $icon_class);
        $icon_class = $icon_class[3];
        return $icon_class;
    }
    public static function getClassPathByName($className){
        $reflector = new ReflectionClass($className);
        return $reflector->getFileName();
    }
}
