<?php
namespace WooBooking\CMS\Application;



use WooBooking\CMS\FOFInput\FOFInput;
use WooBooking\CMS\Registry\Registry;
use nb_config;
use Factory;
use WooBooking\CMS\Log\Log;
use Language;
use WooBooking\CMS\Object\CMSObject;



use WooBooking\CMS\OpenSource\WordPress\WooBookingOnWordpress;
use WooBooking\CMS\Router\Router;

class ApplicationSite extends Application
{
    public static $instance=null;
    public  $language=null;
    public  $config=null;
    public  $app_open_source=null;
    protected  $_client;
    /**
     * The application message queue.
     *
     * @var    array
     * @since  3.2
     * @deprecated  4.0  Will be renamed $messageQueue
     */
    protected $_messageQueue = array();
    /**
     * @var FOFInput
     */
    public  $input;
    public $logger;

    public function getName(){
        return $this->_client;
    }
    public function getRouter(){
        if (!isset($name))
        {
            $app = Factory::getApplication();
            $name = $app->getName();
        }

        $options['mode'] = Factory::getConfig()->get('sef');

        try
        {
            $router = Router::getInstance($name, $options);
        }
        catch (\Exception $e)
        {
            return;
        }

        return $router;
    }
    protected function setClient($client){
            $this->_client=$client=="site"?0:1;
    }
    public function getClient(){
        return $this->_client;
    }
    public function isClient($type){
        if($type=="admin"){
            return 1;
        }else{
            return 0;
        }
    }


    public function redirect($url){
        $root_url=Factory::getRootUrl();
        $html = '<html><head>';
        $html .= '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
        $html .= '<script>document.location.href=' . json_encode(str_replace("'", '&apos;', $root_url.'/'.$url)) . ';</script>';
        $html .= '</head><body></body></html>';
        echo $html;
    }


    /**
     * Sets the configuration for the application.
     *
     * @param   Registry  $config  A registry object holding the configuration.
     *
     * @return  AbstractApplication  Returns itself to support chaining.
     *
     * @since   1.0
     */
    public function setConfiguration(Registry $config)
    {
        $this->config = $config;

        return $this;
    }
    /**
     * Gets a user state.
     *
     * @param   string  $key      The path of the state.
     * @param   mixed   $default  Optional default value, returned if the internal value is null.
     *
     * @return  mixed  The user state or null.
     *
     * @since   3.2
     */
    public function getUserState($key, $default = null)
    {
        $session = Factory::getSession();
        $registry = $session->get('registry');

        if ($registry !== null)
        {
            return $registry->get($key, $default);
        }

        return $default;
    }
    /**
     * Sets the value of a user state variable.
     *
     * @param   string  $key    The path of the state.
     * @param   mixed   $value  The value of the variable.
     *
     * @return  mixed  The previous state, if one existed.
     *
     * @since   3.2
     */
    public function setUserState($key, $value)
    {
        $session = Factory::getSession();
        $registry = $session->get('registry');

        if ($registry !== null)
        {
            return $registry->set($key, $value);
        }

        return;
    }

    /**
     * Gets the value of a user state variable.
     *
     * @param   string  $key      The key of the user state variable.
     * @param   string  $request  The name of the variable passed in a request.
     * @param   string  $default  The default value for the variable if not found. Optional.
     * @param   string  $type     Filter for the variable, for valid values see {@link \JFilterInput::clean()}. Optional.
     *
     * @return  mixed  The request user state.
     *
     * @since   3.2
     */
    public function getUserStateFromRequest($key, $request, $default = null, $type = 'none')
    {
        $cur_state = $this->getUserState($key, $default);
        $new_state = $this->input->get($request, null, $type);

        if ($new_state === null)
        {
            return $cur_state;
        }

        // Save the new value only if it was set in this request.
        $this->setUserState($key, $new_state);

        return $new_state;
    }

    public function enqueueMessage($msg, $type = 'message')
    {
        // Don't add empty messages.
        if (trim($msg) === '')
        {
            return;
        }

        // For empty queue, if messages exists in the session, enqueue them first.
        $messages = $this->getMessageQueue();

        $message = array('message' => $msg, 'type' => strtolower($type));

        if (!in_array($message, $this->_messageQueue))
        {
            // Enqueue the message.
            $this->_messageQueue[] = $message;
        }
    }
    /**
     * Get the system message queue.
     *
     * @param   boolean  $clear  Clear the messages currently attached to the application object
     *
     * @return  array  The system message queue.
     *
     * @since   3.2
     */
    public function getMessageQueue($clear = false)
    {
        // For empty queue, if messages exists in the session, enqueue them.
        if (!$this->_messageQueue)
        {
            $session = Factory::getSession();
            $sessionQueue = $session->get('application.queue', array());

            if ($sessionQueue)
            {
                $this->_messageQueue = $sessionQueue;
                $session->set('application.queue', array());
            }
        }

        $messageQueue = $this->_messageQueue;

        if ($clear)
        {
            $this->_messageQueue = array();
        }

        return $messageQueue;
    }


    /**
     * Initialise the application.
     *
     * @param   array  $options  An optional associative array of configuration settings.
     *
     * @return  void
     *
     * @since   3.2
     */
    protected function initialiseApp($options = array())
    {
        
        require_once WOOBOOKING_PATH_ROOT."/nb_config.php";
        $config= new nb_config();
        
        $register=new Registry();
        $register->loadObject($config);
        $this->setConfiguration($register);


        // Check that we were given a language in the array (since by default may be blank).
        if (isset($options['language']))
        {
            $this->set('language', $options['language']);
        }

        // Build our language object

        $lang = Language::getInstance($this->get('language'), $this->get('debug_lang'));

        // Load the language to the API
        $this->loadLanguage($lang);


        // Register the language object with \Factory
        Factory::$language = $this->getLanguage();

        Log::addLogger(array('text_file' => 'logs/everything.php'), Log::ALL, array('deprecated', 'databasequery'), false);




    }
    public function getLanguage()
    {
        return $this->language;
    }
    /*
    * The logic and options for creating this object are adequately generic for default cases
    * but for many applications it will make sense to override this method and create a language,
    * if required, based on more specific needs.
    *
    * @param   \JLanguage  $language  An optional language object. If omitted, the factory language is created.
    *
    * @return  WebApplication This method is chainable.
    *
    * @since   1.7.3
    */
    public function loadLanguage(Language $language = null)
    {
        $this->language = ($language === null) ? Factory::getLanguage() : $language;

        return $this;
    }

    public function execute(){
        $this->app_open_source=Factory::getOpenSource();
    }
    public function __construct()
    {
        
        $this->input =  new FOFInput;
        $this->initialiseApp();
    }


    public function get($key, $default = null)
    {
        return $this->config->get($key, $default);
    }
    public function set($key, $value = null)
    {
        $previous = $this->config->get($key);
        $this->config->set($key, $value);

        return $previous;
    }


}