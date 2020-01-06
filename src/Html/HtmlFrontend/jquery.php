<?php
/**
 * @package     WooBooking.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_WOO_BOOKING_EXEC') or die;

/**
 * Utility class for jQuery JavaScript behaviors
 *
 * @since  3.0
 */
abstract class HtmlFrontendJquery
{
    /**
     * @var    array  Array containing information for loaded files
     * @since  3.0
     */
    protected static $loaded = array();

    /**
     * Method to load the jQuery JavaScript framework into the document head
     *
     * If debugging mode is on an uncompressed version of jQuery is included for easier debugging.
     *
     * @param boolean $noConflict True to load jQuery in noConflict mode [optional]
     * @param mixed $debug Is debugging mode on? [optional]
     * @param boolean $migrate True to enable the jQuery Migrate plugin
     *
     * @return  void
     *
     * @since   3.0
     */
    public static function framework($noConflict = true, $debug = null, $migrate = true)
    {

        // Only load once
        if (!empty(static::$loaded[__METHOD__])) {
            $doc=Factory::getDocument();
            //$doc->addScript('resources/js/jquery-ui-1.12.1/external/jquery/jquery.js');

            return;

        }
        
        // If no debugging value is set, use the configuration setting
        if ($debug === null) {
            $debug = true;
        }
        //$doc=Factory::getDocument();

        static::$loaded[__METHOD__] = true;

        return;
    }
    public static function animated_modal($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addScript('resources/js/animatedModal.js/animatedModal.js');
            $doc->addStyleSheet('resources/js/animatedModal.js/css/animate.min.css');
            $doc->addStyleSheet('resources/js/animatedModal.js/css/normalize.min.css');
            static::$loaded[__METHOD__] = true;
        }
        return;
    }

    /**
     * Method to load the jQuery UI JavaScript framework into the document head
     *
     * If debugging mode is on an uncompressed version of jQuery UI is included for easier debugging.
     *
     * @param array $components The jQuery UI components to load [optional]
     * @param mixed $debug Is debugging mode on? [optional]
     *
     * @return  void
     *
     * @since   3.0
     */
    public static function ui(array $components = array('core'), $debug = null)
    {


        // Set an array containing the supported jQuery UI components handled by this method
        $supported = array('core', 'sortable');

        // Include jQuery
        static::framework();

        // If no debugging value is set, use the configuration setting
        if ($debug === null) {
            $debug = WBDEBUG;
        }

        $doc=Factory::getDocument();
        $doc->addScript('admin/resources/js/jquery-ui-1.11.4/jquery-ui.js');
        $doc->addStyleSheet('admin/resources/js/jquery-ui-1.11.4/jquery-ui.css');

        return;
    }
    public static function slider_pro_master($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addStyleSheet('resources/js/slider-pro-master/dist/css/slider-pro.css');
            $doc->addScript('resources/js/slider-pro-master/dist/js/jquery.sliderPro.js');
            static::$loaded[__METHOD__] = true;
        }
        return;
    }
    public static function loading_js($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addStyleSheet('resources/js/Fullscreen-Loading-Modal-Indicator/css/jquery.loadingModal.css');
            $doc->addScript('resources/js/Fullscreen-Loading-Modal-Indicator/js/jquery.loadingModal.js');
            static::$loaded[__METHOD__] = true;
        }
        return;
    }
    public static function twbs_pagination($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addScript('resources/js/twbs-pagination/jquery.twbsPagination.js');
            static::$loaded[__METHOD__] = true;
        }
        return;
    }
    public static function validate($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addScript('resources/js/jquery-validation/dist/jquery.validate.min.js');
            static::$loaded[__METHOD__] = true;
        }
        return;
    }

    /**
     * Auto set CSRF token to ajaxSetup so all jQuery ajax call will contains CSRF token.
     *
     * @param string $name The CSRF meta tag name.
     *
     * @return  void
     *
     * @throws  \InvalidArgumentException
     *
     * @since   3.8.0
     */
    public static function token($name = 'csrf.token')
    {
        // Only load once
        if (!empty(static::$loaded[__METHOD__][$name])) {
            return;
        }

        static::framework();
        HtmlFrontend::_('form.csrf', $name);

        $doc = Factory::getDocument();

        $doc->addScriptDeclaration(
            <<<JS
;(function ($) {
	$.ajaxSetup({
		headers: {
			'X-CSRF-Token': WooBooking.getOptions('$name')
		}
	});
})(jQuery);
JS
        );

        static::$loaded[__METHOD__][$name] = true;
    }

    /*
     * HtmlFrontend::_('jquery.framework');
        HtmlFrontend::_('jquery.confirm');
     */
    public static function confirm($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addScript('resources/js/jquery-confirm-master/dist/jquery-confirm.min.js');
            $doc->addStyleSheet('resources/js/jquery-confirm-master/dist/jquery-confirm.min.css');
            static::$loaded[__METHOD__] = true;
        }
        return;
    }
    public static function base64($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addScript('admin/resources/js/base64-js/jquery.base64.js');
            static::$loaded[__METHOD__] = true;
        }
        return;
    }
    public static function serialize_object($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addScript('resources/js/form-serializeObject/jquery.serializeObject.min.js');
            static::$loaded[__METHOD__] = true;
        }
        return;
    }
    public static function daterangepicker($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addStyleSheet('resources/js/daterangepicker-master/daterangepicker.css');
            $doc->addScript('resources/js/daterangepicker-master/daterangepicker.js');
            static::$loaded[__METHOD__] = true;
        }
        return;
    }

    public static function fontawesome($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addStyleSheet('resources/js/fontawesome-free-5.11.2/css/all.min.css');
            static::$loaded[__METHOD__] = true;
        }
        return;
    }
    public static function icheck($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addStyleSheet('admin/resources/js/icheck-1.x/skins/all.css');
            $doc->addScript('admin/resources/js/icheck-1.x/icheck.min.js');
            static::$loaded[__METHOD__] = true;
        }
        return;
    }
    public static function popper($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addScript('admin/resources/js/popper/popper.min.js');
            static::$loaded[__METHOD__] = true;
        }
        return;
    }
    public static function less($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addScript('resources/js/less/less.min.js');
            static::$loaded[__METHOD__] = true;
        }
        return;
    }
    public static function bootstrap($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addStyleSheet('resources/js/bootstrap-3.3.7-dist/css/bootstrap.css');
            $doc->addScript('resources/js/bootstrap-3.3.7-dist/js/bootstrap.js');
            static::$loaded[__METHOD__] = true;
        }
        return;
    }
    public static function tooltip($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addScript('admin/resources/js/tooltip/tooltip.min.js');
            static::$loaded[__METHOD__] = true;
        }
        return;
    }
    public static function fullcalendar($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addStyleSheet('admin/resources/js/fullcalendar/core/main.min.css');
            $doc->addStyleSheet('admin/resources/js/fullcalendar/daygrid/main.min.css');
            $doc->addStyleSheet('admin/resources/js/fullcalendar/timegrid/main.min.css');
            $doc->addStyleSheet('admin/resources/js/fullcalendar/bootstrap/main.min.css');
            $doc->addScript('admin/resources/js/fullcalendar/core/main.min.js');
            $doc->addScript('admin/resources/js/fullcalendar/daygrid/main.min.js');
            $doc->addScript('admin/resources/js/fullcalendar/timegrid/main.min.js');
            $doc->addScript('admin/resources/js/fullcalendar/bootstrap/main.min.js');
            $doc->addScript('admin/resources/js/fullcalendar/interaction/main.min.js');
            $doc->addScript('admin/resources/js/fullcalendar/moment/main.min.js');
            static::$loaded[__METHOD__] = true;
        }
        return;
    }
    /*
     * HtmlFrontend::_('jquery.framework');
        HtmlFrontend::_('jquery.spidochetube');
     */
    public static function spidochetube($debug = null)
    {
        // Include jQuery
        static::framework();
        static::fullscreen();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addScript('media/system/js/spidocheTube-master/jquery.spidochetube.js');
            $doc->addStyleSheet('media/system/js/spidocheTube-master/demo/minimal/css/minimal.css');
            $attribs = array('type' => 'text/css');
            $doc->addHeadLink(JRoute::_('media/system/js/spidocheTube-master/style.less'), 'stylesheet/less', 'rel', $attribs);


            static::$loaded[__METHOD__] = true;
        }
        return;
    }
    /*
     * HtmlFrontend::_('jquery.framework');
        HtmlFrontend::_('jquery.youtube_video');
     */
    public static function select2($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addScript('admin/resources/js/select2-4.0.12/dist/js/select2.full.min.js');
            $doc->addStyleSheet('admin/resources/js/select2-4.0.12/dist/css/select2.min.css');


            static::$loaded[__METHOD__] = true;
        }
        return;
    }
    public static function moment($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addScript('resources/js/moment/moment.js');


            static::$loaded[__METHOD__] = true;
        }
        return;
    }
    public static function increment($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addScript('resources/js/jquery-Increment/dist/js/handleCounter.js');
            $doc->addStyleSheet('resources/js/jquery-Increment/dist/css/handle-counter.min.css');


            static::$loaded[__METHOD__] = true;
        }
        return;
    }
    public static function select_yes_no($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addScript('admin/resources/js/Toggle-Switches/lc_switch.js');
            $doc->addStyleSheet('admin/resources/js/Toggle-Switches/lc_switch.css');


            static::$loaded[__METHOD__] = true;
        }
        return;
    }
    public static function fancybox($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addScript('resources/js/fancybox/jquery.fancybox-1.3.4.pack.js');
            $doc->addScript('resources/js/fancybox/jquery.easing-1.3.pack.js');
            $doc->addScript('resources/js/fancybox/jquery.mousewheel-3.0.4.pack.js');
            $doc->addStyleSheet('resources/js/fancybox/jquery.fancybox-1.3.4.css');


            static::$loaded[__METHOD__] = true;
        }
        return;
    }
    public static function auto_numeric($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addScript('admin/resources/js/autoNumeric/autoNumeric.js');


            static::$loaded[__METHOD__] = true;
        }
        return;
    }
    public static function scroll_to($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addScript('admin/resources/js/jquery.scrollTo-2.1.2/jquery.scrollTo.min.js');


            static::$loaded[__METHOD__] = true;
        }
        return;
    }
    /*
     * HtmlFrontend::_('jquery.framework');
        HtmlFrontend::_('jquery.blink');
     */
    public static function blink($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addScript('media/system/js/jquery-blink-master/jquery.blink.js');


            static::$loaded[__METHOD__] = true;
        }
        return;
    }
    /*
     * HtmlFrontend::_('jquery.framework');
        HtmlFrontend::_('jquery.fullscreen');
     */
    public static function fullscreen($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addScript('media/system/js/Fullscreen-Plugin-jQuery/release/jquery.fullscreen.js');
            static::$loaded[__METHOD__] = true;
        }
        return;
    }
    /*
     * HtmlFrontend::_('jquery.framework');
        HtmlFrontend::_('jquery.elite_video_player');

    	<link rel="stylesheet" href="css/elite.css" type="text/css" media="screen"/>
	<link rel="stylesheet" href="css/elite-font-awesome.css" type="text/css">
	<link rel="stylesheet" href="css/jquery.mCustomScrollbar.css" type="text/css">
	<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
	<script src="js/froogaloop.js" type="text/javascript"></script>
	<script src="js/jquery.mCustomScrollbar.js" type="text/javascript"></script>
	<script src="js/THREEx.FullScreen.js"></script>
	<script src="js/videoPlayer.js" type="text/javascript"></script>
	<script src="js/Playlist.js" type="text/javascript"></script>


     */
    public static function elite_video_player($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addScript('https://cdn.jsdelivr.net/npm/hls.js@latest');
            $doc->addScript('media/system/js/elite-video-player/source/js/froogaloop.js');
            $doc->addScript('media/system/js/elite-video-player/source/js/jquery.mCustomScrollbar.js');
            $doc->addScript('media/system/js/elite-video-player/source/js/THREEx.FullScreen.js');
            $doc->addScript('media/system/js/elite-video-player/source/js/videoPlayer.js');
            $doc->addScript('media/system/js/elite-video-player/source/js/Playlist.js');

            $doc->addStyleSheet('media/system/js/elite-video-player/source/css/elite.css');
            $doc->addStyleSheet('media/system/js/elite-video-player/source/css/elite-font-awesome.css');
            $doc->addStyleSheet('media/system/js/elite-video-player/source/css/jquery.mCustomScrollbar.css');
            static::$loaded[__METHOD__] = true;
        }
        return;
    }

    /*
         * HtmlFrontend::_('jquery.framework');
            HtmlFrontend::_('jquery.waitingfor');
         */
    public static function waitingfor($debug = null)
    {
        // Include jQuery
        static::framework();
        // If no debugging value is set, use the configuration setting
        // Only attempt to load the component if it's supported in core and hasn't already been loaded
        if (empty(static::$loaded[__METHOD__])) {
            $doc = Factory::getDocument();
            $doc->addScript('media/system/js/Bootstrap-Loading-Modal-With-Progress-Bar-waitingFor/src/waitingfor.js');
            static::$loaded[__METHOD__] = true;
        }
        return;
    }

}
