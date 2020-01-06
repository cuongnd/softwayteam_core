<?php

use WooBooking\CMS\Version;
use WooBooking\CMS\WbDate\WbDate;
use WooBooking\CMS\FOFInput\FOFInput;
use WooBooking\CMS\Registry\Registry;
use WooBooking\CMS\Document\Document;
use WooBooking\CMS\Application\Application;
use WooBooking\CMS\Uri\NBUri;
use WooBooking\CMS\Application\NBAppHelper;
use WooBooking\CMS\OpenSource\WooBookingOnOpenSource;
use WooBooking\CMS\Session\Session;
use WooBooking\CMS\Database\driver\DatabaseDriverMysqli;
class Factory
{
    private static $database;
    public static $language;
    private static $application;
    private static $input;
    private static $document;
    private static $open_source=null;
    private static $dates=array();
    private static $config;
    public static $root_url;
    public static $root_url_plugin;

    /**
     * Global session object
     *
     * @var    Session
     * @since  1.7.0
     */
    public static $session = null;

    public static function getDBO(){
        if (!self::$database) {
            self::$database = self::createDbo();
        }
        return self::$database;
    }
    public static function getUser(){
        $session=self::getSession();
        return $session->get('user');


    }
    /**
     * Return the {@link Date} object
     *
     * @param   mixed  $time      The initial time for the JDate object
     * @param   mixed  $tzOffset  The timezone offset.
     *
     * @return  Date object
     *
     * @see     Date
     * @since   1.7.0
     */
    public static function getDate($time = 'now', $tzOffset = null)
    {
        if (!isset(self::$dates[$time]))
        {
            self::$dates[$time] = new WbDate($time, $tzOffset);
        }

        $date = clone self::$dates[$time];
        return $date;
    }
    public static function getOpenSource(){
        if (!isset(self::$open_source))
        {

            self::$open_source = WooBookingOnOpenSource::getInstance();
        }
        return self::$open_source;
    }
    protected static function createDbo()
    {
        $host = DB_HOST;
        $user = DB_USER;
        $password = DB_PASSWORD;
        $database = DB_NAME;
        $prefix = "";
        $driver = "mysqli";
        $debug = true;
        $options = array('driver' => $driver, 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'prefix' => $prefix);


        try {
            $db = DatabaseDriverMysqli::getInstance($options);
        } catch (RuntimeException $e) {
            if (!headers_sent()) {
                header('HTTP/1.1 500 Internal Server Error');
            }
            exit('Database Error: ' . $e->getMessage());
        }

        $db->setDebug($debug);
        return $db;
    }
    public static function getLanguage()
    {
        if (!self::$language) {
            self::$language = self::createLanguage();
        }
        return self::$language;
    }
    public static function getUri()
    {
        $uri=NBUri::getInstance();
        return $uri;
    }

    /**
     * @return Document
     */
    public static function getDocument():Document
    {
        if (!self::$document)
        {
            self::$document = self::createDocument();
        }

        return self::$document;
    }

    /**
     * @return Document
     */
    protected static function createDocument()
    {

        $input = self::getApplication()->input;
        $type = $input->get('format', 'html', 'cmd');


        $attributes = array(
            'charset'      => 'utf-8',
            'lineend'      => 'unix',
            'tab'          => "\t",

        );

        return Document::getInstance($type, $attributes);
    }


    public static function getApplication($client="")
    {

        if (!self::$application) {

            self::$application =Application::getInstance($client);
        }

        return self::$application;
    }
    public static function setRootUrl($root_url){
        self::$root_url=$root_url;
    }
    public static function setRootUrlPlugin($root_url_plugin){
        self::$root_url_plugin=$root_url_plugin;
    }
    public static function getRootUrlPlugin(){
        return self::$root_url_plugin;
    }
    /**
     * Get a session object.
     *
     * Returns the global {@link Session} object, only creating it if it doesn't already exist.
     *
     * @param   array  $options  An array containing session options
     *
     * @return  Session object
     *
     * @see     Session
     * @since   1.7.0
     */
    public static function getSession(array $options = array())
    {
        if (!self::$session)
        {
            self::$session = self::createSession($options);
        }

        return self::$session;
    }
    /**
     * Create a session object
     *
     * @param   array  $options  An array containing session options
     *
     * @return  Session object
     *
     * @since   1.7.0
     */
    protected static function createSession(array $options = array())
    {
        // Get the Joomla configuration settings
        $conf    = self::getConfig();
        $handler = $conf->get('session_handler', 'none');

        // Config time is in minutes
        $options['expire'] = ($conf->get('lifetime')) ? $conf->get('lifetime') * 60 : 900;
        $session = Session::getInstance($handler, $options);
        return $session;
    }

    public static function getRootUrl(){
        return self::$root_url;
    }
    public static function getInput()
    {
        if (!self::$input) {
            self::$input = new FOFInput();
        }
        return self::$input;
    }
    public static function getConfig($file = null, $type = 'PHP', $namespace = '')
    {

        if (!self::$config)
        {
            if ($file === null)
            {
                $file = WOOBOOKING_PATH_ROOT . '/nb_config.php';
            }

            self::$config = self::createConfig($file, $type, $namespace);
        }

        return self::$config;
    }
    protected static function createConfig($file, $type = 'PHP', $namespace = '')
    {
        if (is_file($file))
        {
            include_once $file;
        }

        // Create the registry with a default namespace of config
        $registry = new Registry;

        // Sanitize the namespace.
        $namespace = ucfirst((string) preg_replace('/[^A-Z_]/i', '', $namespace));

        // Build the config name.
        $name = 'nb_config' . $namespace;

        // Handle the PHP configuration type.
        if ($type == 'PHP' && class_exists($name))
        {
            // Create the JConfig object
            $config = new $name;

            // Load the configuration values into the registry
            $registry->loadObject($config);
        }

        return $registry;
    }

    protected static function createLanguage()
    {
        $conf = self::getConfig();

        $locale = $conf->get('language');
        $debug = $conf->get('debug_lang');
        $lang = Language::getInstance($locale, $debug);
    }

    public static function getAppConfig()
    {
        return NBAppHelper::getConfig();
    }

}
