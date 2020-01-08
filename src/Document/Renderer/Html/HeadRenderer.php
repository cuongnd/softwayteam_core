<?php
/**
 * SoftWay! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace SoftWay\CMS\Document\Renderer\Html;
defined('_SOFT_WAY_EXEC') or die;

use Factory;
use SoftWay\CMS\Document\DocumentRenderer;
/**
 * HTML document renderer for the document `<head>` element
 *
 * @since  3.5
 */

class HeadRenderer extends DocumentRenderer
{
    /**
     * Renders the document head and returns the results as a string
     *
     * @param string $head (unused)
     * @param array $params Associative array of values
     * @param string $content The script
     *
     * @return  string  The output of the script
     *
     * @since   3.5
     */
    public function render($head, $params = array(), $content = null)
    {
        return $this->fetchHead($this->_doc);
    }

    /**
     * Generates the head HTML and return the results as a string
     *
     * @param DocumentHtml $document The document for which the head will be created
     *
     * @return  string  The head hTML
     *
     * @since   3.5
     * @deprecated  4.0  Method code will be moved into the render method
     */
    public function fetchHead($document)
    {
        $openSource=Factory::getOpenSource();

        if($openSource->is_rest_api()){
            return ;
        }
        // Convert the tagids to titles
        if (isset($document->_metaTags['name']['tags'])) {
            $tagsHelper = new TagsHelper;
            $document->_metaTags['name']['tags'] = implode(', ', $tagsHelper->getTagNames($document->_metaTags['name']['tags']));
        }

        if ($document->getScriptOptions()) {
            \Html::_('behavior.core');
        }

        // Trigger the onBeforeCompileHead event

        // Get line endings
        $lnEnd = $document->_getLineEnd();
        $tab = $document->_getTab();
        $tagEnd = ' />';
        $buffer = '';
        $mediaVersion = $document->getMediaVersion();


        // Generate base tag (need to happen early)
        $base = $document->getBase();

        if (!empty($base)) {
            $buffer .= $tab . '<base href="' . $base . '" />' . $lnEnd;
        }


        // Generate META tags (needs to happen as early as possible in the head)
        foreach ($document->_metaTags as $type => $tag) {
            foreach ($tag as $name => $content) {
                if ($type == 'http-equiv' && !($document->isHtml5() && $name == 'content-type')) {
                    $buffer .= $tab . '<meta http-equiv="' . $name . '" content="' . htmlspecialchars($content, ENT_COMPAT, 'UTF-8') . '" />' . $lnEnd;
                } elseif ($type != 'http-equiv' && !empty($content)) {
                    if (is_array($content)) {
                        foreach ($content as $value) {
                            $buffer .= $tab . '<meta ' . $type . '="' . $name . '" content="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '" />' . $lnEnd;
                        }
                    } else {
                        $buffer .= $tab . '<meta ' . $type . '="' . $name . '" content="' . htmlspecialchars($content, ENT_COMPAT, 'UTF-8') . '" />' . $lnEnd;
                    }
                }
            }
        }

        // Don't add empty descriptions
        $documentDescription = $document->getDescription();

        if ($documentDescription) {
            $buffer .= $tab . '<meta name="description" content="' . htmlspecialchars($documentDescription, ENT_COMPAT, 'UTF-8') . '" />' . $lnEnd;
        }

        // Don't add empty generators
        $generator = $document->getGenerator();

        if ($generator) {
            $buffer .= $tab . '<meta name="generator" content="' . htmlspecialchars($generator, ENT_COMPAT, 'UTF-8') . '" />' . $lnEnd;
        }

        $buffer .= $tab . '<title>' . htmlspecialchars($document->getTitle(), ENT_COMPAT, 'UTF-8') . '</title>' . $lnEnd;


        $defaultCssMimes = array('text/css');

        // Generate stylesheet links





        foreach ($document->_lessStyleSheets as $src => $attribs) {

            $random = random_int(100000, 900000);
            ob_start();
            ?>
            <link rel="stylesheet/less" type="text/css" href="<?php echo plugins_url() . "/".SW_PLUGIN_NAME."/" . $src ?>"/>
            <?php
            echo ob_get_clean();
        }
        foreach ($document->_styleSheets as $src => $attribs) {
            $random = random_int(100000, 900000);
            wp_enqueue_style('softwaycore-css-' . $random, plugins_url() . '/'.SW_PLUGIN_NAME.'/' . $src);
        }
        // Generate stylesheet declarations
        foreach ($document->_style as $type => $content) {
            $buffer .= $tab . '<style';

            if (!is_null($type) && (!$document->isHtml5() || !in_array($type, $defaultCssMimes))) {
                $buffer .= ' type="' . $type . '"';
            }

            $buffer .= '>' . $lnEnd;

            // This is for full XHTML support.
            if ($document->_mime != 'text/html') {
                $buffer .= $tab . $tab . '/*<![CDATA[*/' . $lnEnd;
            }

            $buffer .= $content . $lnEnd;

            // See above note
            if ($document->_mime != 'text/html') {
                $buffer .= $tab . $tab . '/*]]>*/' . $lnEnd;
            }

            $buffer .= $tab . '</style>' . $lnEnd;
        }

        // Generate scripts options
        $scriptOptions = $document->getScriptOptions();

        if (!empty($scriptOptions)) {
            $buffer .= $tab . '<script type="application/json" class="SoftWay-script-options new">';

            $prettyPrint = (WBDEBUG && defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : false);
            $jsonOptions = json_encode($scriptOptions, $prettyPrint);
            $jsonOptions = $jsonOptions ? $jsonOptions : '{}';

            $buffer .= $jsonOptions;
            $buffer .= '</script>' . $lnEnd;
        }

        $defaultJsMimes = array('text/javascript', 'application/javascript', 'text/x-javascript', 'application/x-javascript');
        $html5NoValueAttributes = array('defer', 'async');
        // Generate script file links
        $openSource->add_script_footer($document->_scripts);
        $openSource->add_script_content_footer($document->_script);



        //add_action('wp_footer', array($this, 'wpb_hook_javascript'));


        // Generate script declarations
        foreach ($document->_script as $type => $content) {

            //add_action('wp_head', array($this,'test_funtion'));
        }


        return ltrim($buffer, $tab);
    }

}
